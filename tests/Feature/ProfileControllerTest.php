<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function student_profile_page_loads()
    {
        $user = User::factory()->create(['user_role' => 'Student']);
        $this->actingAs($user);

        $response = $this->get(route('profile'));
        $response->assertStatus(200)
            ->assertViewIs('Student.profile')
            ->assertViewHas('user');
    }

    /** @test */
    public function technician_profile_page_loads()
    {
        $user = User::factory()->create(['user_role' => 'Technician']);
        
        // Create the technician record
        DB::table('Technicians')->insert([
            'user_id' => $user->user_id,
            'specialization' => 'General',
            'availability_status' => 'Available',
            'current_workload' => 0
        ]);

        $this->actingAs($user);

        $response = $this->get(route('techProfile'));
        $response->assertStatus(200)
            ->assertViewIs('Technician.profile')
            ->assertViewHas('user');
    }

    /** @test */
    public function admin_profile_page_loads()
    {
        $user = User::factory()->create(['user_role' => 'Admin']);
        Admin::factory()->create(['user_id' => $user->user_id]);
        $this->actingAs($user);

        $response = $this->get(route('adminProfile'));
        $response->assertStatus(200)
            ->assertViewIs('Admin.profile')
            ->assertViewHas('user');
    }

    /** @test */
    public function student_can_update_profile_and_gets_notification()
    {
        NotificationFacade::fake();
        $user = User::factory()->create(['user_role' => 'Student']);
        $this->actingAs($user);

        $response = $this->post(route('profile.update'), [
            'first_name' => 'NewFirst',
            'last_name' => 'NewLast',
            'email' => 'newemail@example.com',
            'phone_number' => '1234567890',
            'address' => 'New Address',
        ]);

        $response->assertRedirect(route('test.profile.edit'));
        $this->assertDatabaseHas('users', ['email' => 'newemail@example.com']);
        NotificationFacade::assertSentTo($user, \App\Notifications\DatabaseNotification::class);
    }

    /** @test */
    public function technician_update_requires_changes()
    {
        NotificationFacade::fake();
        $user = User::factory()->create(['user_role' => 'Technician']);
        
        // Create the technician record
        DB::table('Technicians')->insert([
            'user_id' => $user->user_id,
            'specialization' => 'General',
            'availability_status' => 'Available',
            'current_workload' => 0
        ]);

        $this->actingAs($user);

        $response = $this->post(route('tech_profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
        ]);

        $response->assertRedirect(route('tech_edit'));
        $response->assertSessionHas('info');
        NotificationFacade::assertNothingSent();
    }

    /** @test */
    public function admin_update_requires_changes()
    {
        NotificationFacade::fake();
        $user = User::factory()->create(['user_role' => 'Admin']);

        $this->actingAs($user);

        $response = $this->post(route('admin_profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
        ]);

        $response->assertRedirect(route('adminEdit'));
        $response->assertSessionHas('info');
        NotificationFacade::assertNothingSent();
    }

    /** @test */
    public function password_update_requires_current_password()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->put(route('profile.updatePassword'), [
            'current_password' => 'wrong-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    /** @test */
    public function password_update_successfully_changes_password_and_logs_out()
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('oldpassword')
        ]);
        $this->actingAs($user);

        $response = $this->put(route('profile.updatePassword'), [
            'current_password' => 'oldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertTrue(session()->has('password_changed'));
    }

    
}
