<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Floor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Building>
 */
class FloorFactory extends Factory
{
    protected $model = Floor::class;

    public function definition()
    {
        return [
            // Create a related building first
            'building_id' => Building::factory(),
            'floor_number' => $this->faker->numberBetween(1, 10),
        ];
    }

}
