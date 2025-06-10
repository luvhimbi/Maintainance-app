<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Admin;


class AdminSeeder extends Seeder
{
    public function run()
    {
        $admins = [
            [
            'email' => '223579752@tut4life.ac.za',
            'first_name' => 'Munzhedzi',
            'last_name' => 'Munyadziwa',
            'phone' => '01122334455',
            'department' => 'Administration',
            ],
            [
            'email' => '224545657@tut4life.ac.za',
            'first_name' => 'Boitumelo',
            'last_name' => 'Mahlangu',
            'phone' => '01122334466',
            'department' => 'Faculty Affairs',
            ],
            [
            'email' => 'registrar@university.edu',
            'first_name' => 'Registrar',
            'last_name' => 'Office',
            'phone' => '01122334477',
            'department' => 'Registrar',
            ],
            [
            'email' => 'finance@university.edu',
            'first_name' => 'Finance',
            'last_name' => 'Manager',
            'phone' => '01122334488',
            'department' => 'Finance',
            ],
            [
            'email' => 'it@university.edu',
            'first_name' => 'IT',
            'last_name' => 'Support',
            'phone' => '01122334499',
            'department' => 'IT Support',
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'password_hash' => Hash::make('admin123'),
                    'phone_number' => $admin['phone'],
                    'user_role' => UserRole::ADMIN->value,
                    'first_name' => $admin['first_name'],
                    'last_name' => $admin['last_name'],
                    'address' => 'Administration Building, Main Campus',
                    'created_at' => Carbon::now(), // Account created 1 year ago
                    'updated_at' => Carbon::now(), // Last updated 1 month ago
                ]
            );

             Admin::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'department' => $admin['department'],

                ]
            );
    }
    }
}
