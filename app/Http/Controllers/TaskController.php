<?php

namespace App\Http\Controllers;

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
    public function assignedTasks()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Fetch tasks assigned to the user
        $tasks = Task::where('assignee_id', $userId)->get();

        // Pass the tasks to the view
        return view('technician.tasks_assigned', compact('tasks'));
    }


    public function viewTasks()
    {
        // Fetch all tasks with their assignee information
        $task = Task::with(['assignee', 'issue'])->get();
        $tasks = Task::with('admin')->get(); 
        // Pass the tasks to the view
        return view('admin.tasks.view', compact('task'));
    }

    public function showUpdateForm($task_id)
    {
        $task = Task::findOrFail($task_id);
        return view('technician.task_update_form', compact('task'));
    }

    
    // Handle the update submission
    public function updateTask(Request $request, $task_id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed',
            'update_description' => 'required|string',
        ]);
    
        // Find the task
        $task = Task::findOrFail($task_id);
        $oldStatus = $task->issue_status;
        $newStatus = $request->status;
    
        // Update the task status
        $task->issue_status = $newStatus;
        $task->save();
    
        // Update the related issue status
        $issue = Issue::findOrFail($task->issue_id);
    
        if ($newStatus === 'Completed') {
            $issue->issue_status = 'Resolved';
        } else {
            $issue->issue_status = $newStatus;
        }
        $issue->save();
    
        // Log the update
        $update = $task->updates()->create([
            'staff_id' => auth()->id(),
            'update_description' => $request->update_description,
            'status_change' => $newStatus,
        ]);
    
        // Get the involved parties
        $reporter = User::find($issue->reporter_id);
        $technician = User::find($issue->assignee_id);
        $updater = auth()->user();
    
        // Notification for the reporter (user who reported the issue)
        if ($reporter) {
            $reporter->notify(new DatabaseNotification(
                $this->createReporterMessage($issue, $task, $oldStatus, $newStatus, $updater, $request->update_description),
                
                route('Student.issue_details', [$task->issue_id])
            ));
        }
    
       // Notification for the technician (assignee)
// In your controller method (top)


// Later in notification code
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
}     return redirect()->route('tasks.update.form', $task->task_id)
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
            $updater->name,
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
            $updater->name,
            $description,
            $issue->issue_description,
            $issue->reporter->name
        );
    }

    // Delete a comment
    public function destroy(Comment $comment)
    {
        // Ensure the authenticated user owns the comment
        if (auth()->id() !== $comment->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to delete this comment.');
        }

        $comment->delete();
        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }

    // Update a comment
    public function update(Request $request, Comment $comment)
    {
        // Ensure the authenticated user owns the comment
        if (auth()->id() !== $comment->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to edit this comment.');
        }

        // Validate the request
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        // Update the comment
        $comment->update([
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment updated successfully.');
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
