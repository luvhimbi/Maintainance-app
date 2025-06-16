<?php


use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeederBackup extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $locations = [
            ['building_name' => 'TUT FM', 'floor_number' => '0', 'room_number' => '0', 'description' => '', 'latitude' => -25.540893498512094, 'longitude' => 28.094742888932608],

            ['building_name' => 'TUT south Main building Entrance', 'floor_number' => '0', 'room_number' => '0', 'description' => 'Reception', 'latitude' => -25.540637081765333, 'longitude' => 28.09619991596886],

            ['building_name' => 'Ruth first', 'floor_number' => 'Ground floor', 'room_number' => 'no rooms', 'description' => 'next to the gymanisum', 'latitude' => -25.541752786463565, 'longitude' =>28.095702362301438],

            ['building_name' => 'Res Admin', 'floor_number' => 'no floors', 'room_number' => '202', 'description' => '', 'latitude' => -25.540019779122517, 'longitude' => 28.094724845254145],

            ['building_name' => 'Library', 'floor_number' => 'Ground floor', 'room_number' =>'study area', 'description' => 'at student town', 'latitude' => -25.54024242951449, 'longitude' => 28.09529883794843],

            ['building_name' => 'ICentre', 'floor_number' => 'Ground floor', 'room_number' => 'no rooms', 'description' => 'Break Room', 'latitude' => -25.540261790398606, 'longitude' => 28.09564216068146],

            ['building_name' => 'Student Town', 'floor_number' => 'Ground floor', 'room_number' => 'n/a', 'description' => 'residence', 'latitude' => -25.54250279161096, 'longitude' => 28.095261287024506],

            ['building_name' => 'Building 10', 'floor_number' => 'Ground floor', 'room_number' => 'n/a', 'description' => 'class rooms and labs', 'latitude' =>-25.540064007741037, 'longitude' =>28.09572390504471],

            ['building_name' => 'Building 14', 'floor_number' => 'Ground floor', 'room_number' => 'n/a', 'description' => 'Exam rooms', 'latitude' => -25.53872113745894, 'longitude' => 28.096164064732626],

            ['building_name' => 'Building 20', 'floor_number' => 'Ground floor', 'room_number' => 'n/a', 'description' => 'Lecture Offices', 'latitude' => -25.539856711319203, 'longitude' => 28.09644374174887],

            ['building_name' => 'Swimming pool', 'floor_number' => 'n/a', 'room_number' => 'n/a', 'description' => 'recreation area', 'latitude' => -25.541492399137248, 'longitude' => 28.094519466907336],

            ['building_name' => 'Cafeteria', 'floor_number' => 'n/a', 'room_number' => 'n/a', 'description' => 'eating area', 'latitude' => -25.541080983899782, 'longitude' => 28.0950934596016]

        ];


        // Insert locations into the database
        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
