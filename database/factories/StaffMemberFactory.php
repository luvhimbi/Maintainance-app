<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\StaffMember;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffMember>
 */
class StaffMemberFactory extends Factory
{
    protected $model = StaffMember::class;

    public function definition()
    {
        return [
            'department' => $this->faker->randomElement(['IT', 'HR', 'Finance']),
            'position_title' => $this->faker->jobTitle,
        ];
    }
}
