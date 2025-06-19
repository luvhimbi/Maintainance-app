<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\MaintenanceStaff;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceStaff>
 */
class MaintenanceStaffFactory extends Factory
{
    protected $model = MaintenanceStaff::class;

    public function definition()
    {
        return [
            'specialization' => $this->faker->randomElement(['Electrical', 'Plumbing', 'Structural','General']),
            'availability_status' => 'Available',
            'current_workload' => $this->faker->numberBetween(0, 10),
        ];
    }
}
