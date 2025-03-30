<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Issue;
use App\Models\TaskUpdate;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
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

    // Find the tasks
    $task = Task::findOrFail($task_id);

    // Update the tasks status
    $task->issue_status = $request->status;
    $task->save();

    // Update the related issue status
    $issue = Issue::findOrFail($task->issue_id);

    // If the tasks is marked as "Completed", set the issue status to "Resolved"
    if ($request->status === 'Completed') {
        $issue->issue_status = 'Resolved'; // Ensure this value is allowed by the check constraint
    } else {
        $issue->issue_status = $request->status; // Sync issue status with tasks status
    }

    $issue->save();

    // Log the update
    $task->updates()->create([
        'staff_id' => auth()->id(),
        'update_description' => $request->update_description,
        'status_change' => $request->status,
    ]);

    // Redirect back with a success message
    return redirect()->route('tasks.update.form', $task->task_id)->with('success', 'Task updated successfully!');
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
        'tasks' => $task,
    ]);
}


}
