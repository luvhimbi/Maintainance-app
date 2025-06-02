<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    // use RefreshDatabase;

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
        $this->actingAs($user);

        $response = $this->post(route('tech_update'), [
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
    public function admin_can_update_profile_and_gets_notification()
    {
        NotificationFacade::fake();
        $user = User::factory()->create(['user_role' => 'Admin']);
        Admin::factory()->create(['user_id' => $user->user_id]);
        $this->actingAs($user);

        $response = $this->post(route('admin.update'), [
            'first_name' => 'AdminNew',
            'last_name' => 'AdminLast',
            'email' => 'adminnew@example.com',
            'phone_number' => '9876543210',
            'address' => 'Admin Address',
        ]);

        $response->assertRedirect(route('adminEdit'));
        $this->assertDatabaseHas('users', ['email' => 'adminnew@example.com']);
        NotificationFacade::assertSentTo($user, \App\Notifications\DatabaseNotification::class);
    }

    /** @test */
    public function password_update_requires_correct_current_password()
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('oldpassword')
        ]);
        $this->actingAs($user);

        $response = $this->post(route('profile.password.update'), [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
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

        $response = $this->post(route('profile.password.update'), [
            'current_password' => 'oldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertTrue(session()->has('password_changed'));
    }

    /** @test */
    public function bulk_destroy_notifications_works()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $notifications = DatabaseNotification::factory()->count(2)->create(['notifiable_id' => $user->user_id]);
        $ids = $notifications->pluck('id')->toArray();

        $response = $this->post(route('notifications.bulkDestroy'), [
            'notifications' => $ids,
        ]);

        $response->assertRedirect(route('notifications.index'));
        $this->assertDatabaseMissing('notifications', ['id' => $ids[0]]);
        $this->assertDatabaseMissing('notifications', ['id' => $ids[1]]);
    }
}
