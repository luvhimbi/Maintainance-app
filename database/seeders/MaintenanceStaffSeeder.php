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
            'email' => '222118050@tut4life.ac.za',
            'first_name' => 'Lindiwe',
            'last_name' => 'Sekgala',
            'specialization' => Specialization::ELECTRICAL,
            'phone' => '0998877611'
            ],
            [
            'email' => '231312137@tut4life.ac.za',
            'first_name' => 'Kgothatso',
            'last_name' => 'Moyo',
            'specialization' => Specialization::ELECTRICAL,
            'phone' => '0998877612'
            ],
            [
            'email' => '22145678@tut4life.ac.za',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'specialization' => Specialization::ELECTRICAL,
            'phone' => '0998877613'
            ],

            // Plumbing Technicians
            [
            'email' => '267890467@tut4life.ac.za',
            'first_name' => 'Sarah',
            'last_name' => 'Williams',
            'specialization' => Specialization::PLUMBING,
            'phone' => '0998877621'
            ],
            [
            'email' => '23456678@tut4life.ac.za',
            'first_name' => 'Emily',
            'last_name' => 'Davis',
            'specialization' => Specialization::PLUMBING,
            'phone' => '0998877622'
            ],
            [
            'email' => '245675678@tut4life.ac.za',
            'first_name' => 'Chris',
            'last_name' => 'Taylor',
            'specialization' => Specialization::PLUMBING,
            'phone' => '0998877623'
            ],

            // General Technicians
            [
            'email' => 'davidbrown@tut.ac.za',
            'first_name' => 'David',
            'last_name' => 'Brown',
            'specialization' => Specialization::GENERAL,
            'phone' => '0998877631'
            ],
            [
            'email' => 'annawhite@tut.ac.za',
            'first_name' => 'Anna',
            'last_name' => 'White',
            'specialization' => Specialization::GENERAL,
            'phone' => '0998877632'
            ],
            [
            'email' => 'tomgreen@tut.ac.za',
            'first_name' => 'Tom',
            'last_name' => 'Green',
            'specialization' => Specialization::GENERAL,
            'phone' => '0998877633'
            ],

            // Structural Technicians
            [
            'email' => 'laurahill@tut.ac.za',
            'first_name' => 'Laura',
            'last_name' => 'Hill',
            'specialization' => Specialization::STRUCTURAL,
            'phone' => '0998877641'
            ],
            [
            'email' => 'markadams@tut.ac.za',
            'first_name' => 'Mark',
            'last_name' => 'Adams',
            'specialization' => Specialization::STRUCTURAL,
            'phone' => '0998877642'
            ],
            [
            'email' => 'sophiaclark@tut.ac.za',
            'first_name' => 'Sophia',
            'last_name' => 'Clark',
            'specialization' => Specialization::STRUCTURAL,
            'phone' => '0998877643'
            ],

            // PC Technicians
            [
                'email' => 'peternguyen@tut.ac.za',
                'first_name' => 'Peter',
                'last_name' => 'Nguyen',
                'specialization' => Specialization::PC,
                'phone' => '0998877651'
            ],
            [
                'email' => 'lindamartinez@tut.ac.za',
                'first_name' => 'Linda',
                'last_name' => 'Martinez',
                'specialization' => Specialization::PC,
                'phone' => '0998877652'
            ],

            // HVAC Technicians
            [
                'email' => 'carloslopez@tut.ac.za',
                'first_name' => 'Carlos',
                'last_name' => 'Lopez',
                'specialization' => Specialization::HVAC,
                'phone' => '0998877661'
            ],
            [
                'email' => 'gracekim@tut.ac.za',
                'first_name' => 'Grace',
                'last_name' => 'Kim',
                'specialization' => Specialization::HVAC,
                'phone' => '0998877662'
            ],

            // Furniture Technicians
            [
                'email' => 'samuelwright@tut.ac.za',
                'first_name' => 'Samuel',
                'last_name' => 'Wright',
                'specialization' => Specialization::FURNITURE,
                'phone' => '0998877671'
            ],
            [
                'email' => 'oliviabaker@tut.ac.za',
                'first_name' => 'Olivia',
                'last_name' => 'Baker',
                'specialization' => Specialization::FURNITURE,
                'phone' => '0998877672'
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
