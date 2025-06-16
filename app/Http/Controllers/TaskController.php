<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceStaff;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Issue;
use App\Models\TaskUpdate;
use App\Models\User;
use App\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\IssueUpdateMail;
use App\Mail\TaskOverdueReminderMail;
use App\Mail\TechnicianAssignmentEmail;
use App\Mail\TechnicianReassignmentMail;

use App\Models\Location;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{


    public function viewTasks()
    {
        // Fetch all tasks with related data in a single query
        $tasks = Task::with(['assignee', 'issue', 'admin'])->get();

        // Calculate overdue tasks count
        $overdueCount = Task::where('expected_completion', '<', now())
            ->where('issue_status', '!=', 'Completed')
            ->count();

        // Pass data to the view
        return view('admin.tasks.view', compact('tasks', 'overdueCount'));
    }

    public function showUpdateForm($task_id)
    {
        $task = Task::findOrFail($task_id);
        return view('technician.task_update_form', compact('task'));
    }


    // Handle the update submission
    public function updateTask(Request $request, $task_id)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed',
            'update_description' => 'required|string',
        ]);

        $task = Task::findOrFail($task_id);
        $oldStatus = $task->issue_status;
        $newStatus = $request->status;

        // Update task status
        $task->issue_status = $newStatus;

        // Set actual completion time if status is changed to Completed
        if ($newStatus === 'Completed' && $oldStatus !== 'Completed') {
            $task->actual_completion = now();
        }

        $task->save();

        // Update related issue status
        $issue = Issue::with(['building', 'floor', 'room'])->findOrFail($task->issue_id);

        if ($newStatus === 'Completed') {
            $issue->issue_status = 'Resolved';

            // Decrement workload for the assigned maintenance staff
            MaintenanceStaff::where('user_id', $task->assignee_id)
                ->decrement('current_workload');
        } else {
            $issue->issue_status = $newStatus;
        }
        $issue->save();

        // Log the update
        $update = $task->updates()->create([
            'staff_id' => auth()->id(),
            'update_description' => $request->update_description,
            'status_change' => $newStatus,
            'update_timestamp' => now(),
        ]);

        // Notification logic
        $reporter = User::find($issue->reporter_id);
        $updater = auth()->user();

        if ($reporter) {
            // In-app notification
            $reporter->notify(new DatabaseNotification(
                $this->createReporterMessage($issue, $task, $oldStatus, $newStatus, $updater, $request->update_description),
                route('Student.issue_details', [$task->issue_id])
            ));

            // Email notification
            try {
                Mail::to($reporter->email)->send(new IssueUpdateMail($issue, $task, $update, $updater));
            } catch (\Exception $e) {
                \Log::error('Failed to send issue update email: ' . $e->getMessage());
            }
        }

        if ($issue->assignee && Auth::id() !== $task->assignee->id) {
            $task->assignee->notify(new DatabaseNotification(
                $this->createTechnicianMessage(
                    $issue,
                    $task,
                    $oldStatus,
                    $newStatus,
                    Auth::user(),
                    $request->update_description
                ),
                route('technician.task_details', $task->id)
            ));
        }

        // Notify all admins when task is completed
        if ($newStatus === 'Completed') {
            $admins = User::where('user_role', 'Admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new DatabaseNotification(
                    sprintf(
                        "Task #%d has been completed:\n\n" .
                        "Issue Type: %s\n" .
                        "Location: %s, Floor %s, Room %s\n" .
                        "Completed by: %s\n" .
                        "Completion Time: %s",
                        $task->task_id,
                        $issue->issue_type,
                        $issue->building->building_name,
                        $issue->floor->floor_number,
                        $issue->room->room_number,
                        $task->assignee ? $task->assignee->first_name . ' ' . $task->assignee->last_name : 'Unassigned',
                        now()->format('Y-m-d H:i:s')
                    ),
                    route('admin.tasks.view')
                ));
            }
        }

        return redirect()->route('tasks.update.form', $task->task_id)
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Create notification message for the reporter
     */
    private function createReporterMessage($issue, $task, $oldStatus, $newStatus, $updater, $description)
    {
        $location = sprintf(
            "%s, Floor %s, Room %s",
            $issue->building->building_name,
            $issue->floor->floor_number,
            $issue->room->room_number
        );

        return sprintf(
            "Update on your issue #%s (%s):\n\n" .
            "Location: %s\n" .
            "Status changed from %s to %s\n" .
            "Updated by: %s\n" .
            "Update details: %s\n\n" .
            "Task: %s",
            $issue->id,
            $issue->issue_type,
            $location,
            $oldStatus,
            $newStatus,
            $updater->first_name,
            $description,
            $task->description ?? 'No additional details'
        );
    }

    /**
     * Create notification message for the technician
     */
    private function createTechnicianMessage($issue, $task, $oldStatus, $newStatus, $updater, $description)
    {
        $location = sprintf(
            "%s, Floor %s, Room %s",
            $issue->building->building_name,
            $issue->floor->floor_number,
            $issue->room->room_number
        );

        return sprintf(
            "Task #%s update (%s):\n\n" .
            "Location: %s\n" .
            "Status changed from %s to %s\n" .
            "Updated by: %s\n" .
            "Update details: %s\n\n" .
            "Issue: %s\n" .
            "Reporter: %s",
            $task->id,
            $issue->issue_type,
            $location,
            $oldStatus,
            $newStatus,
            $updater->first_name,
            $description,
            $issue->issue_description,
            $issue->reporter->name
        );
    }


    public function completedTasks()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Fetch completed tasks assigned to the logged-in technician
        $completedTasks = Task::where('assignee_id', $userId)
            ->where('issue_status', 'Completed')
            ->with(['issue', 'updates']) // Eager load related issue and updates
            ->get();

        // Pass data to the view
        return view('technician.completed_tasks', [
            'completedTasks' => $completedTasks,
        ]);
    }

    public function taskUpdates($task_id)
    {
        // Fetch the tasks with its updates
        $task = Task::with(['updates', 'updates.staff']) // Eager load updates and staff
        ->findOrFail($task_id);

        // Pass data to the view
        return view('technician.task_updates', [
            'task' => $task,
        ]);
    }

    public function sendReminder(Request $request, $task_id)
    {
        $task = Task::with(['assignee', 'issue'])->findOrFail($task_id);

        if (!$task->assignee) {
            return response()->json(['success' => false, 'message' => 'No technician assigned to this task.'], 400);
        }

        try {
            // In-app notification
            $task->assignee->notify(new DatabaseNotification(
                "Reminder: You have an overdue task (#{$task->task_id}) for issue '{$task->issue->issue_type}' that requires your attention.",
                route('technician.task_details', $task->task_id)
            ));

            // Email notification
            Mail::to($task->assignee->email)->send(new TaskOverdueReminderMail($task));

            return response()->json(['success' => true, 'message' => 'Reminder sent to technician.']);
        } catch (\Exception $e) {
            \Log::error('Failed to send overdue task reminder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send reminder.'], 500);
        }
    }


    public function reassignTask($taskId)
    {
        $task = Task::with(['assignee', 'issue', 'issue.reporter', 'issue.location'])->findOrFail($taskId);

        // Only allow reassignment if task is overdue and not completed
        if ($task->issue_status === 'Completed' || !$task->expected_completion || !$task->expected_completion->isPast()) {
            return response()->json(['message' => 'Task is not overdue or already completed.'], 400);
        }

        $oldTechnician = $task->assignee;
        $issue = $task->issue;
        $reporter = $issue->reporter;

        // Extend expected completion by 2 days from now
        $task->expected_completion = now()->addDays(2);
        $task->save();

        // Use IssueController's assignOrQueueTask method
        $issueController = app(\App\Http\Controllers\IssueController::class);

        // Unassign the current technician (decrement workload, set assignee_id to null)
        if ($oldTechnician) {
            \DB::table('Technicians')
                ->where('user_id', $oldTechnician->id)
                ->decrement('current_workload');

            // Mark technician as Available if workload is less than 6
            $technicianRecord = \DB::table('Technicians')
                ->where('user_id', $oldTechnician->id)
                ->first();

            if ($technicianRecord && $technicianRecord->current_workload < 6) {
                \DB::table('Technicians')
                    ->where('user_id', $oldTechnician->id)
                    ->update(['availability_status' => 'Available']);
            }

            // Notify old technician (in-app and email)
            $oldTechnician->notify(new \App\Notifications\DatabaseNotification(
                "You have been unassigned from Task #{$task->task_id} due to inactivity and the task being overdue.",
                null,
                'Task Unassigned'
            ));
            try {
                \Mail::to($oldTechnician->email)->send(new \App\Mail\TechnicianReassignmentMail($issue, $oldTechnician, $issue->issue_type, $issue->issue_type));
            } catch (\Exception $e) {
                \Log::error('Failed to send reassignment email to old technician: ' . $e->getMessage());
            }
        }

        // Unassign the task
        $task->update(['assignee_id' => null]);

        // Assign to a new technician
        $newTechnician = $issueController->assignOrQueueTask($task);

        if ($newTechnician) {
            $userNewTechnician = \App\Models\User::find($newTechnician->user_id);

            // Notify new technician (in-app and email)
            $userNewTechnician->notify(new \App\Notifications\DatabaseNotification(
                "You have been assigned a new overdue task (Task #{$task->task_id}) for Issue #{$issue->issue_id}.",
                route('technician.task_details', $issue->issue_id),
                'New Task Assignment'
            ));

            try {
                $location = $issue->location;
                $reporter = $issue->reporter;
                $taskUrl = route('technician.task_details', $issue->issue_id);
                \Mail::to($userNewTechnician->email)->send(new \App\Mail\TechnicianAssignmentEmail(
                    $issue,
                    $task,
                    $userNewTechnician,
                    $reporter,
                    $location,
                    $taskUrl
                ));
            } catch (\Exception $e) {
                \Log::error('Failed to send assignment email to new technician: ' . $e->getMessage());
            }

            // Notify the reporter about reassignment and apologize
            if ($reporter) {
                $reporter->notify(new \App\Notifications\DatabaseNotification(
                    "Your reported issue (Issue #{$issue->issue_id}) became overdue and has now been reassigned to a new technician. We apologize for the inconvenience and are working to resolve your issue as soon as possible.",
                    route('Student.issue_details', $issue->issue_id),
                    'Issue Reassigned'
                ));
            }

            return response()->json(['success' => true, 'message' => 'Task reassigned to a new technician and expected completion extended.']);
        } else {
            // Notify the reporter about reassignment attempt and apologize
            if ($reporter) {
                $reporter->notify(new \App\Notifications\DatabaseNotification(
                    "Your reported issue (Issue #{$issue->issue_id}) became overdue and we could not find an available technician at this time. Your task has been queued and will be assigned as soon as possible. We apologize for the inconvenience.",
                    route('Student.issue_details', $issue->issue_id),
                    'Issue Queued'
                ));
            }
            return response()->json(['success' => false, 'message' => 'No available technician found. Task has been queued and reporter notified.']);
        }
    }
}
