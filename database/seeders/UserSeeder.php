<?php

namespace Database\Seeders;
namespace Database\Seeders;

use App\Models\User; // Import the User model
use App\Models\Admin;
use App\Models\MaintenanceStaff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
         // Create 5 Students
         for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate(
                ['username' => 'student' . $i],
                [
                    'password_hash' => Hash::make('password'), // Hash the password
                    'email' => 'student' . $i . '@example.com',
                    'phone_number' => '123456789' . $i,
                    'user_role' => 'Student',
                ]
            );
        }

        // Create 5 Technicians (Maintenance Staff)
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['username' => 'technician' . $i],
                [
                    'password_hash' => Hash::make('password'), // Hash the password
                    'email' => 'technician' . $i . '@example.com',
                    'phone_number' => '987654321' . $i,
                    'user_role' => 'Technician',
                ]
            );

            // Create associated maintenance_staff record
            MaintenanceStaff::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'specialization' => 'Specialization ' . $i,
                    'availability_status' => 'Available',
                    'current_workload' => 0,
                ]
            );
        }

        // Create 5 Admins
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['username' => 'admin' . $i],
                [
                    'password_hash' => Hash::make('password'), // Hash the password
                    'email' => 'admin' . $i . '@example.com',
                    'phone_number' => '555555555' . $i,
                    'user_role' => 'Admin',
                ]
            );

            // Create associated admin record
            Admin::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'department' => 'Department ' . $i,
                ]
            );
        }
    
    }
}
