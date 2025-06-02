<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\MaintenanceStaff;
use App\Models\StaffMember;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
//     use RefreshDatabase;

    // Test valid student login
    public function test_student_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'unique_student@example.com', // Use unique email
            'password_hash' => Hash::make('student123'),
            'user_role' => 'Student'
        ]);

        Student::create([
            'user_id' => $user->user_id,
            'student_number' => 'S12345', // Required field
            'course' => 'Computer Science',
            'faculty' => 'Engineering'
        ]);

        $response = $this->post('/login', [
            'email' => 'unique_student@example.com', // Match the unique email
            'password' => 'student123',
            'role' => 'Student'
        ]);

        $response->assertRedirect(route('Student.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    // Test valid staff member login
    public function test_staff_member_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'unique_staff@example.com', // Use unique email
            'password_hash' => Hash::make('staff123'),
            'user_role' => 'Staff_Member'
        ]);

        StaffMember::create([
            'user_id' => $user->user_id,
            'department' => 'IT',
            'position_title' => 'Lecturer'
        ]);

        $response = $this->post('/login', [
            'email' => 'unique_staff@example.com', // Match the unique email
            'password' => 'staff123',
            'role' => 'Staff_Member'
        ]);

        $response->assertRedirect(route('Student.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    // Test valid maintenance staff login
    public function test_maintenance_staff_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'unique_tech@example.com', // Use unique email
            'password_hash' => Hash::make('tech123'),
            'user_role' => 'Technician'
        ]);

        MaintenanceStaff::create([
            'user_id' => $user->user_id,
            'specialization' => 'Electrical',
            'availability_status' => 'Available',
            'current_workload' => 0
        ]);

        $response = $this->post('/login', [
            'email' => 'unique_tech@example.com', // Match the unique email
            'password' => 'tech123',
            'role' => 'Technician'
        ]);

        $response->assertRedirect(route('technician.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    // Test valid admin login
    public function test_admin_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'unique_admin@example.com', // Use unique email
            'password_hash' => Hash::make('admin123'),
            'user_role' => 'Admin'
        ]);

        Admin::create([
            'user_id' => $user->user_id,
            'department' => 'Administration' // Required field
        ]);

        $response = $this->post('/login', [
            'email' => 'unique_admin@example.com', // Match the unique email
            'password' => 'admin123',
            'role' => 'Admin'
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    // Test login fails with incorrect password
    public function test_login_fails_with_incorrect_password()
    {
        $user = User::factory()->create([
            'email' => 'unique_fail@example.com', // Ensure unique email
            'password_hash' => Hash::make('correctpassword'),
            'user_role' => 'Student'
        ]);

        Student::create([
            'user_id' => $user->user_id,
            'student_number' => 'S54321', // Required field
            'course' => 'Mathematics',
            'faculty' => 'Science'
        ]);

        $response = $this->post('/login', [
            'email' => 'unique_fail@example.com', // Match the unique email
            'password' => 'wrongpassword',
            'role' => 'Student'
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    // Test login fails with incorrect role
    public function test_login_fails_with_incorrect_role()
    {
        $user = User::factory()->create([
            'email' => 'unique_role_fail@example.com', // Ensure unique email
            'password_hash' => Hash::make('password'),
            'user_role' => 'Student'
        ]);

        Student::create([
            'user_id' => $user->user_id,
            'student_number' => 'S67890', // Required field
            'course' => 'Physics',
            'faculty' => 'Science'
        ]);

        $response = $this->post('/login', [
            'email' => 'unique_role_fail@example.com', // Match the unique email
            'password' => 'password',
            'role' => 'Admin'
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }

    // Test validation errors when fields are missing
    public function test_login_requires_email_password_and_role()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password', 'role']);
    }

    // Test login fails with non-existent email
    public function test_login_fails_with_nonexistent_email()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
            'role' => 'Student'
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // Test authenticated users are redirected from login page
    public function test_authenticated_users_are_redirected_from_login()
    {
        $user = User::factory()->create([
            'email' => 'unique_redirect@example.com', // Ensure unique email
            'user_role' => 'Student'
        ]);

        Student::create([
            'user_id' => $user->user_id,
            'student_number' => 'S98765', // Required field
            'course' => 'Biology',
            'faculty' => 'Science'
        ]);

        Auth::login($user);

        $response = $this->get('/login');
        $response->assertRedirect(route('Student.dashboard'));
    }

}
