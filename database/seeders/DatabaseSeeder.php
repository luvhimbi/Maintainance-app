<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
