<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('user_role', 'Staff_Member')
                        ->with('staffDetail');    

        $staff = $query->orderBy('first_name')->paginate(5); // Pagination added

        return view('admin.staff.index', compact('staff'));
    }

    public function show($id)
    {
        $staffMember = User::findOrFail($id);
        return view('admin.staff.show', compact('staffMember'));
    }
}
