<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Admin;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
   protected $model = Admin::class;

    public function definition()
    {
        return [
            'department' => $this->faker->randomElement(['Administration', 'Management', 'Support']),
        ];
    }
}
