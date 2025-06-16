<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Create buildings with their coordinates
        $buildings = [
            ['building_name' => 'TUT FM', 'latitude' => -25.540893498512094, 'longitude' => 28.094742888932608],
            ['building_name' => 'Ruth first', 'latitude' => -25.541752786463565, 'longitude' => 28.095702362301438],
            ['building_name' => 'Res Admin', 'latitude' => -25.540019779122517, 'longitude' => 28.094724845254145],
            ['building_name' => 'Library', 'latitude' => -25.54024242951449, 'longitude' => 28.09529883794843],
            ['building_name' => 'ICentre', 'latitude' => -25.540261790398606, 'longitude' => 28.09564216068146],
            ['building_name' => 'Building 10', 'latitude' => -25.540064007741037, 'longitude' => 28.09572390504471],
            ['building_name' => 'Building 14', 'latitude' => -25.53872113745894, 'longitude' => 28.096164064732626],
            ['building_name' => 'Building 20', 'latitude' => -25.539856711319203, 'longitude' => 28.09644374174887],
            ['building_name' => 'Cafeteria', 'latitude' => -25.541080983899782, 'longitude' => 28.0950934596016]
        ];

        // Define floor and room configurations for each building
        $buildingConfigs = [
            'TUT FM' => [
                'Ground floor' => ['office 1', '103'],
                '1' => ['office 2', 'office 3']
            ],
            'Ruth first' => [
                'Ground floor' => ['Exam centre hall'],
            ],
            'Res Admin' => [
                'Ground floor' => ['Res administrator office', 'Main office area'],

            ],
            'Library' => [
                'Ground floor' => ['print room', 'study area 1', 'study area 2', 'study area 3'],
                '1' => ['computer lab', 'study area 4', 'Librian offices'],
            ],
            'ICentre' => [
                'Ground floor' => ['computer lab'],
                '1' => ['I201', 'I202'],

            ],
            'Building 10' => [
                'Ground floor' => ['Computer Lab 138', 'Computer Lab 140', 'Computer Lab 14','Computer Lab 35','class room 1','class room 2','class room 3'],
                '1' => ['Computer Lab 30', 'Computer Lab 45'],

            ],
            'Building 14' => [
                'Ground floor' => ['Class room 34', 'class room 35', 'class room 45'],
                '1' => ['class room 105', 'class room 106', 'class room 107'],
            ],
            'Building 20' => [
                'Ground floor' => ['Lecturer office 213', 'Lecturer office 214', 'Lecturer office 215'],
                '1' => ['Lecturers Meeting room', 'Lecturer Office 216', 'Lecture office 217'],
                '2' => ['Lecturer office 218', 'receptionist Office']
            ],
            'Cafeteria' => [
                'Ground floor' => ['Dining Area 1', 'Dining Area 2'],

            ]
        ];

        foreach ($buildings as $buildingData) {
            $building = Building::create($buildingData);
            $buildingName = $buildingData['building_name'];

            // Check if we have a specific configuration for this building
            if (isset($buildingConfigs[$buildingName])) {
                // Use the specific configuration
                foreach ($buildingConfigs[$buildingName] as $floorNumber => $rooms) {
                    $floor = Floor::create([
                        'building_id' => $building->id,
                        'floor_number' => $floorNumber
                    ]);

                    foreach ($rooms as $roomNumber) {
                        Room::create([
                            'floor_id' => $floor->id,
                            'room_number' => $roomNumber
                        ]);
                    }
                }
            } else {
                // Use default configuration for buildings without specific config
                $floorNumbers = ['Ground floor', '1', '2', '3', '4'];
                foreach ($floorNumbers as $floorNumber) {
                    $floor = Floor::create([
                        'building_id' => $building->id,
                        'floor_number' => $floorNumber
                    ]);

                    $roomCount = rand(5, 10);
                    for ($roomNumber = 1; $roomNumber <= $roomCount; $roomNumber++) {
                        Room::create([
                            'floor_id' => $floor->id,
                            'room_number' => $roomNumber
                        ]);
                    }
                }
            }
        }
    }
}
