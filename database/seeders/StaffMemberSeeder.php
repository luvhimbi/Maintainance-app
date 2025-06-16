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
            'email' => '230747598@tut4life.ac.za',
            'first_name' => 'Siyamthanda',
            'last_name' => 'Khumalo',
            'department' => 'Registrar Office',
            'position' => 'Senior Coordinator'
            ],
            [
            'email' => '224688350@tut4life.ac.za',
            'first_name' => 'Samkelo',
            'last_name' => 'Mavuso',
            'department' => 'Academic Affairs',
            'position' => 'Department Head'
            ],
            [
            'email' => '223922104@tut4life.ac.za',
            'first_name' => 'Mlondile',
            'last_name' => 'Mthembu',
            'department' => 'Computer Science',
            'position' => 'Lecturer'
            ],
            [
            'email' => '223691226@tut4life.ac.za',
            'first_name' => 'Fortunate',
            'last_name' => 'Malatji',
            'department' => 'Finance',
            'position' => 'Accountant'
            ],
            [
            'email' => '230000000@tut4life.ac.za',
            'first_name' => 'Emily',
            'last_name' => 'Davis',
            'department' => 'Human Resources',
            'position' => 'HR Manager'
            ],
            [
            'email' => '234567868@tut4life.ac.za',
            'first_name' => 'Mukovhe',
            'last_name' => 'Mamagau',
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
                    'phone_number' => '01122334' . $phoneCounter++,
                    'user_role' => UserRole::STAFF->value,
                    'first_name' => $staff['first_name'],
                    'last_name' => $staff['last_name'],
                    'address' => rand(1, 100) . ' Admin Building, sosh south Campus',
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
