<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class StudentController extends Controller
{
    public function index(Request $request)
{
    $query = User::with('studentDetail')->where('user_role', 'Student');

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('first_name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('phone_number', 'like', "%$search%");
        });
    }

 

    $students = $query->orderBy('first_name')->paginate(5);

    return view('admin.students.index', compact('students'));
}


   

    public function show($id)
    {
        $student = User::findOrFail($id);
        return view('admin.students.show', compact('student'));
    }
}