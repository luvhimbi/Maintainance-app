<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StaffMember;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Enums\UserRole;
use Carbon\Carbon;
class StaffMemberSeeder extends Seeder
{
    public function run()
    {
        $staffMembers = [
            [
            'email' => 'registrar1@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'department' => 'Registrar Office',
            'position' => 'Senior Coordinator'
            ],
            [
            'email' => 'academic1@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'department' => 'Academic Affairs',
            'position' => 'Department Head'
            ],
            [
            'email' => 'lecturer1@example.com',
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'department' => 'Computer Science',
            'position' => 'Lecturer'
            ],
            [
            'email' => 'finance1@example.com',
            'first_name' => 'Robert',
            'last_name' => 'Brown',
            'department' => 'Finance',
            'position' => 'Accountant'
            ],
            [
            'email' => 'hr1@example.com',
            'first_name' => 'Emily',
            'last_name' => 'Davis',
            'department' => 'Human Resources',
            'position' => 'HR Manager'
            ],
            [
            'email' => 'it1@example.com',
            'first_name' => 'Michael',
            'last_name' => 'Wilson',
            'department' => 'IT Support',
            'position' => 'System Administrator'
            ],
        ];

        $phoneCounter = 100; // Initialize a counter for unique phone numbers

        foreach ($staffMembers as $staff) {
            $user = User::firstOrCreate(
                ['email' => $staff['email']],
                [
                    'password_hash' => Hash::make('password'),
                    'phone_number' => '01122334' . $phoneCounter++, // Increment counter for unique phone numbers
                    'user_role' => UserRole::STAFF->value,
                    'first_name' => $staff['first_name'],
                    'last_name' => $staff['last_name'],
                    'address' => rand(1, 100) . ' Admin Building, Main Campus',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

            StaffMember::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'department' => $staff['department'],
                    'position_title' => $staff['position'],
                ]
            );
        }
    }
}