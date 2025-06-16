<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition(): array
    {
        return [
            'issue_id' => Issue::factory(),
            'user_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comments' => $this->faker->optional(0.8)->paragraph(), // 80% chance of having comments
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Indicate that the feedback is positive (rating 4-5)
     */
    public function positive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(4, 5),
                'comments' => $this->faker->optional(0.9)->paragraph(),
            ];
        });
    }

    /**
     * Indicate that the feedback is negative (rating 1-2)
     */
    public function negative(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(1, 2),
                'comments' => $this->faker->optional(0.9)->paragraph(),
            ];
        });
    }

    /**
     * Indicate that the feedback is neutral (rating 3)
     */
    public function neutral(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => 3,
                'comments' => $this->faker->optional(0.7)->paragraph(),
            ];
        });
    }

    /**
     * Indicate that the feedback has no comments
     */
    public function withoutComments(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'comments' => null,
            ];
        });
    }
} 