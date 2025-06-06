<?php

namespace App\Http\Controllers;

use App\Exceptions\MissingIssueFormDataException;
use App\Mail\TechnicianAssignmentEmail;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Issue;
use App\Models\IssueAttachment;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\MaintenanceStaff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use App\Models\HistoryLog;

class IssueController extends Controller
{


   //this is called when the user  clicks on create or report an issue
    public function create()
    {
        // Fetch locations to populate a dropdown in the form
        $locations = Location::all();
        // Retrieve session data if it exists (for going back to the form)
        $formData = session()->get('formData', []);
        $attachments = session()->get('attachments', []);

        return view('Student.createissue', compact('locations', 'formData', 'attachments'));
    }


// this is for storing the issue
    public function store(Request $request)
    {
        $this->processIssueForm($request);

        return redirect()->route('Student.confirmissue');
    }

    private function processIssueForm(Request $request)
    {
        // Validate the form data
        /*
         *
         */
        $validated = $request->validate([
            'location_id' => 'required|integer',
            'issue_type' => 'required|string|max:50',
            'issue_description' => 'required|string',
            'safety_hazard' => 'required|boolean',
            'affected_areas' => 'required|integer|min:1',
            'pc_number' => 'nullable|required_if:issue_type,PC|integer|min:1|max:100',
            'pc_issue_type' => 'nullable|required_if:issue_type,PC|string|max:50',
            'critical_work_affected' => 'nullable|boolean',
            'affects_operations' => 'required|boolean', // Added affects_operations validation
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,pdf,doc,docx|max:2048',
        ]);


        $validated['safety_hazard'] = filter_var($validated['safety_hazard'], FILTER_VALIDATE_BOOLEAN);
        $validated['critical_work_affected'] = filter_var(
            $validated['critical_work_affected'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
        $validated['affects_operations'] = filter_var($validated['affects_operations'], FILTER_VALIDATE_BOOLEAN);


        // Fetch the authenticated user's ID
        $reporterId = auth()->id();

        // Fetch the selected location details
        $location = Location::find($validated['location_id']);

        // Prepare form data for session
        $formData = [
            'location_id' => $validated['location_id'],
            'issue_type' => $validated['issue_type'],
            'issue_description' => $validated['issue_description'],
            'safety_hazard' => $validated['safety_hazard'],
            'affected_areas' => $validated['affected_areas'],
            'reporter_id' => $reporterId,
            'building' => $location->building_name,
            'floor' => $location->floor_number,
            'room' => $location->room_number,
            'affects_operations' => $validated['affects_operations'],
            'urgency_level' => $this->determineUrgencyLevel($this->calculateUrgencyScore($validated)),
            'urgency_score' => $this->calculateUrgencyScore($validated)
        ];

        // Add PC-specific fields if present
        if ($validated['issue_type'] === 'PC') {
            $formData['pc_number'] = $validated['pc_number'];
            $formData['pc_issue_type'] = $validated['pc_issue_type'];
            $formData['critical_work_affected'] = $validated['critical_work_affected'];
        }

        // Store the form data in session
        session()->put('formData', $formData);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('issues/attachments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize()
                ];
            }
            session()->put('attachments', $attachments);
        }
    }

    private function calculateUrgencyScore($data)
    {
        $urgencyScore = 0;

        // 1. Issue Type Scoring
        $typeScores = [
            'Electrical' => 3,
            'Structural' => 3,
            'Plumbing' => 2,
            'HVAC' => 2,
            'PC' => 1,
            'Furniture' => 1,
            'General' => 1
        ];
        $urgencyScore += $typeScores[$data['issue_type']] ?? 1;

        // 2. Safety Hazard
        if ($data['safety_hazard']) {
            $urgencyScore += 3;
        }

        // 3. Affected Areas
        if ($data['affected_areas'] > 3) {
            $urgencyScore += 2;
        } elseif ($data['affected_areas'] > 1) {
            $urgencyScore += 1;
        }

        // 4. PC-specific factors
        if ($data['issue_type'] === 'PC') {
            if ($data['critical_work_affected']) {
                $urgencyScore += 2;
            }
            if ($data['pc_issue_type'] === 'hardware') {
                $urgencyScore += 1;
            }
        }

        return $urgencyScore;
    }

    private function determineUrgencyLevel($urgencyScore)
    {
        if ($urgencyScore >= 5) {
            return 'High';
        } elseif ($urgencyScore >= 3) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }

    // In your controller where the issue is created/processed
    public function success()
    {
        return view('Student.success');
    }

    public function confirm()
    {
        // Retrieve session data
        $formData = session()->get('formData', []);
        $attachments = session()->get('attachments', []);

        if (empty($formData) || empty($attachments)) {
            throw new MissingIssueFormDataException('Form data or attachments are missing. Please return to the Create Issue page and fill out the form.');
        }


        // Fetch location details for display
        $location = Location::find($formData['location_id']);

        // Get reporter details
        $reporter = User::find($formData['reporter_id']);

        return view('Student.confirmissue', compact('formData', 'attachments', 'location', 'reporter'));
    }
/*
 * update the created_at here also
 */

    public function save()
    {
        // Retrieve session data
        $formData = session()->get('formData', []);
        $attachments = session()->get('attachments', []);

        // If no form data exists, redirect back to the form
        if (empty($formData)) {
            return redirect()->route('Student.createissue')->with('error', 'Please fill out the form first.');
        }

        // Check for duplicate issue within last 24 hours
        $existingIssue = Issue::where('reporter_id', $formData['reporter_id'])
            ->where('issue_type', $formData['issue_type'])
            ->whereRaw('LOWER(issue_description) = ?', [strtolower($formData['issue_description'])])
            ->where('created_at', '>', now()->subDay())
            ->first();

        if ($existingIssue) {
            return redirect()->route('Student.createissue')->with([
                'error' => 'You already submitted this issue within the last 24 hours. ' .
                    'Existing Issue ID: #' . $existingIssue->issue_id,
                'existing_issue_id' => $existingIssue->issue_id
            ]);
        }

        try {
            DB::beginTransaction();

            // Save the issue to the database with all fields
            $issue = Issue::create([
                'reporter_id' => $formData['reporter_id'],
                'location_id' => $formData['location_id'],
                'issue_type' => $formData['issue_type'],
                'issue_description' => $formData['issue_description'],
                'urgency_level' => $formData['urgency_level'],
                'urgency_score' => $formData['urgency_score'] ?? null, // Add score if available
                'safety_hazard' => $formData['safety_hazard'] ?? false,
                'affects_operations'=>$formData['affects_operations'] ?? false,
                'affected_areas' => $formData['affected_areas'] ?? 1,
                'pc_number' => $formData['pc_number'] ?? null,
                'pc_issue_type' => $formData['pc_issue_type'] ?? null,
                'critical_work_affected' => $formData['critical_work_affected'] ?? false,
                'issue_status' => 'Open',
                'created_at' => now()
            ]);
Log::error("$issue->issue_id");
            session()->put('reported_issue_id', $issue->issue_id);

            // Create the task
            $task = Task::create([
                'issue_id' => $issue->issue_id,
                'priority' => $this->mapUrgencyToPriority($formData['urgency_level']),
                'issue_status' => 'Pending',
                'expected_completion' => Carbon::now()->addDays(3),
                'created_at' => now()
            ]);

            $assignedTechnician = $this->assignOrQueueTask($task);

            // Save attachments
            foreach ($attachments as $attachment) {
                IssueAttachment::create([
                    'issue_id' => $issue->issue_id,
                    'file_path' => $attachment['path'],
                    'original_name' => $attachment['original_name'],
                    'mime_type' => $attachment['mime_type'],
                    'file_size' => $attachment['size'],
                    'storage_disk' => 'public',
                ]);
            }

            // Get related data

            $reporter = User::find($formData['reporter_id']);

            // Send notifications
            $this->sendReporterNotification($issue, $reporter, $assignedTechnician);

            if ($assignedTechnician) {
                $this->sendTechnicianEmail($issue, $task, $reporter, $assignedTechnician);
                $this->sendTechnicianNotification($issue, $task, $reporter, $assignedTechnician);
            }

            DB::commit();

            // Clear session data
            session()->forget(['formData', 'attachments']);

            return redirect()->route('issue.success')->with([
                'success' => 'Issue reported successfully!',
                'assigned_technician' => $assignedTechnician ? $assignedTechnician->first_name . ' ' . $assignedTechnician->last_name : null,
                'issue_id' => $issue->issue_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Issue submission failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit issue. Please try again.');
        }
    }

    /**
     * Send email notification to assigned technician
     */
    protected function sendTechnicianEmail($issue, $task, $reporter, $technician)
    {
        $location = Location::find($issue->location_id);

        Mail::to($technician->email)
            ->send(new TechnicianAssignmentEmail(
                $issue,
                $task,
                $technician,
                $reporter
            ));
    }

    /**
     * Send database notification to reporter
     */
    public function sendReporterNotification($issue, $reporter, $technician)
    {
        $message = sprintf(
            "New Issue #%s\n\nType: %s\nUrgency: %s\nLocation: Building - %s, Floor - %s\nSubmitted: %s\nStatus: %s\n\n%s",
            $issue->issue_id,
            $issue->issue_type,
            $issue->urgency_level,
            $issue->location->building_name ?? 'N/A',
            $issue->location->floor_number ?? 'N/A',
            $issue->created_at,
            $issue->issue_status,
            $technician
                ? "Assigned Technician: {$technician->first_name} {$technician->last_name}"
                : "Awaiting technician assignment"
        );

        $reporter->notify(new DatabaseNotification(
            $message,
            route('Student.issue_details', $issue->issue_id),
            'New Issue Submitted'
        ));
    }


    /**
     * Send database notification to technician
     */
    public function sendTechnicianNotification($issue, $task, $reporter, $technician)
    {
        $message = sprintf(
            "New Assignment #%s\n\nPriority: %s\nExpected Completion: %s\nReporter: %s\nUrgency: %s\n\n%s",
            $issue->issue_id,
            $task->priority,
            $task->expected_completion,
            $reporter->first_name,
            $issue->urgency_level,
            $issue->issue_description
        );

        $technician->notify(new DatabaseNotification(
            $message,
            route('technician.task_details', $issue->issue_id),
            'New Task Assignment'
        ));
    }

    /**
     * Map urgency level to task priority
     */
    public function mapUrgencyToPriority($urgencyLevel)
    {
        return [
            'High' => 'High',
            'Medium' => 'Medium',
            'Low' => 'Low'
        ][$urgencyLevel] ?? 'Low';
    }



    public function editReportedIssue(Issue $issue)
    {
        // Check if the authenticated user is the reporter of this issue
        if (Auth::id() !== $issue->reporter_id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow editing if issue is still open
        if ($issue->issue_status !== 'Open') {
            return redirect()
                ->route('Student.issue_details', $issue->issue_id)
                ->with('swal_error', 'Only Open issues can be edited.');
        }

        $locations = Location::all();
        return view('Student.editissue', compact('issue', 'locations'));
    }


/*
 * this is a method for updating the issue
 * to do update this as well
 *
 */


//  public function update(Request $request, Issue $issue)
//  {
//      // Validate the request
//      $validated = $request->validate([
//          'location_id' => 'required|exists:location,location_id',
//          'issue_type' => 'required|string|max:255',
//          'issue_description' => 'required|string',
//          'safety_hazard' => 'required|boolean', // Added safety_hazard
//          'affected_areas' => 'required|integer|min:1', // Added affected_areas
//          'pc_number' => 'nullable|integer|min:1|max:100', // Conditional, nullable
//          'pc_issue_type' => 'nullable|string|max:255', // Conditional, nullable
//          'critical_work_affected' => 'nullable|boolean', // Conditional, nullable
//          'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,mp4', // Max 10MB (10240 KB)
//      ]);

//      // Authorization checks
//      if (Auth::id() !== $issue->reporter_id) {
//          abort(403, 'Unauthorized action.');
//      }

//      if ($issue->issue_status !== 'Open') {
//          return redirect()->route('Student.issue_details', $issue->issue_id)
//              ->with('error', 'Only Open issues can be updated.');
//      }

//      // Store the old issue type for comparison
//      $oldIssueType = $issue->issue_type;

//      // Calculate urgency score and determine urgency level
//      $urgencyScore = $this->calculateUrgencyScore($validated);
//      $urgencyLevel = $this->determineUrgencyLevel($urgencyScore);

//      // Check if we need to reassign due to issue type change or urgency level change
//      $needsReassignment = $issue->task && ($oldIssueType !== $validated['issue_type'] || $issue->urgency_level !== $urgencyLevel);

//      // If we need to reassign, clear the current assignment first
//      if ($needsReassignment) {
//          // Remove the task from the current technician
//          if ($issue->task->assignee_id) {
//              // Decrease the current technician's workload
//              DB::table('Technicians')
//                  ->where('user_id', $issue->task->assignee_id)
//                  ->decrement('current_workload');

//              // Mark technician as Available if workload is less than 6
//              $technician = DB::table('Technicians')
//                  ->where('user_id', $issue->task->assignee_id)
//                  ->first();

//              if ($technician && $technician->current_workload < 6) {
//                  DB::table('Technicians')
//                      ->where('user_id', $technician->user_id) // Use $technician->user_id
//                      ->update(['availability_status' => 'Available']);
//              }
//          }

//          // Clear the task's assignee while maintaining assignment_date
//          $issue->task->update([
//              'assignee_id' => null
//          ]);
//      }

//      // Prepare PC-specific fields, setting to null if issue type is not 'PC'
//      $pcData = [];
//      if ($validated['issue_type'] === 'PC') {
//          $pcData['pc_number'] = $validated['pc_number'] ?? null;
//          $pcData['pc_issue_type'] = $validated['pc_issue_type'] ?? null;
//          $pcData['critical_work_affected'] = $validated['critical_work_affected'] ?? false;
//      } else {
//          $pcData['pc_number'] = null;
//          $pcData['pc_issue_type'] = null;
//          $pcData['critical_work_affected'] = false;
//      }

//      // Update the issue
//      $issue->update(array_merge([
//          'location_id' => $validated['location_id'],
//          'issue_type' => $validated['issue_type'],
//          'issue_description' => $validated['issue_description'],
//          'safety_hazard' => $validated['safety_hazard'],
//          'affected_areas' => $validated['affected_areas'],
//          'urgency_level' => $urgencyLevel, // Use derived urgency level
//          'updated_at' => now()
//      ], $pcData)); // Merge PC-specific data

//      // Reassign if needed after issue update
//      if ($needsReassignment) {
//          // Reassign the task to a new technician
//          $newTechnician = $this->assignOrQueueTask($issue->task); // Assuming assignOrQueueTask exists and handles assignment
//          if ($newTechnician) {
//              // Update task priority with new urgency level
//              $issue->task->update([
//                  'priority' => $this->mapUrgencyToPriority($urgencyLevel) // Use derived urgency level
//              ]);
//          }
//      } else {
//          // Update task priority if issue type didn't change (only urgency might have changed)
//          if ($issue->task) {
//              $issue->task->update([
//                  'priority' => $this->mapUrgencyToPriority($urgencyLevel) // Use derived urgency level
//              ]);
//          }
//      }

//      // Handle attachments: Delete all old attachments if new ones are uploaded
//      if ($request->hasFile('attachments')) {
//          // Delete all old attachments
//          foreach ($issue->attachments as $attachment) {
//              Storage::disk($attachment->storage_disk)->delete($attachment->file_path);
//              $attachment->delete();
//          }

//          // Add new attachments
//          foreach ($request->file('attachments') as $file) {
//              $path = $file->store('attachments', 'public');

//              IssueAttachment::create([
//                  'issue_id' => $issue->issue_id,
//                  'file_path' => $path,
//                  'original_name' => $file->getClientOriginalName(),
//                  'mime_type' => $file->getMimeType(),
//                  'file_size' => $file->getSize(),
//                  'storage_disk' => 'public',
//              ]);
//          }
//      }

//      return redirect()->route('Student.issue_details', $issue->issue_id)
//          ->with('success', 'Issue updated successfully!');
//  }





     public function assignOrQueueTask(Task $task)
     {
         // Get all available technicians with matching specialization
         $technicians = User::whereHas('maintenanceStaff', function ($query) use ($task) {
             $query->where('availability_status', 'Available')
                 ->where('specialization', $task->issue->issue_type);
         })
             ->join('Technicians', 'users.user_id', '=', 'Technicians.user_id')
             ->select('users.*', 'Technicians.current_workload', 'Technicians.user_id as technician_id')
             ->get();

         if ($technicians->isEmpty()) {
             // Queue the task if no technicians available
             Log::info("Task {$task->task_id} queued - no available technicians");
             return null;
         }

         // Calculate average workload for technicians in this specialization
         $averageWorkload = $technicians->avg('current_workload') ?? 0;

         // Filter technicians who are below average workload
         $belowAverageTechnicians = $technicians->filter(function ($tech) use ($averageWorkload) {
             return $tech->current_workload <= $averageWorkload;
         });

         // If no technicians below average, use all available technicians
         $eligibleTechnicians = $belowAverageTechnicians->isEmpty() ? $technicians : $belowAverageTechnicians;

         // Sort technicians by workload (lowest first)
         $eligibleTechnicians = $eligibleTechnicians->sortBy('current_workload');

         // Get the technician with the lowest workload
         $technician = $eligibleTechnicians->first();

         // Assign the task
         $task->update([
             'assignee_id' => $technician->technician_id,
             'assignment_date' => now(),
         ]);

         // Update technician workload
         DB::table('Technicians')
             ->where('user_id', $technician->technician_id)
             ->increment('current_workload');

         // Mark as busy if workload reaches 6
         if ($technician->current_workload >= 6) {
             DB::table('Technicians')
                 ->where('user_id', $technician->technician_id)
                 ->update(['availability_status' => 'Busy']);
         }

         // Return the technician
         return $technician;
     }


public function update(Request $request, Issue $issue)
{
    // Validate the request
    $validated = $request->validate([
        'location_id' => 'required|exists:location,location_id',
        'issue_type' => 'required|string|max:255',
        'issue_description' => 'required|string',
        'safety_hazard' => 'required|boolean',
        'affected_areas' => 'required|integer|min:1',
        'affects_operations' => 'required|boolean',
        'pc_number' => 'nullable|integer|min:1|max:100',
        'pc_issue_type' => 'nullable|string|max:255',
        'critical_work_affected' => 'nullable|boolean',
        'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,mp4',
    ]);
    Log::info('Validation passed', $validated);
    // Authorization checks
    if (Auth::id() !== $issue->reporter_id) {
        Log::warning('Unauthorized update attempt by user ID: ' . Auth::id());
        abort(403, 'Unauthorized action.');
    }


    // Store old values for comparison and notification logic
    $oldIssueType = $issue->issue_type;
    $oldUrgencyLevel = $issue->urgency_level;
    $oldAssigneeId = $issue->task->assignee_id ?? null;

    // Calculate new urgency score and determine urgency level
    $urgencyScore = $this->calculateUrgencyScore($validated);
    $newUrgencyLevel = $this->determineUrgencyLevel($urgencyScore);

    // Determine if reassignment is needed
    // Reassignment is needed if issue type changes OR urgency level changes AND a task exists
    $needsReassignment = $issue->task && ($oldIssueType !== $validated['issue_type'] || $oldUrgencyLevel !== $newUrgencyLevel);

    $oldTechnician = null;
    if ($oldAssigneeId) {
        $oldTechnician = User::find($oldAssigneeId); // Assuming technicians are User models
    }

    // If reassignment is needed, clear the current assignment first
    if ($needsReassignment) {
        if ($oldTechnician) {
            // Decrease the current technician's workload
            DB::table('Technicians')
                ->where('user_id', $oldTechnician->id)
                ->decrement('current_workload');

            // Mark technician as Available if workload is less than 6
            $technicianRecord = DB::table('Technicians')
                ->where('user_id', $oldTechnician->id)
                ->first();

            if ($technicianRecord && $technicianRecord->current_workload < 6) {
                DB::table('Technicians')
                    ->where('user_id', $oldTechnician->id)
                    ->update(['availability_status' => 'Available']);
            }
        }

        // Clear the task's assignee while maintaining assignment_date if it was already set
        $issue->task->update([
            'assignee_id' => null
        ]);
    }

    // Prepare PC-specific fields, setting to null if issue type is not 'PC'
    $pcData = [];
    if ($validated['issue_type'] === 'PC') {
        $pcData['pc_number'] = $validated['pc_number'] ?? null;
        $pcData['pc_issue_type'] = $validated['pc_issue_type'] ?? null;
        $pcData['critical_work_affected'] = $validated['critical_work_affected'] ?? false;
    } else {
        $pcData['pc_number'] = null;
        $pcData['pc_issue_type'] = null;
        $pcData['critical_work_affected'] = false;
    }

    // Update the issue
    $issue->update(array_merge([
        'location_id' => $validated['location_id'],
        'issue_type' => $validated['issue_type'],
        'issue_description' => $validated['issue_description'],
        'safety_hazard' => $validated['safety_hazard'],
        'affected_areas' => $validated['affected_areas'],
        'affects_operations'=>$validated['affects_operations'],
        'urgency_level' => $newUrgencyLevel,
        'updated_at' => now()
    ], $pcData));
Log::info('Issue updated', $validated);
    $reporter = Auth::user();

    $assignedTechnician = null;

    // Reassign if needed after issue update, or update existing task details
    if ($needsReassignment) {
        // Reassign the task to a new technician
        $assignedTechnicianRecord = $this->assignOrQueueTask($issue->task);
        if ($assignedTechnicianRecord) {
            $assignedTechnician = User::find($assignedTechnicianRecord->user_id); // Get User model
            // Update task priority with new urgency level
            $issue->task->update([
                'priority' => $this->mapUrgencyToPriority($newUrgencyLevel)
            ]);
        }
        // If assignedTechnician is null here, it means the task was queued.
    } else {
        // No reassignment, but issue details might have changed for the current assignee.
        // Check if the task is currently assigned.
        if ($issue->task && $issue->task->assignee_id) {
            $assignedTechnician = User::find($issue->task->assignee_id);
            // Update task priority if urgency level changed (even if type didn't)
            $issue->task->update([
                'priority' => $this->mapUrgencyToPriority($newUrgencyLevel)
            ]);
        }
    }

    // Send notifications if a technician is assigned
    if ($assignedTechnician) {
        $this->sendTechnicianUpdateNotification($issue, $issue->task, $reporter, $assignedTechnician);
        // $this->sendTechnicianEmail($issue, $issue->task, $reporter, $assignedTechnician);
    }


    // Handle attachments: Delete all old attachments if new ones are uploaded
    if ($request->hasFile('attachments')) {
        // Delete all old attachments
        foreach ($issue->attachments as $attachment) {
            Storage::disk($attachment->storage_disk)->delete($attachment->file_path);
            $attachment->delete();
        }

        // Add new attachments
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('attachments', 'public');

            IssueAttachment::create([
                'issue_id' => $issue->issue_id,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'storage_disk' => 'public',
            ]);
        }
    }

    // Return with a custom session flash for SweetAlert2 modal
    return redirect()->route('Student.editissue', $issue->issue_id)
        ->with('swal_success_update', $issue->issue_id);
}

public function sendTechnicianUpdateNotification($issue, $task, $reporter, $technician)
{
    $message = sprintf(
        "Issue #%s Updated\n\nPriority: %s\nExpected Completion: %s\nReporter: %s\nUrgency: %s\n\nDescription: %s",
        $issue->issue_id,
        $task->priority,
        $task->expected_completion ? $task->expected_completion->format('Y-m-d H:i') : 'N/A', // Format date
        $reporter->first_name . ' ' . $reporter->last_name, // Full name
        $issue->urgency_level,
        $issue->location->building_name,
        $issue->issue_description
    );

    $technician->notify(new DatabaseNotification(
        $message,
        route('technician.task_details', $issue->issue_id),
        'Task Update for Issue #' . $issue->issue_id
    ));
}



    public function assignQueuedTasks($technicianId)
{
    $technician = User::find($technicianId);
    $availableSlots = 3 - $technician->maintenanceStaff->current_workload;

    $queuedTasks = Task::whereNull('assignee_id')
        ->orderBy('created_at', 'asc')
        ->limit($availableSlots)
        ->get();

    foreach ($queuedTasks as $queuedTask) {
        $this->assignOrQueueTask($queuedTask);
    }
}

    public function edit()
    {
        // Allow the user to go back to the form with the existing data
        return redirect()->route('Student.createissue');
    }
    public function viewAllIssues()
    {
        // Fetch the authenticated user's ID
        $userId = auth()->id();

        // Fetch all issues for the logged-in user (excluding closed issues)
        $issues = Issue::where('reporter_id', $userId)
            ->where('issue_status', '=', 'Resolved')
            ->with('location') // Eager load the location relationship
            ->orderBy('report_date', 'desc') // Sort by report date (newest first)
            ->paginate(5);

        return view('Student.view_issues', compact('issues'));
    }


    public function viewIssueDetails($id)
    {
        $issue = Issue::where('reporter_id', auth()->id())
            ->with([
                'location',
                'attachments',
                'task' => function($query) {
                    $query->with(['assignee', 'updates' => function($q) {
                        $q->with('staff')->latest('update_timestamp');
                    }]);
                }
            ])
            ->findOrFail($id);

        return view('Student.issue_details', compact('issue'));
    }

}
