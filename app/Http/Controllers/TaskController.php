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
            $task->actual_completion = now(); // or Carbon::now() if you're using Carbon
        }

        $task->save();

        // Update related issue status
        $issue = Issue::findOrFail($task->issue_id);

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

        // Notification logic remains the same
        $reporter = User::find($issue->reporter_id);
        $updater = auth()->user();

        if ($reporter) {
            $reporter->notify(new DatabaseNotification(
                $this->createReporterMessage($issue, $task, $oldStatus, $newStatus, $updater, $request->update_description),
                route('Student.issue_details', [$task->issue_id])
            ));
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

        return redirect()->route('tasks.update.form', $task->task_id)
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Create notification message for the reporter
     */
    private function createReporterMessage($issue, $task, $oldStatus, $newStatus, $updater, $description)
    {
        return sprintf(
            "Update on your issue #%s (%s):\n\n" .
            "Status changed from %s to %s\n" .
            "Updated by: %s\n" .
            "Update details: %s\n\n" .
            "Task: %s",
            $issue->id,
            $issue->issue_type,
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
        return sprintf(
            "Task #%s update (%s):\n\n" .
            "Status changed from %s to %s\n" .
            "Updated by: %s\n" .
            "Update details: %s\n\n" .
            "Issue: %s\n" .
            "Reporter: %s",
            $task->id,
            $issue->issue_type,
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


}
