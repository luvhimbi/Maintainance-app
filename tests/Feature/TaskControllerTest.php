<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Issue;
use App\Models\TaskUpdate;
use App\Models\MaintenanceStaff;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $technician;
    protected $student;
    protected $building;
    protected $floor;
    protected $room;
    protected $issue;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users with proper roles
        $this->admin = User::factory()->create([
            'user_role' => 'Admin'
        ]);
        
        $this->technician = User::factory()->create([
            'user_role' => 'Technician'
        ]);
        
        $this->student = User::factory()->create([
            'user_role' => 'Student'
        ]);

        // Create admin record
        DB::table('admin')->insert([
            'user_id' => $this->admin->user_id,
            'department' => 'Maintenance'
        ]);

        // Create technician record
        DB::table('Technicians')->insert([
            'user_id' => $this->technician->user_id,
            'specialization' => 'General',
            'availability_status' => 'Available',
            'current_workload' => 0
        ]);

        // Create location hierarchy
        $this->building = Building::create(['building_name' => 'Test Building']);
        $this->floor = Floor::create(['floor_number' => '1', 'building_id' => $this->building->id]);
        $this->room = Room::create(['room_number' => '101', 'floor_id' => $this->floor->id]);

        // Create test issue
        $this->issue = Issue::create([
            'reporter_id' => $this->student->id,
            'issue_type' => 'Test Issue',
            'issue_description' => 'Test Description',
            'issue_status' => 'Open',
            'building_id' => $this->building->id,
            'floor_id' => $this->floor->id,
            'room_id' => $this->room->id,
            'urgency_level' => 'Low',
            'urgency_score' => 0,
            'safety_hazard' => false,
            'affects_operations' => false,
            'affected_areas' => 1
        ]);

        // Create test task with all required fields
        $this->task = Task::create([
            'issue_id' => $this->issue->issue_id,
            'assignee_id' => $this->technician->user_id,
            'admin_id' => $this->admin->user_id,
            'assignment_date' => now(),
            'expected_completion' => now()->addDays(2),
            'issue_status' => 'Pending',
            'priority' => 'Medium'
        ]);
    }

    /** @test */
    public function admin_can_view_tasks()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.view'));

        $response->assertStatus(200)
            ->assertViewIs('admin.tasks.view')
            ->assertViewHas(['tasks', 'overdueCount']);
    }

    /** @test */
    public function technician_can_view_update_form()
    {
        $response = $this->actingAs($this->technician)
            ->get(route('tasks.update.form', $this->task->task_id));

        $response->assertStatus(200)
            ->assertViewIs('technician.task_update_form')
            ->assertViewHas('task');
    }

    /** @test */
    public function technician_can_update_task()
    {
        Notification::fake();
        Mail::fake();

        // Ensure the task is in a state that can be updated
        $this->task->update([
            'issue_status' => 'Pending',
            'assignee_id' => $this->technician->user_id
        ]);

        // Ensure the issue is properly linked
        $this->issue->update([
            'reporter_id' => $this->student->user_id
        ]);

        $response = $this->actingAs($this->technician)
            ->put(route('tasks.update', $this->task->task_id), [
                'status' => 'In Progress',
                'update_description' => 'Test update'
            ]);

        $response->assertRedirect(route('tasks.update.form', $this->task->task_id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('task', [
            'task_id' => $this->task->task_id,
            'issue_status' => 'In Progress'
        ]);

        $this->assertDatabaseHas('task_update', [
            'task_id' => $this->task->task_id,
            'staff_id' => $this->technician->user_id,
            'update_description' => 'Test update',
            'status_change' => 'In Progress'
        ]);

        // Verify the notification was sent to the student
        Notification::assertSentTo(
            $this->student,
            \App\Notifications\DatabaseNotification::class
        );
    }

    /** @test */
    public function technician_can_view_completed_tasks()
    {
        $this->task->update(['issue_status' => 'Completed']);

        $response = $this->actingAs($this->technician)
            ->get(route('completed.tasks'));

        $response->assertStatus(200)
            ->assertViewIs('technician.completed_tasks')
            ->assertViewHas('completedTasks');
    }

    /** @test */
    public function technician_can_view_task_updates()
    {
        $response = $this->actingAs($this->technician)
            ->get(route('tasks.updates', $this->task->task_id));

        $response->assertStatus(200)
            ->assertViewIs('technician.task_updates')
            ->assertViewHas('task');
    }

    /** @test */
    public function admin_can_send_reminder_for_overdue_task()
    {
        Notification::fake();
        Mail::fake();

        $this->task->update([
            'expected_completion' => now()->subDay(),
            'issue_status' => 'Pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.sendReminder', $this->task->task_id));

        $response->assertJson([
            'success' => true,
            'message' => 'Reminder sent to technician.'
        ]);

        Notification::assertSentTo(
            $this->technician,
            \App\Notifications\DatabaseNotification::class
        );
    }

    /** @test */
    public function admin_can_reassign_overdue_task()
    {
        Notification::fake();
        Mail::fake();

        // Create a new technician to be assigned
        $newTechnician = User::factory()->create([
            'user_role' => 'Technician',
            'first_name' => 'New',
            'last_name' => 'Technician'
        ]);

        // Ensure the new technician is properly set up in the Technicians table
        DB::table('Technicians')->insert([
            'user_id' => $newTechnician->user_id,
            'specialization' => 'General',
            'availability_status' => 'Available',
            'current_workload' => 0
        ]);

        // Make sure the task is overdue and not completed
        $this->task->update([
            'expected_completion' => now()->subDay(),
            'issue_status' => 'Pending',
            'assignee_id' => $this->technician->user_id
        ]);

        // Make sure the issue is properly linked and has the correct type
        $this->issue->update([
            'reporter_id' => $this->student->user_id,
            'issue_type' => 'General'  // Match the technician's specialization
        ]);

        // Ensure the current technician has a workload
        DB::table('Technicians')
            ->where('user_id', $this->technician->user_id)
            ->update(['current_workload' => 1]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.reassign', $this->task->task_id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task reassigned to a new technician and expected completion extended.'
            ]);

        $this->assertDatabaseHas('task', [
            'task_id' => $this->task->task_id,
            'expected_completion' => now()->addDays(2)->format('Y-m-d H:i:s')
        ]);

        // Verify the notification was sent to the student
        Notification::assertSentTo(
            $this->student,
            \App\Notifications\DatabaseNotification::class
        );
    }

    /** @test */
    public function cannot_reassign_completed_task()
    {
        $this->task->update(['issue_status' => 'Completed']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.reassign', $this->task->task_id));

        $response->assertJson([
            'message' => 'Task is not overdue or already completed.'
        ]);
    }

    /** @test */
    public function cannot_reassign_task_that_is_not_overdue()
    {
        $this->task->update([
            'expected_completion' => now()->addDay(),
            'issue_status' => 'Pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.reassign', $this->task->task_id));

        $response->assertJson([
            'message' => 'Task is not overdue or already completed.'
        ]);
    }
} 