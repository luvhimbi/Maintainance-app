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
class IssueController extends Controller
{

    public function create()
    {
        // Fetch locations to populate a dropdown in the form
        $locations = Location::all();

        // Retrieve session data if it exists (for going back to the form)
        $formData = session()->get('formData', []);
        $attachments = session()->get('attachments', []);

        return view('Student.createissue', compact('locations', 'formData', 'attachments'));
    }



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


        // Ensure the issue ID is in the session
        // if (!session()->has('reporter_id')) {
        //     return redirect()->route('home')->with('error', 'No issue found to track.');
        // }

        // // Get the issue from database
        // $issue = Issue::find(session('reporter_id'));

        // // Clear the session data
        // session()->forget('reporter_id');

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

    public function save()
    {
        // Retrieve session data
        $formData = session()->get('formData', []);
        $attachments = session()->get('attachments', []);

        // If no form data exists, redirect back to the form
        if (empty($formData)) {
            return redirect()->route('Student.createissue')->with('error', 'Please fill out the form first.');
        }

        // Save the issue to the database with a default status of 'Open'
        $issue = Issue::create([
            'reporter_id' => $formData['reporter_id'],
            'location_id' => $formData['location_id'],
            'issue_type' => $formData['issue_type'],
            'issue_description' => $formData['issue_description'],
            'urgency_level' => $formData['urgency_level'],
            'issue_status' => 'Open', // Set default status
        ]);
        $task = Task::create([
            'issue_id' => $issue->issue_id,
            'priority' => $this->mapUrgencyToPriority($formData['urgency_level']),
            'issue_status' => 'Pending', // Explicitly set status
            'expected_completion' => Carbon::now()->addWeek() // 7 days from now
        ]);
        $this->assignOrQueueTask($task);
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

        // Clear session data
        // session()->forget(['formData', 'attachments']);

        // Redirect to a success page
        return redirect()->route('issue.success')->with('success', 'Issue reported successfully!');
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

// public function success()
// {
//     // Ensure the issue ID is in the session
//     if (!session()->has('issue_id')) {
//         return redirect()->route('home')->with('error', 'No issue found to track.');
//     }

//     return view('Student.success');
// }
private function assignOrQueueTask(Task $task)
{
    // Find available technician with matching specialization and workload < 3
    $technician = User::whereHas('maintenanceStaff', function ($query) use ($task) {
        $query->where('availability_status', 'Available')
            //   ->where('current_workload', '<', 3)
              ->where('specialization', $task->issue->issue_type); // Match specialization to issue type
    })
    ->join('maintenance_staff', 'users.user_id', '=', 'maintenance_staff.user_id') // Join the tables
    ->orderBy('maintenance_staff.current_workload', 'asc') // Order by workload
    ->select('users.*') // Select only user columns
    ->first();

    if ($technician) {
        // Assign tasks
        $task->update([
            'assignee_id' => $technician->user_id,
            
            'assignment_date' => now(),
        ]);
        

        // Update technician workload
        DB::table('maintenance_staff')
            ->where('user_id', $technician->user_id)
            ->increment('current_workload');

        // Mark as busy if workload reaches 3
        if ($technician->maintenanceStaff->current_workload + 1 >= 3) {
            DB::table('maintenance_staff')
                ->where('user_id', $technician->user_id)
                ->update(['availability_status' => 'Busy']);
        }
    } else {
        // Queue the tasks (assignee_id remains null)
        Log::info("Task {$task->task_id} queued - no available technicians");
    }
}

public function completeTask($taskId)
{
    $task = Task::findOrFail($taskId);
    $task->update(['issue_status' => 'Completed']);

    if ($task->assignee_id) {
        // Decrement workload
        DB::table('maintenance_staff')
            ->where('user_id', $task->assignee_id)
            ->decrement('current_workload');

        // Recheck availability
        $workload = DB::table('maintenance_staff')
            ->where('user_id', $task->assignee_id)
            ->value('current_workload');

        if ($workload < 3) {
            DB::table('maintenance_staff')
                ->where('user_id', $task->assignee_id)
                ->update(['availability_status' => 'Available']);
        }

        // Assign queued tasks to freed-up technician
        $this->assignQueuedTasks($task->assignee_id);
    }

    return redirect()->back()->with('success', 'Task completed!');
}


private function assignQueuedTasks($technicianId)
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
