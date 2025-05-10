<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use App\Models\HistoryLog;

class IssueController extends Controller
{


    //this is for populating the dropdown
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
        // Validate the form data
        $request->validate([
            'location_id' => 'required|integer',
            'issue_type' => 'required|string|max:50',
            'issue_description' => 'required|string',
            'urgency_level' => 'required|in:Low,Medium,High',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,pdf,doc,docx|max:2048', // Max 2MB per file
        ]);
        $reporterId = auth()->id();

        // Fetch the selected location details
        $location = Location::find($request->location_id);

        // Store form data in session
        $formData = $request->only([ 'location_id', 'issue_type', 'issue_description', 'urgency_level']);
        $formData['reporter_id'] = $reporterId;
        $formData['building'] = $location->building_name;
        $formData['floor'] = $location->floor_number;
        $formData['room'] = $location->room_number;
        session()->put('formData', $formData);

        // Store uploaded files in session
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('uploads/temp', 'public'); // Store files temporarily
                $attachments[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ];
            }
            session()->put('attachments', $attachments);
        }

        // Redirect to confirmation page
        return redirect()->route('Student.confirmissue');
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

        // If no form data exists, redirect back to the form
        if (empty($formData)) {
            return redirect()->route('issue.create')->with('error', 'Please fill out the form first.');
        }

        // Fetch location details for display
        $location = Location::find($formData['location_id']);

        return view('Student.confirmissue', compact('formData', 'attachments', 'location'));
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

            // Save the issue to the database with a default status of 'Open'
            $issue = Issue::create([
                'reporter_id' => $formData['reporter_id'],
                'location_id' => $formData['location_id'],
                'issue_type' => $formData['issue_type'],
                'issue_description' => $formData['issue_description'],
                'urgency_level' => $formData['urgency_level'],
                'issue_status' => 'Open',
                'created_at' => now()
            ]);

            session()->put('reported_issue_id', $issue->issue_id);

            $task = Task::create([
                'issue_id' => $issue->issue_id,
                'priority' => $this->mapUrgencyToPriority($formData['urgency_level']),
                'issue_status' => 'Pending',
                'expected_completion' => Carbon::now()->addWeek(),
                'created_at' => now()
            ]);

            // Assign task and get the assigned technician (if any)
            $assignedTechnician = $this->assignOrQueueTask($task);

            // Save attachments to the database
            foreach ($attachments as $attachment) {
                IssueAttachment::create([
                    'issue_id' => $issue->issue_id,
                    'file_path' => $attachment['path'],
                    'original_name' => $attachment['original_name'],
                    'mime_type' => $attachment['mime_type'],
                    'file_size' => $attachment['file_size'],
                    'storage_disk' => 'public',
                ]);
            }

            // Get related data for notifications
            $location = Location::find($formData['location_id']);
            $reporter = User::find($formData['reporter_id']);

            // Enhanced Reporter Notification
            $reporterMessage = sprintf(
                "New Issue #%s\n\n".
                "Type: %s\n".
                "Location: %s\n".
                "Urgency: %s\n".
                "Submitted: %s\n".
                "Status: %s\n\n".
                "%s",
                $issue->issue_id,
                $issue->issue_type,
                $location->building_name ?? 'Unknown Location',
                $issue->urgency_level,
                $issue->created_at,
                $issue->issue_status,
                $assignedTechnician
                    ? "Assigned Technician: {$assignedTechnician->first_name}{$assignedTechnician->last_name}"
                    : "Awaiting technician assignment"
            );

            $reporter->notify(new DatabaseNotification(
                $reporterMessage,
                route('Student.issue_details', $issue->issue_id),
                'New Issue Submitted'
            ));

            // Enhanced Technician Notification
            if ($assignedTechnician) {
                $technicianMessage = sprintf(
                    "New Assignment #%s\n\n".
                    "Priority: %s\n".
                    "Expected Completion: %s\n".
                    "Location: %s\n".
                    "Reporter: %s\n".
                    "Urgency: %s\n\n".
                    "%s",
                    $issue->issue_id,
                    $task->priority,
                    $task->expected_completion,
                    $location->buidling_name ?? 'Unknown Location',
                    $reporter->first_name,
                    $issue->urgency_level,
                    $issue->issue_description
                );

                $assignedTechnician->notify(new DatabaseNotification(
                    $technicianMessage,
                    route('technician.task_details', $issue->issue_id),
                    'New Task Assignment'
                ));
            }

            DB::commit();

            // Clear session data after successful submission
            session()->forget(['formData', 'attachments']);

            return redirect()->route('issue.success')->with([
                'success' => 'Issue reported successfully!',
                'assigned_technician' => $assignedTechnician ? $assignedTechnician->first_name : null,
                'issue_id' => $issue->issue_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Issue submission failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit issue. Please try again.');
        }
    }

private function mapUrgencyToPriority($urgency)
{
    return match($urgency) {
        'Critical' => 'High',
        'High' => 'Medium',
        'Normal' => 'Low',
        default => 'Low',
    };
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


public function update(Request $request, Issue $issue)
{
    // Validate the request
    $validated = $request->validate([
        'location_id' => 'required|exists:location,location_id',
        'issue_type' => 'required|string|max:255',
        'issue_description' => 'required|string',
        'urgency_level' => 'required|in:High,Medium,Low',
        'attachments.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,doc,docx',
        'keep_attachments' => 'nullable|array', // For attachments to keep
        'keep_attachments.*' => 'nullable|integer|exists:issue_attachments,id', // Validate attachment IDs
    ]);

    // Authorization checks
    if (Auth::id() !== $issue->reporter_id) {
        abort(403, 'Unauthorized action.');
    }

    if ($issue->issue_status !== 'Open') {
        return redirect()->route('Student.issue_details', $issue->issue_id)
            ->with('error', 'Only Open issues can be updated.');
    }

    // Update the issue
    $issue->update([
        'location_id' => $validated['location_id'],
        'issue_type' => $validated['issue_type'],
        'issue_description' => $validated['issue_description'],
        'urgency_level' => $validated['urgency_level'],
        'updated_at' => now()
    ]);

    // Update task priority
    if ($issue->task) {
        $issue->task->update([
            'priority' => $this->mapUrgencyToPriority($validated['urgency_level'])
        ]);
    }

    // Handle attachments
    if ($request->hasFile('attachments')) {
        // Option 1: Delete all old attachments
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



    return redirect()->route('Student.issue_details', $issue->issue_id)
        ->with('success', 'Issue updated successfully!');
}




    public function assignOrQueueTask(Task $task)
    {
        // Find available technician with matching specialization and workload < 3
        $technician = User::whereHas('maintenanceStaff', function ($query) use ($task) {
            $query->where('availability_status', 'Available')
                ->where('specialization', $task->issue->issue_type); // Match specialization to issue type
        })
            ->join('Technicians', 'users.user_id', '=', 'Technicians.user_id') // Join the tables
            ->orderBy('Technicians.current_workload', 'asc') // Order by workload
            ->select('users.*') // Select only user columns
            ->first();

        if ($technician) {
            // Assign tasks
            $task->update([
                'assignee_id' => $technician->user_id,
                'assignment_date' => now(),
            ]);

            // Update technician workload
            DB::table('Technicians')
                ->where('user_id', $technician->user_id)
                ->increment('current_workload');

            // Mark as busy if workload reaches 6
            if ($technician->maintenanceStaff->current_workload >= 6) {
                DB::table('Technicians')
                    ->where('user_id', $technician->user_id)
                    ->update(['availability_status' => 'Busy']);
            }

            // Return the technician
            return $technician;
        } else {
            // Queue the tasks (assignee_id remains null)
            Log::info("Task {$task->task_id} queued - no available technicians");
            return null;
        }
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
