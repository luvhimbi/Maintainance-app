<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Specialization;
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
        $specializations = array_column(Specialization::cases(), 'value');
        return view('admin.technicians.create', compact('specializations'));
    }
    public function showTech($id)
    {
        // Find the user by user_id and ensure they have the 'Technician' role.
        // Eager load the maintenanceStaff relationship for technician-specific details.
        $technician = User::where('user_id', $id)
            ->where('user_role', 'Technician')
            ->with('maintenanceStaff') // Eager load the relationship
            ->firstOrFail(); // Throws 404 if not found or not a technician

        return view('admin.technicians.show_technician', compact('technician'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'email' => 'required|string|email|max:200|unique:users,email', // <-- Ensure email is unique
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // <-- CORRECTED: Use 'password' for validation
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                // Make phone number unique only if provided (not null)
                // Assuming 'phone_number' is the column name in the 'users' table
                Rule::unique('users', 'phone_number')->where(function ($query) {
                    return $query->whereNotNull('phone_number');
                }),
            ],
            'specialization' => [
                'required',
                'string',
                'max:100',
                // Validate that the specialization is one of the allowed enum values-
                Rule::in(array_column(Specialization::cases(), 'value')),
            ],
        ]);

        // Create the User record
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'user_role' => 'Technician',
            'address' => $validated['address'],
        ]);

        // Create the associated MaintenanceStaff record
        MaintenanceStaff::create([
            'user_id' => $user->user_id, // Assuming 'user_id' is the primary key on User model
            'specialization' => $validated['specialization'],
            'availability_status' => 'Available', // Default status for new technicians
            'current_workload' => 0 // Default workload for new technicians
        ]);

        // Notify all admins about the new technician
        $admins = User::where('user_role', 'Admin')->get();
        $message = "New technician registered: {$user->first_name} {$user->last_name}";
        // Ensure 'admin.technicians.show' route exists and expects 'user_id'
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
        $specializations = array_column(Specialization::cases(), 'value');
       return view('admin.technicians.edit', compact('technician', 'specializations'));
    }

    public function update(Request $request, $id)
    {
        // Find the technician user, including their maintenanceStaff relationship
        $technician = User::with('maintenanceStaff')->findOrFail($id);

        // Validate the incoming request data
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:200',
                // Ensure email is unique across users, but ignore the current technician's email.
                // Assuming 'user_id' is the primary key column name on the 'users' table,
                // and 'email' is the column to check uniqueness against.
                Rule::unique('users', 'email')->ignore($technician->user_id, 'user_id'),
            ],
            'phone_number' => 'nullable|string|max:20',
            'specialization' => [
                'required',
                'string',
                'max:100',

                Rule::in(array_column(Specialization::cases(), 'value')),
            ],
        ]);

        // Capture the original data for comparison BEFORE updating
        $originalUserData = $technician->only(['first_name', 'last_name', 'address', 'email', 'phone_number']);
        $originalSpecialization = $technician->maintenanceStaff ? $technician->maintenanceStaff->specialization : null;

        // --- Update User Data ---
        $technician->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        // --- Update Maintenance Staff Data ---
        // Ensure the maintenanceStaff record exists or create it if it doesn't
        if ($technician->maintenanceStaff) {
            $technician->maintenanceStaff->update([
                'specialization' => $request->specialization, // Correctly use lowercase 's' for request input
            ]);
        } else {
            // If a technician somehow doesn't have a maintenanceStaff record, create it.
            // This might happen if user roles change or data was manually inserted.
            $technician->maintenanceStaff()->create([
                'specialization' => $request->specialization,
                // You might need to add other default fields for maintenanceStaff if they are non-nullable
                // e.g., 'availability_status' => 'Available', 'current_workload' => 0,
            ]);
        }

        // --- Prepare details of changes for notification ---
        $changes = [];
        // Reload the technician and its relation AFTER update to get the latest values for comparison
        $technician->refresh();
        $technician->load('maintenanceStaff'); // Ensure maintenanceStaff is reloaded

        foreach ($originalUserData as $key => $value) {
            if ($value !== $technician->$key) {
                // Convert snake_case key to readable format for the message
                $changes[] = ucfirst(str_replace('_', ' ', $key)) . " was changed from '{$value}' to '{$technician->$key}'.";
            }
        }

        // Compare original specialization with the new one
        if ($originalSpecialization !== ($technician->maintenanceStaff->specialization ?? null)) {
            $changes[] = "Specialization was changed from '" . ($originalSpecialization ?? 'N/A') . "' to '" . ($technician->maintenanceStaff->specialization ?? 'N/A') . "'.";
        }

        // --- Notify the technician about the update ---
        if (!empty($changes)) {
            $message = "Your profile data has been updated by an admin. Changes: " . implode(' ', $changes);
            // Ensure 'techProfile' route exists and expects 'user_id'
            $actionUrl = route('techProfile', $technician->user_id);

            Notification::send($technician, new DatabaseNotification($message, $actionUrl));
        }

        // Redirect with success message
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
