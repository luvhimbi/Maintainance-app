<?php

namespace Database\Seeders;

use App\Models\CampusMember;
use App\Models\User;
use App\Models\Admin;
use App\Models\MaintenanceStaff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create 5 Students (Campus Members)
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => 'student' . $i . '@example.com'], // Using email as unique identifier
                [
                    'password_hash' => Hash::make('password'),
                    'phone_number' => '123456789' . $i,
                    'user_role' => 'Campus_Member',
                    'first_name' => 'Student',
                    'last_name' => 'User' . $i,
                    'address' => $i . ' Student Residence, Main Campus'
                ]
            );

            CampusMember::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'member_type' => 'Student',
                    'student_staff_id' => 'STU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'faculty_department' => ($i % 2 == 0) ? 'Faculty of Science' : 'Faculty of Arts',
                    'program_course' => ($i % 2 == 0) ? 'Computer Science' : 'Business Administration',
                    'year_of_study' => $i % 4 + 1,
                    'position_title' => null
                ]
            );
        }

        // Create 5 Staff (3 Technicians and 2 Admins)
        for ($i = 1; $i <= 5; $i++) {
            $isTechnician = $i <= 3;
            $role = $isTechnician ? 'Technician' : 'Admin';
            $staffPrefix = $isTechnician ? 'TECH' : 'ADM';
            $roleLower = strtolower($role);

            $user = User::firstOrCreate(
                ['email' => $roleLower . $i . '@example.com'], // Using email as unique identifier
                [
                    'password_hash' => Hash::make('password'),
                    'phone_number' => '98765432' . $i,
                    'user_role' => $role,
                    'first_name' => $role,
                    'last_name' => 'Staff' . $i,
                    'address' => $i . ' Staff Quarters, Main Campus'
                ]
            );



            if ($isTechnician) {
                MaintenanceStaff::firstOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'specialization' => ['General', 'Electrical', 'Structural', 'Plumbing', 'Furniture', 'Other'][$i-1],
                        'availability_status' => 'Available',
                        'current_workload' => 0,
                    ]
                );
            } else {
                Admin::firstOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'department' => ($i == 4) ? 'Academic Affairs' : 'Student Services',
                    ]
                );
            }
        }
    }
}
