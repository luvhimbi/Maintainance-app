<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            AdminSeeder::class,
            MaintenanceStaffSeeder::class,
            StaffMemberSeeder::class,
            StudentSeeder::class
        ]);

    }
}
