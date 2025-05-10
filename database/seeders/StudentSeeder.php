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
                'email' => 'student1@university.edu',
                'first_name' => 'Alex',
                'last_name' => 'Johnson',
                'phone' => '01122334477',
                'course' => 'Computer Science',
                'faculty' => 'ICT',
            ],
            [
                'email' => 'student2@university.edu',
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'phone' => '01122334488',
                'course' => 'Business Administration',
                'faculty' => 'Business',
            ],
            [
                'email' => 'student3@university.edu',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '01122334499',
                'course' => 'Mechanical Engineering',
                'faculty' => 'Engineering',
            ],
            [
                'email' => 'student4@university.edu',
                'first_name' => 'Emma',
                'last_name' => 'Brown',
                'phone' => '01122334500',
                'course' => 'Psychology',
                'faculty' => 'Social Sciences',
            ],
            [
                'email' => 'student5@university.edu',
                'first_name' => 'Liam',
                'last_name' => 'Smith',
                'phone' => '01122334511',
                'course' => 'Mathematics',
                'faculty' => 'Sciences',
            ],
            [
                'email' => 'student6@university.edu',
                'first_name' => 'Sophia',
                'last_name' => 'Williams',
                'phone' => '01122334522',
                'course' => 'Architecture',
                'faculty' => 'Design',
            ],
            [
                'email' => 'student7@university.edu',
                'first_name' => 'James',
                'last_name' => 'Taylor',
                'phone' => '01122334533',
                'course' => 'Law',
                'faculty' => 'Law',
            ],
        ];

        foreach ($students as $student) {
            $user = User::firstOrCreate(
                ['email' => $student['email']],
                [
                    'password_hash' => Hash::make('student123'),
                    'phone_number' => $student['phone'],
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