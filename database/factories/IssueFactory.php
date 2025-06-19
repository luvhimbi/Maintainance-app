<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\User;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    protected $model = Issue::class;

    public function definition(): array
    {
        $building = Building::factory()->create();
        $floor = Floor::factory()->create(['building_id' => $building->id]);
        $room = Room::factory()->create(['floor_id' => $floor->id]);

        return [
            'reporter_id' => User::factory(),
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'room_id' => $room->id,
            'issue_type' => $this->faker->randomElement(['Electrical', 'Plumbing', 'Structural', 'HVAC', 'Furniture', 'PC', 'General']),
            'issue_description' => $this->faker->paragraph(),
            'report_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'issue_status' => $this->faker->randomElement(['Open', 'In Progress', 'Resolved']),
            'urgency_level' => $this->faker->randomElement(['Low', 'Medium', 'High']),
            'urgency_score' => $this->faker->numberBetween(1, 10),
            'safety_hazard' => $this->faker->boolean(20),
            'affects_operations' => $this->faker->boolean(30),
            'affected_areas' => $this->faker->numberBetween(1, 5),
            'pc_number' => null,
            'pc_issue_type' => null,
            'critical_work_affected' => false,
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the issue is a PC-related issue
     */
    public function pcIssue(): static
    {
        return $this->state(fn (array $attributes) => [
            'issue_type' => 'PC',
            'pc_number' => $this->faker->numberBetween(1, 100),
            'pc_issue_type' => $this->faker->randomElement(['hardware', 'software', 'network', 'peripheral', 'other']),
            'critical_work_affected' => $this->faker->boolean(30),
        ]);
    }

    /**
     * Indicate that the issue is a safety hazard
     */
    public function safetyHazard(): static
    {
        return $this->state(fn (array $attributes) => [
            'safety_hazard' => true,
            'urgency_level' => 'High',
            'urgency_score' => $this->faker->numberBetween(8, 10),
        ]);
    }

    /**
     * Indicate that the issue affects operations
     */
    public function affectsOperations(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'affects_operations' => true,
                'urgency_level' => $this->faker->randomElement(['High', 'Medium']),
                'urgency_score' => $this->faker->numberBetween(5, 10),
            ];
        });
    }

    /**
     * Indicate that the issue is resolved
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'issue_status' => 'Resolved',
        ]);
    }

    /**
     * Indicate that the issue is open
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'issue_status' => 'Open',
        ]);
    }

    /**
     * Indicate that the issue is in progress
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'issue_status' => 'In Progress',
        ]);
    }
} 