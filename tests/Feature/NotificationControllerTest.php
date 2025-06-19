<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $technician;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = User::factory()->create(['user_role' => 'Student']);
        $this->technician = User::factory()->create(['user_role' => 'Technician']);
        $this->admin = User::factory()->create(['user_role' => 'Admin']);
    }

    /** @test */
    public function it_shows_notifications_for_student()
    {
        $this->actingAs($this->student);
        $response = $this->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertViewIs('Student.notifications');
    }

    /** @test */
    public function it_shows_notifications_for_technician()
    {
        $this->actingAs($this->technician);
        $response = $this->get(route('notification.index'));
        $response->assertStatus(200);
        $response->assertViewIs('Technician.notifications');
    }

    /** @test */
    public function it_shows_notifications_for_admin()
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('notify.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.notifications');
    }

    /** @test */
    public function it_shows_specific_notification_for_student()
    {
        $this->actingAs($this->student);
        
        // Create a notification with UUID
        $notification = $this->student->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\DatabaseNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->get(route('notifications.show', $notification->id));
        $response->assertStatus(200);
        $response->assertViewIs('Student.show');
    }

    /** @test */
    public function it_shows_specific_notification_for_technician()
    {
        $this->actingAs($this->technician);
        
        // Create a notification with UUID
        $notification = $this->technician->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\DatabaseNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->get(route('notifications.Techshow', $notification->id));
        $response->assertStatus(200);
        $response->assertViewIs('Technician.show');
    }

    /** @test */
    public function it_shows_specific_notification_for_admin()
    {
        $this->actingAs($this->admin);
        
        // Create a notification with UUID
        $notification = $this->admin->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\DatabaseNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->get(route('notifications.Adminshow', $notification->id));
        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
    }

    /** @test */
    public function it_marks_all_notifications_as_read()
    {
        $this->actingAs($this->student);
        
        // Create some notifications
        $this->student->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\DatabaseNotification',
            'data' => ['message' => 'Test notification 1'],
        ]);
        
        $this->student->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\DatabaseNotification',
            'data' => ['message' => 'Test notification 2'],
        ]);

        $response = $this->post(route('notifications.markAllRead'));
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_deletes_notification_for_student()
    {
        $this->actingAs($this->student);
        
        // Create a notification
        $notification = $this->student->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\DatabaseNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->delete(route('notifications.destroy', $notification->id));
        $response->assertViewIs('student.notifications');
        $response->assertViewHas('success');
    }

    /** @test */
    public function it_deletes_notification_for_technician()
    {
        $this->actingAs($this->technician);
        
        // Create a notification
        $notification = $this->technician->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\DatabaseNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->delete(route('notifications.destroy', $notification->id));
        $response->assertViewIs('technician.notifications');
        $response->assertViewHas('success');
    }

    /** @test */
    public function it_deletes_notification_for_admin()
    {
        $this->actingAs($this->admin);
        
        // Create a notification
        $notification = $this->admin->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\DatabaseNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->delete(route('notifications.destroy', $notification->id));
        $response->assertViewIs('admin.notifications');
        $response->assertViewHas('success');
    }
} 