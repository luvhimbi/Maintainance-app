<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Issue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskUpdate;
class TaskAssignmentController extends Controller
{
    public function create($task_id = null)
    {
        // If no task ID is provided, redirect back with error
        if (!$task_id) {
            return redirect()->back()->with('error', 'No task specified for assignment');
        }
    
        // Fetch the specific task with its related issue
        $task = Task::with(['issue', 'issue.reporter'])->findOrFail($task_id);
        
        // Verify the task needs assignment
        if ($task->assignee_id) {
            return redirect()->back()->with('error', 'This task is already assigned to a technician');
        }
        
        //fetch all technicians
          $technicians = User::where('user_role', 'Technician')
                     ->select('user_id', 'username', 'email', 'phone_number') 
                     ->orderBy('username')
                     ->get();
                     $technicians = User::where('user_role', 'Technician')
                     ->select('user_id', 'username', 'email', 'phone_number') 
                     ->orderBy('username')
                     ->get();

        return view('Admin.tasks.assign', compact('task', 'technicians'));
    }

    
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:task,task_id',
            'assignee_id' => [
                'required',
                'exists:users,user_id',
                function ($attr, $value, $fail) use ($request) {
                    $task = Task::find($request->task_id);
                    if ($task && $task->assignee_id) {
                        $fail('This task is already assigned');
                    }
                }
            ]
        ]);

        try {
            $task = Task::findOrFail($validated['task_id']);
            
            $task->update([
                'assignee_id' => $validated['assignee_id'],
                'admin_id' => auth()->id()
               
            ]);

            return redirect()->route('tasks.assign', $task->task_id)
                ->with('success', 'Technician assigned successfully');

        } catch (\Exception $e) {
            Log::error("Assignment error: " . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to assign technician');
        }
    }
    public function show(Task $task)
    {
        $task->load([
            'issue.location',
            'issue.reporter',
            'assignee',
            'admin',
            'updates.staff'
        ]);

        return view('admin.tasks.progress', compact('task'));
    }
}
