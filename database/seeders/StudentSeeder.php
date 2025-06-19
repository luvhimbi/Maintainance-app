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
                'email' => '221857887@tut4life.ac.za',
                'first_name' => 'Brilliant',
                'last_name' => 'Matlala',
                'course' => 'Informatics',
                'faculty' => 'ICT',
            ],
            [
                'email' => '22000000@tut4life.ac.za',
                'first_name' => 'John',
                'last_name' => 'Mapulta',
                'course' => 'Computer Science',
                'faculty' => 'ICT',
            ],
            [
                'email' => '221456789@tut4life.ac.za',
                'first_name' => 'Emma',
                'last_name' => 'Brown',
                'course' => 'Computer systems',
                'faculty' => 'ICT',
            ],
            [
                'email' => '23456789@tut4life.ac.za',
                'first_name' => 'Liam',
                'last_name' => 'Smith',
                'course' => 'Information Technology',
                'faculty' => 'ICT',
            ],
            [
                'email' => '23456789@tut4life.ac.za',
                'first_name' => 'Sophia',
                'last_name' => 'Williams',
                'course' => 'Information Technology',
                'faculty' => 'ICT',
            ],
            [
                'email' => '22678965@tut4life.ac.za',
                'first_name' => 'James',
                'last_name' => 'Taylor',
                'course' => 'Information Technology',
                'faculty' => 'ICT',
            ],
        ];

        $phoneCounter = 1000;

        foreach ($students as $student) {
            $user = User::firstOrCreate(
                ['email' => $student['email']],
                [
                    'password_hash' => Hash::make('student123'),
                    'phone_number' => '0112233' . $phoneCounter++, // Generate unique phone numbers
                    'user_role' => UserRole::STUDENT->value,
                    'first_name' => $student['first_name'],
                    'last_name' => $student['last_name'],
                    'address' => 'Student Dormitory, Sosh south Campus',
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
