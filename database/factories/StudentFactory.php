<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Student;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{


 protected $model = Student::class;

 public function definition()
 {
     return [
         'student_number' => 'S' . $this->faker->unique()->numberBetween(10000, 99999),
         'course' => $this->faker->randomElement(['Computer Science', 'Engineering', 'Business']),
         'faculty' => $this->faker->word,
     ];
 }
}
