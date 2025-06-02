<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'building_name' => $this->faker->word(),
            'floor_number' => $this->faker->numberBetween(1, 5),
            'room_number' => $this->faker->bothify('Room ###'),
            'description'=>$this->faker->word()
        ];
    }
}
