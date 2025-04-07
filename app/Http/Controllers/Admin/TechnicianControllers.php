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
use App\Models\MaintenanceStaff;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
class TechnicianControllers extends Controller 
{


    public function index()
    {
        $technicians = User::where('user_role', 'Technician')
            ->with(['maintenanceStaff'])  
            ->orderBy('username')
            ->paginate(10);
            
        return view('admin.technicians.index', compact('technicians')); 
    }

    public function create()
    {
        $specializations = ['Electrical', 'Structural', 'Plumbing'];  
        return view('admin.technicians.create', compact('specializations'));  
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
        'password_hash' => Hash::make($request->password),
        'phone_number' => $request->phone_number,
        'user_role' => 'Technician',
        'status' => 'Active',
    ]);

    MaintenanceStaff::create([ 
        'user_id' => $user->user_id, 
        'specialization' => $request->specialization,
    ]);

    return redirect()->route('admin.technicians.index')
        ->with('success', 'Technician created successfully.');
}

    public function show($id)
    {
        $technician = User::with(['maintenanceStaff', 'tasks', 'issues.comments'])->findOrFail($id);
        return view('admin.technicians.show', compact('technician'));  
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
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($technician->user_id, 'user_id')
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:200',
                Rule::unique('users', 'email')->ignore($technician->user_id, 'user_id')
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone_number')->ignore($technician->user_id, 'user_id')
            ],
            'status' => 'required|in:Active,Inactive,Suspended',
            'Specialization' => 'required|string|max:100',
        ]);
    
        // Update user data
        $technician->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
        ]);
    
        // Update maintenance staff data
        if ($technician->maintenanceStaff) {
            $technician->maintenanceStaff->update([
                'Specialization' => $request->Specialization,
            ]);
        }
    
        return redirect()->route('admin.technicians.index')
            ->with('success', 'Technician updated successfully.');
    }

    public function destroy($id)
{
    $technician = User::findOrFail($id);
    
    // First delete the maintenance staff record if it exists
    if ($technician->maintenanceStaff) {
        $technician->maintenanceStaff()->delete();
    }
    
    // Then delete the user
    $technician->delete();

    return redirect()->route('admin.technicians.index')
        ->with('success', 'Technician deleted successfully.');
}
}