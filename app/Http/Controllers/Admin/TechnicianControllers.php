<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Task;
use App\Models\Issue;
class TechnicianControllers extends Controller 
{


    public function index()
    {
        $technicians = User::where('user_role', 'Technician')
            ->with(['maintenanceStaff'])  // Added eager loading for related models
            ->orderBy('username')
            ->paginate(10);
            
        return view('admin.technicians.index', compact('technicians'));  // Updated view path to admin namespace
    }

    public function create()
    {
        $specializations = ['Electrical', 'Structural', 'Plumbing'];  // Added predefined specializations
        return view('admin.technicians.create', compact('specializations'));  // Updated view path
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:200|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone_number' => 'nullable|string|max:20|unique:users',
            'specialization' => 'required|string|max:100',
           
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),  // Changed from password_hash to password
            'phone_number' => $request->phone_number,
            'user_role' => 'Technician',
            'status' => 'Active',
        ]);

        Technician::create([
            'user_id' => $user->id,  // Changed from user_id to id
            'specialization' => $request->specialization,
        ]);

        return redirect()->route('admin.technicians.index')  // Updated route name
            ->with('success', 'Technician created successfully.');
    }

    public function show($id)
    {
        $technician = User::with(['maintenanceStaff', 'tasks', 'issues.comments'])->findOrFail($id);
        return view('admin.technicians.show', compact('technician'));  // Updated view path
    }

    public function edit($id)
    {
        $technician = User::with('maintenanceStaff')->findOrFail($id);
        $specializations = ['Electrical', 'Plumbing', 'Structural'];  
        $statuses = ['Active', 'Inactive', 'Suspended']; 
        
        return view('admin.technicians.edit', compact('technician', 'specializations', 'statuses')); 
    }

    public function update(Request $request, $id)
    {
        $technician = User::with('maintenanceStaff')->findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $technician->id,
            'email' => 'required|string|email|max:200|unique:users,email,' . $technician->id,
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number,' . $technician->id,
            'status' => 'required|in:Active,Inactive,Suspended',
            'specialization' => 'required|string|max:100',
    
        ]);

        // Update user data
        $technician->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
        ]);

        // Update technician data
        $technician->technician->update([
            'specialization' => $request->specialization,
    
        ]);

        return redirect()->route('admin.technicians.index')  // Updated route name
            ->with('success', 'Technician updated successfully.');
    }

    public function destroy($id)
    {
        $technician = User::findOrFail($id);
        
        // Soft delete if implemented, otherwise regular delete
        if (method_exists($technician, 'trashed')) {
            $technician->delete();
        } else {
            // Delete related technician record first
            $technician->technician()->delete();
            $technician->delete();
        }

        return redirect()->route('admin.technicians.index')  // Updated route name
            ->with('success', 'Technician deleted successfully.');
    }
}