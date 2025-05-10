<?php

namespace Database\Seeders;

use App\Enums\Specialization;
use App\Models\User;
use App\Models\MaintenanceStaff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;


class MaintenanceStaffSeeder extends Seeder
{
    public function run()
    {
        $technicians = [
            // Electrical Technicians
            [
            'email' => 'electrician1@example.com',
            'first_name' => 'Mike',
            'last_name' => 'Johnson',
            'specialization' => Specialization::ELECTRICAL,
            'phone' => '0998877611'
            ],
            [
            'email' => 'electrician2@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'specialization' => Specialization::ELECTRICAL,
            'phone' => '0998877612'
            ],
            [
            'email' => 'electrician3@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'specialization' => Specialization::ELECTRICAL,
            'phone' => '0998877613'
            ],

            // Plumbing Technicians
            [
            'email' => 'plumber1@example.com',
            'first_name' => 'Sarah',
            'last_name' => 'Williams',
            'specialization' => Specialization::PLUMBING,
            'phone' => '0998877621'
            ],
            [
            'email' => 'plumber2@example.com',
            'first_name' => 'Emily',
            'last_name' => 'Davis',
            'specialization' => Specialization::PLUMBING,
            'phone' => '0998877622'
            ],
            [
            'email' => 'plumber3@example.com',
            'first_name' => 'Chris',
            'last_name' => 'Taylor',
            'specialization' => Specialization::PLUMBING,
            'phone' => '0998877623'
            ],

            // General Technicians
            [
            'email' => 'general_tech1@example.com',
            'first_name' => 'David',
            'last_name' => 'Brown',
            'specialization' => Specialization::GENERAL,
            'phone' => '0998877631'
            ],
            [
            'email' => 'general_tech2@example.com',
            'first_name' => 'Anna',
            'last_name' => 'White',
            'specialization' => Specialization::GENERAL,
            'phone' => '0998877632'
            ],
            [
            'email' => 'general_tech3@example.com',
            'first_name' => 'Tom',
            'last_name' => 'Green',
            'specialization' => Specialization::GENERAL,
            'phone' => '0998877633'
            ],

            // Structural Technicians
            [
            'email' => 'structural1@example.com',
            'first_name' => 'Laura',
            'last_name' => 'Hill',
            'specialization' => Specialization::STRUCTURAL,
            'phone' => '0998877641'
            ],
            [
            'email' => 'structural2@example.com',
            'first_name' => 'Mark',
            'last_name' => 'Adams',
            'specialization' => Specialization::STRUCTURAL,
            'phone' => '0998877642'
            ],
            [
            'email' => 'structural3@example.com',
            'first_name' => 'Sophia',
            'last_name' => 'Clark',
            'specialization' => Specialization::STRUCTURAL,
            'phone' => '0998877643'
            ],
        ];

        foreach ($technicians as $tech) {
            $user = User::firstOrCreate(
                ['email' => $tech['email']],
                [
                    'password_hash' => Hash::make('password'),
                    'phone_number' => $tech['phone'],
                    'user_role' => UserRole::TECHNICIAN ->value,
                    'first_name' => $tech['first_name'],
                    'last_name' => $tech['last_name'],
                    'address' => rand(1, 100) . ' Maintenance Block, Main Campus'
                    ,'created_at' => Carbon::now(), 
                    'updated_at' => Carbon::now(), 
                    ]
            );

            MaintenanceStaff::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'specialization' => $tech['specialization']->value,
                    'availability_status' => 'Available',
                    'current_workload' => 0,
                ]
            );
        }
    }
}