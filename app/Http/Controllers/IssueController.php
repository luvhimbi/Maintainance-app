<?php

namespace App\Http\Controllers;

use App\Exceptions\MissingIssueFormDataException;
use App\Mail\TechnicianAssignmentEmail;
use App\Mail\TechnicianReassignmentMail;
use App\Services\SupabaseStorageService;
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
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;

class IssueController extends Controller
{
    protected $supabaseStorage;

    public function __construct(SupabaseStorageService $supabaseStorage)
    {
        $this->supabaseStorage = $supabaseStorage;
    }

    public function create()
    {
        // Fetch buildings to populate the dropdown
        $buildings = Building::orderBy('building_name')->get();

        // Retrieve session data if it exists (for going back to the form)
        $formData = session()->get('formData', []);
        $attachments = session()->get('attachments', []);

        // If we have a building_id in the form data, fetch the floors
        if (!empty($formData['building_id'])) {
            $floors = Floor::where('building_id', $formData['building_id'])->get();
        } else {
            $floors = collect();
        }

        // If we have a floor_id in the form data, fetch the rooms
        if (!empty($formData['floor_id'])) {
            $rooms = Room::where('floor_id', $formData['floor_id'])->get();
        } else {
            $rooms = collect();
        }

        // Debug session data
        \Log::info('Form Data in Create:', $formData);
        \Log::info('Floors:', $floors->toArray());
        \Log::info('Rooms:', $rooms->toArray());

        return view('Student.createissue', compact('buildings', 'floors', 'rooms', 'formData', 'attachments'));
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
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor_id' => 'required|exists:floors,id',
            'room_id' => 'required|exists:rooms,id',
            'issue_type' => 'required|string|max:50',
            'issue_description' => 'required|string',
            'safety_hazard' => 'required|boolean',
            'affected_areas' => 'required|integer|min:1',
            'pc_number' => 'nullable|required_if:issue_type,PC|integer|min:1|max:100',
            'pc_issue_type' => 'nullable|required_if:issue_type,PC|string|max:50',
            'critical_work_affected' => 'nullable|boolean',
            'affects_operations' => 'required|boolean',
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
        $building = Building::find($validated['building_id']);
        $floor = Floor::find($validated['floor_id']);
        $room = Room::find($validated['room_id']);

        // Prepare form data for session
        $formData = [
            'building_id' => $validated['building_id'],
            'floor_id' => $validated['floor_id'],
            'room_id' => $validated['room_id'],
            'issue_type' => $validated['issue_type'],
            'issue_description' => $validated['issue_description'],
            'safety_hazard' => $validated['safety_hazard'],
            'affected_areas' => $validated['affected_areas'],
            'reporter_id' => $reporterId,
            'building' => $building->building_name,
            'floor' => $floor->floor_number,
            'room' => $room->room_number,
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
                try {
                    $result = $this->supabaseStorage->uploadFile($file, 'issue-attachments');
                    if ($result['success']) {
                        $attachments[] = [
                            'path' => $result['path'],
                            'original_name' => $result['original_name'],
                            'mime_type' => $result['mime_type'],
                            'size' => $result['size']
                        ];
                    } else {
                        Log::error('File upload failed: ' . ($result['error'] ?? 'Unknown error'));
                        throw new \RuntimeException('Failed to upload file to Supabase');
                    }
                } catch (\Exception $e) {
                    Log::error('File upload failed: ' . $e->getMessage());
                    throw new \RuntimeException('Failed to upload file: ' . $e->getMessage());
                }
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
        $formData = session()->get('formData', []);
        $attachments = session()->get('attachments', []);

        if (empty($formData)) {
            return redirect()->route('Student.createissue')->with('error', 'Please fill out the form first.');
        }

        // Get the building, floor, and room details
        $building = Building::find($formData['building_id']);
        $floor = Floor::find($formData['floor_id']);
        $room = Room::find($formData['room_id']);

        // Create a location object for the view
        $location = (object)[
            'building_name' => $building->building_name,
            'floor_number' => $floor->floor_number,
            'room_number' => $room->room_number
        ];

        return view('Student.confirmissue', [
            'formData' => $formData,
            'attachments' => $attachments,
            'location' => $location
        ]);
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
                'building_id' => $formData['building_id'],
                'floor_id' => $formData['floor_id'],
                'room_id' => $formData['room_id'],
                'issue_type' => $formData['issue_type'],
                'issue_description' => $formData['issue_description'],
                'urgency_level' => $formData['urgency_level'],
                'urgency_score' => $formData['urgency_score'] ?? null,
                'safety_hazard' => $formData['safety_hazard'] ?? false,
                'affects_operations' => $formData['affects_operations'] ?? false,
                'affected_areas' => $formData['affected_areas'] ?? 1,
                'pc_number' => $formData['pc_number'] ?? null,
                'pc_issue_type' => $formData['pc_issue_type'] ?? null,
                'critical_work_affected' => $formData['critical_work_affected'] ?? false,
                'issue_status' => 'Open',
                'created_at' => now()
            ]);

            Log::info("Created issue with ID: $issue->issue_id");
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
                    'storage_disk' => 'supabase'
                ]);
            }

            // Get related data
            $reporter = User::find($formData['reporter_id']);
            $building = Building::find($formData['building_id']);
            $floor = Floor::find($formData['floor_id']);
            $room = Room::find($formData['room_id']);

            // Send a success message notification to the reporter
            $this->sendReporterNotification($issue, $reporter, $assignedTechnician, $building, $floor, $room);

            if ($assignedTechnician) {
                $this->sendTechnicianEmail($issue, $task, $reporter, $assignedTechnician, $building, $floor, $room);
                $this->sendTechnicianNotification($issue, $task, $reporter, $assignedTechnician, $building, $floor, $room);
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
    protected function sendTechnicianEmail($issue, $task, $reporter, $technician, $building, $floor, $room)
    {
        try {
            $taskUrl = route('technician.task_details', $issue->issue_id);

            Mail::to($technician->email)
                ->send(new TechnicianAssignmentEmail(
                    $issue,
                    $task,
                    $technician,
                    $reporter,
                    $building,
                    $floor,
                    $room,
                    $taskUrl
                ));
        } catch (\Exception $e) {
            Log::error('Failed to send technician assignment email: ' . $e->getMessage());
        }
    }

    /**
     * Send database notification to reporter
     */
    public function sendReporterNotification($issue, $reporter, $technician, $building, $floor, $room)
    {
        $message = sprintf(
            "New Issue #%s\n\nType: %s\nUrgency: %s\nLocation: Building - %s, Floor - %s, Room - %s\nSubmitted: %s\nStatus: %s\n\n%s",
            $issue->issue_id,
            $issue->issue_type,
            $issue->urgency_level,
            $building->building_name,
            $floor->floor_number,
            $room->room_number,
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
    public function sendTechnicianNotification($issue, $task, $reporter, $technician, $building, $floor, $room)
    {
        $message = sprintf(
            "New Assignment #%s\n\nPriority: %s\nExpected Completion: %s\nReporter: %s\nUrgency: %s\nLocation: Building - %s, Floor - %s, Room - %s\n\n%s",
            $issue->issue_id,
            $task->priority,
            $task->expected_completion,
            $reporter->first_name,
            $issue->urgency_level,
            $building->building_name,
            $floor->floor_number,
            $room->room_number,
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

        // Fetch buildings, floors, and rooms
        $buildings = Building::orderBy('building_name')->get();

        // Get floors for the current building
        $floors = Floor::where('building_id', $issue->building_id)->get();

        // Get rooms for the current floor
        $rooms = Room::where('floor_id', $issue->floor_id)->get();

        return view('Student.editissue', compact('issue', 'buildings', 'floors', 'rooms'));
    }



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


         // if there is technician then return null and log that nobody was assigned

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



public function sendTechnicianUpdateNotification($issue, $task, $reporter, $technician)
{
    // Get the location details
    $building = Building::find($issue->building_id);
    $floor = Floor::find($issue->floor_id);
    $room = Room::find($issue->room_id);

    $message = sprintf(
        "Issue #%s Updated\n\nPriority: %s\nExpected Completion: %s\nReporter: %s\nUrgency: %s\nLocation: Building - %s, Floor - %s, Room - %s\n\nDescription: %s",
        $issue->issue_id,
        $task->priority,
        $task->expected_completion ? $task->expected_completion->format('Y-m-d H:i') : 'N/A',
        $reporter->first_name . ' ' . $reporter->last_name,
        $issue->urgency_level,
        $building->building_name,
        $floor->floor_number,
        $room->room_number,
        $issue->issue_description
    );

    $technician->notify(new DatabaseNotification(
        $message,
        route('technician.task_details', $issue->issue_id),
        'Task Update for Issue #' . $issue->issue_id
    ));
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

    public function update(Request $request, $id)
    {
        $issue = Issue::findOrFail($id);

        // Check if the user is the reporter of the issue
        if ($issue->reporter_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to edit this issue.');
        }

        // Check if the issue is still open
        if ($issue->issue_status !== 'Open') {
            return redirect()->back()->with('error', 'You can only edit open issues.');
        }

        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor_id' => 'required|exists:floors,id',
            'room_id' => 'required|exists:rooms,id',
            'issue_type' => 'required|in:Electrical,Plumbing,Structural,HVAC,Furniture,PC,General',
            'issue_description' => 'required|string',
            'safety_hazard' => 'required|boolean',
            'affected_areas' => 'required|integer|min:1',
            'affects_operations' => 'required|boolean',
            'pc_number' => 'nullable|required_if:issue_type,PC|integer|min:1|max:100',
            'pc_issue_type' => 'nullable|required_if:issue_type,PC|string|max:50',
            'critical_work_affected' => 'nullable|boolean',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,pdf,doc,docx|max:2048'
        ]);

        // Store old values for comparison and notification logic
        $oldIssueType = $issue->issue_type;
        $oldUrgencyLevel = $issue->urgency_level;
        $oldAssigneeId = $issue->task->assignee_id ?? null;

        // Calculate new urgency score and determine urgency level
        $urgencyScore = $this->calculateUrgencyScore($validated);
        $newUrgencyLevel = $this->determineUrgencyLevel($urgencyScore);

        // Reassignment is needed if issue type changes OR urgency level changes AND a task exists
        $needsReassignment = $issue->task && ($oldIssueType !== $validated['issue_type'] || $oldUrgencyLevel !== $newUrgencyLevel);

        $oldTechnician = null;
        if ($oldAssigneeId) {
            $oldTechnician = User::find($oldAssigneeId);
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
            'building_id' => $validated['building_id'],
            'floor_id' => $validated['floor_id'],
            'room_id' => $validated['room_id'],
            'issue_type' => $validated['issue_type'],
            'issue_description' => $validated['issue_description'],
            'safety_hazard' => $validated['safety_hazard'],
            'affected_areas' => $validated['affected_areas'],
            'affects_operations' => $validated['affects_operations'],
            'urgency_level' => $newUrgencyLevel,
            'urgency_score' => $urgencyScore,
            'updated_at' => now()
        ], $pcData));

        $reporter = Auth::user();
        $assignedTechnician = null;

        // Reassign if needed after issue update, or update existing task details
        if ($needsReassignment) {
            // Reassign the task to a new technician
            $assignedTechnicianRecord = $this->assignOrQueueTask($issue->task);
            if ($assignedTechnicianRecord) {
                $assignedTechnician = User::find($assignedTechnicianRecord->user_id);
                // Update task priority with new urgency level
                $issue->task->update([
                    'priority' => $this->mapUrgencyToPriority($newUrgencyLevel)
                ]);
            }
            // Notify the old technician if reassigned
            if ($oldTechnician) {
                // In-app notification
                $oldType = $oldIssueType;
                $newType = $validated['issue_type'];
                $oldTechnician->notify(new DatabaseNotification(
                    "You have been unassigned from Issue #{$issue->issue_id}. The issue type has changed from '{$oldType}' to '{$newType}'. The task has been reassigned to another technician.",
                    null,
                    'Task Reassignment'
                ));
                // Email notification
                try {
                    Mail::to($oldTechnician->email)->send(new TechnicianReassignmentMail($issue, $oldTechnician, $oldType, $newType));
                } catch (\Exception $e) {
                    \Log::error('Failed to send reassignment email to old technician: ' . $e->getMessage());
                }
            }
            // Notify the new technician if assigned
            if ($assignedTechnician) {
                // Get the building, floor, and room details
                $building = Building::find($validated['building_id']);
                $floor = Floor::find($validated['floor_id']);
                $room = Room::find($validated['room_id']);

                // In-app notification
                $assignedTechnician->notify(new DatabaseNotification(
                    "You have been assigned a new task for Issue #{$issue->issue_id} ({$issue->issue_type}).",
                    route('technician.task_details', $issue->issue_id),
                    'New Task Assignment'
                ));
                // Email notification
                try {
                    $taskUrl = route('technician.task_details', $issue->issue_id);
                    Mail::to($assignedTechnician->email)->send(new TechnicianAssignmentEmail(
                        $issue,
                        $issue->task,
                        $assignedTechnician,
                        $reporter,
                        $building,
                        $floor,
                        $room,
                        $taskUrl
                    ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send assignment email to new technician: ' . $e->getMessage());
                }
            }
        } else {
            // No reassignment, but issue details might have changed for the current assignee
            if ($issue->task && $issue->task->assignee_id) {
                $assignedTechnician = User::find($issue->task->assignee_id);
                // Update task priority if urgency level changed
                $issue->task->update([
                    'priority' => $this->mapUrgencyToPriority($newUrgencyLevel)
                ]);
            }
        }

        // Send notifications if a technician is assigned
        if ($assignedTechnician) {
            $this->sendTechnicianUpdateNotification($issue, $issue->task, $reporter, $assignedTechnician);
        }

        // Handle attachments
        if ($request->hasFile('attachments')) {
            // Delete old attachments from Supabase
            foreach ($issue->attachments as $attachment) {
                $this->supabaseStorage->deleteFile($attachment->file_path);
                $attachment->delete();
            }

            // Add new attachments
            foreach ($request->file('attachments') as $file) {
                try {
                    $result = $this->supabaseStorage->uploadFile($file, 'issue-attachments');
                    if ($result['success']) {
                        IssueAttachment::create([
                            'issue_id' => $issue->issue_id,
                            'file_path' => $result['path'],
                            'original_name' => $result['original_name'],
                            'mime_type' => $result['mime_type'],
                            'file_size' => $result['size'],
                            'storage_disk' => 'supabase'
                        ]);
                    } else {
                        Log::error('File upload failed: ' . ($result['error'] ?? 'Unknown error'));
                        throw new \RuntimeException('Failed to upload file to Supabase');
                    }
                } catch (\Exception $e) {
                    Log::error('File upload failed: ' . $e->getMessage());
                    throw new \RuntimeException('Failed to upload file: ' . $e->getMessage());
                }
            }
        }

        // Return with a custom session flash for SweetAlert2 modal
        return redirect()->route('Student.editissue', $issue->issue_id)
            ->with('swal_success_update', $issue->issue_id);
    }

}
