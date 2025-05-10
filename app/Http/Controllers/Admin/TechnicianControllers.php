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
use App\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

class TechnicianControllers extends Controller
{


    public function index()
    {
        $technicians = User::where('user_role', 'Technician')
            ->with(['maintenanceStaff'])
            ->orderBy('first_name')
            ->paginate(5);

        return view('admin.technicians.index', compact('technicians'));
    }

    public function create()
    {
        $specializations = ['Electrical', 'Structural', 'Plumbing'];
        return view('admin.technicians.create', compact('specializations'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:50',
        'last_name' => 'required|string|max:50',
        'address' => 'required|string|max:255', // Updated max length
        'email' => 'required|string|email|max:200|unique:users',
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'phone_number' => 'nullable|string|max:20|unique:users',
        'specialization' => 'required|string|max:100',
    ]);

    // Check if a technician with the same email or phone number already exists
    $existingTechnician = User::where('email', $validated['email'])
        ->orWhere('phone_number', $validated['phone_number'])
        ->where('user_role', 'Technician')
        ->first();

    if ($existingTechnician) {
        return redirect()->back()
            ->withErrors(['email' => 'A technician with this email or phone number already exists.'])
            ->withInput();
    }

    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'email' => $validated['email'],
        'password_hash' => Hash::make($validated['password']), // Fixed key name
        'phone_number' => $validated['phone_number'],
        'user_role' => 'Technician',
        'address' => $validated['address'],
    ]);

    MaintenanceStaff::create([
        'user_id' => $user->user_id,
        'specialization' => $validated['specialization'],
        'availability_status' => 'Available',
        'current_workload' => 0
    ]);

    // Notify all admins
    $admins = User::where('user_role', 'Admin')->get();
    $message = "New technician registered: {$user->first_name} {$user->last_name}";
    $actionUrl = route('admin.technicians.show', $user->user_id);

    Notification::send($admins, new DatabaseNotification($message, $actionUrl));

    return redirect()->route('admin.technicians.index')
        ->with('success', 'Technician created successfully.');
}




   public function show($id)
{
    $technician = User::with(['maintenanceStaff', 'tasks'])->findOrFail($id);
    return view('admin.technicians.show', compact('technician'));
}

    public function edit($id)
    {
        $technician = User::with('maintenanceStaff')->findOrFail($id);
        $specializations = ['General', 'Electrical', 'Plumbing', 'Structural']; // Ensure this matches the store method
       return view('admin.technicians.edit', compact('technician', 'specializations'));
    }

    public function update(Request $request, $id)
    {
        $technician = User::with('maintenanceStaff')->findOrFail($id);

        $request->validate([
        'first_name' => 'required|string|max:50',
        'last_name' => 'required|string|max:50',
        'address' => 'required|string|max:255',
        'email' => 'required|string|email|max:200',
        'phone_number' => 'nullable|string|max:20',
        'specialization' => 'required|string|max:100',
        ]);

        // Capture the original data for comparison
        $originalData = $technician->only(['first_name', 'last_name', 'address', 'email', 'phone_number']);
        $originalSpecialization = $technician->maintenanceStaff ? $technician->maintenanceStaff->Specialization : null;

        // Update user data
        $technician->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        // Update maintenance staff data
        if ($technician->maintenanceStaff) {
            $technician->maintenanceStaff->update([
                'Specialization' => $request->Specialization,
            ]);
        }

        // Prepare details of changes
        $changes = [];
        foreach ($originalData as $key => $value) {
            if ($value !== $technician->$key) {
                $changes[] = ucfirst($key) . " was changed from '{$value}' to '{$technician->$key}'.";
            }
        }
        if ($originalSpecialization !== $request->Specialization) {
            $changes[] = "Specialization was changed from '{$originalSpecialization}' to '{$request->Specialization}'.";
        }

        // Notify the technician about the update
        if (!empty($changes)) {
            $message = "Your profile data has been updated by an admin. Changes: " . implode(' ', $changes);
            $actionUrl = route('techProfile', $technician->user_id);

            Notification::send($technician, new DatabaseNotification($message, $actionUrl));
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
