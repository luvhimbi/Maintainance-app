<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            // Use Scout for case-insensitive search on User and Student models
            $searchLower = mb_strtolower($search);
            $userResults = \App\Models\User::search($searchLower)
                ->where('user_role', 'Student')
                ->get()
                ->load('studentDetail');

            $studentResults = \App\Models\Student::search($searchLower)
                ->with('user')
                ->get();

            // Merge unique users from both sources
            $userIds = $userResults->pluck('user_id')->merge($studentResults->pluck('user_id'))->unique();
            $students = \App\Models\User::whereIn('user_id', $userIds)
                ->with('studentDetail')
                ->get();

            // Paginate manually
            $perPage = 5;
            $page = $request->input('page', 1);
            $students = new \Illuminate\Pagination\LengthAwarePaginator(
                $students->forPage($page, $perPage),
                $students->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $students = \App\Models\User::where('user_role', 'Student')
                ->with('studentDetail')
                ->paginate(5);
        }

        return view('admin.Students.index', compact('students'));
    }


    public function show($id)
    {
        $student = User::findOrFail($id);
        return view('admin.students.show', compact('student'));
    }
}