<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $locations = [
            ['building_name' => 'Building 10', 'floor_number' => '1', 'room_number' => 'lab 138', 'description' => 'next to the tap on the ground floor'],
            ['building_name' => 'Building 14', 'floor_number' => '1', 'room_number' => '102', 'description' => 'Reception'],
            ['building_name' => 'Ruth first', 'floor_number' => 'Ground floor', 'room_number' => 'no rooms', 'description' => 'next to the gymanisum'],
            ['building_name' => 'Building 20', 'floor_number' => '2', 'room_number' => '202', 'description' => 'Meeting Room'],
            ['building_name' => 'Block A', 'floor_number' => '2', 'room_number' => '118', 'description' => 'at student town'],
            ['building_name' => 'Annex Building', 'floor_number' => '1', 'room_number' => '102', 'description' => 'Break Room'],
            ['building_name' => 'Annex Building', 'floor_number' => '2', 'room_number' => '201', 'description' => 'Lab'],
            ['building_name' => 'Annex Building', 'floor_number' => '2', 'room_number' => '202', 'description' => 'Workshop'],
            ['building_name' => 'East Wing', 'floor_number' => '1', 'room_number' => '101', 'description' => 'Cafeteria'],
            ['building_name' => 'East Wing', 'floor_number' => '2', 'room_number' => '201', 'description' => 'Library'],
        ];

        // Insert locations into the database
        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
