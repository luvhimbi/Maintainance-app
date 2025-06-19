<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Building>
 */
class RoomFactory extends Factory
{

    protected $model = Room::class;
    public function definition()
    {
        return [
           'floor_id'=>Floor::factory(),
            'room_number'=>$this->faker->unique()->randomNumber(),
        ];
    }

}
