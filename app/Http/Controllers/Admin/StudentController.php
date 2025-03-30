<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('user_role', 'Student');
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone_number', 'like', "%$search%");
            });
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        $students = $query->orderBy('username')->paginate(10);
        
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:200|unique:users',
            'phone_number' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:Active,Inactive,Suspended'
        ]);

        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password_hash' => bcrypt($validated['password']),
            'status' => $validated['status'],
            'user_role' => 'Student'
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully');
    }

    public function edit(User $student)
    {
        $this->authorizeStudent($student);
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $this->authorizeStudent($student);
        
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,'.$student->user_id.',user_id',
            'email' => 'required|email|max:200|unique:users,email,'.$student->user_id.',user_id',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number,'.$student->user_id.',user_id',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:Active,Inactive,Suspended'
        ]);

        $updateData = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'status' => $validated['status']
        ];
        
        if ($request->filled('password')) {
            $updateData['password_hash'] = bcrypt($validated['password']);
        }
        
        $student->update($updateData);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully');
    }

    public function destroy(User $student)
    {
        $this->authorizeStudent($student);
        $student->delete();
        
        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully');
    }

    protected function authorizeStudent(User $user)
    {
        if ($user->user_role !== 'Student') {
            abort(403, 'This user is not a student');
        }
    }
}