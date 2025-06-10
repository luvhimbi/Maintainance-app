<?php
namespace  Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $students = [
            [
                'email' => '224551100@tut4life.ac.za',
                'first_name' => 'Olerato',
                'last_name' => 'Moeko',
                'course' => 'Computer Science',
                'faculty' => 'ICT',
            ],
            [
                'email' => '221857887@tut4life.ac.zAT',
                'first_name' => 'Brilliant',
                'last_name' => 'Matlala',
                'course' => 'Business Administration',
                'faculty' => 'Business',
            ],
            [
                'email' => 'student3@university.edu',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'course' => 'Mechanical Engineering',
                'faculty' => 'Engineering',
            ],
            [
                'email' => 'student4@university.edu',
                'first_name' => 'Emma',
                'last_name' => 'Brown',
                'course' => 'Psychology',
                'faculty' => 'Social Sciences',
            ],
            [
                'email' => 'student5@university.edu',
                'first_name' => 'Liam',
                'last_name' => 'Smith',
                'course' => 'Mathematics',
                'faculty' => 'Sciences',
            ],
            [
                'email' => 'student6@university.edu',
                'first_name' => 'Sophia',
                'last_name' => 'Williams',
                'course' => 'Architecture',
                'faculty' => 'Design',
            ],
            [
                'email' => 'student7@university.edu',
                'first_name' => 'James',
                'last_name' => 'Taylor',
                'course' => 'Law',
                'faculty' => 'Law',
            ],
        ];

        $phoneCounter = 1000; // Initialize a counter for unique phone numbers

        foreach ($students as $student) {
            $user = User::firstOrCreate(
                ['email' => $student['email']],
                [
                    'password_hash' => Hash::make('student123'),
                    'phone_number' => '0112233' . $phoneCounter++, // Generate unique phone numbers
                    'user_role' => UserRole::STUDENT->value,
                    'first_name' => $student['first_name'],
                    'last_name' => $student['last_name'],
                    'address' => 'Student Dormitory, Main Campus',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

            Student::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'student_number' => 'ST' . rand(100000, 999999),
                    'course' => $student['course'],
                    'faculty' => $student['faculty']
                ]
            );
        }
    }
}
