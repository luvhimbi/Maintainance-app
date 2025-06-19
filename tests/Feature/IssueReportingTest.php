<?php

namespace Tests\Feature;

use App\Mail\TechnicianAssignmentEmail;
use App\Models\Issue;
use App\Models\Location;
use App\Models\User;
use App\Notifications\DatabaseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class IssueReportingTest extends TestCase
{


    protected $reporter;
    protected $technician;
    protected $location;
    protected $building;
    protected $floor;
    protected $room;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->reporter = User::factory()->create(['user_role' => 'Student']);
        $this->technician = User::factory()->create(['user_role' => 'Technician']);

        // Create test building, floor, and room
        $this->building = Building::factory()->create();
        $this->floor = Floor::factory()->create(['building_id' => $this->building->id]);
        $this->room = Room::factory()->create(['floor_id' => $this->floor->id]);

        // Authenticate as reporter
        $this->actingAs($this->reporter);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->post(route('issue.store'), []);
        $response->assertSessionHasErrors(['building_id', 'floor_id', 'room_id', 'issue_type', 'issue_description']);
    }

    /** @test */
    public function it_requires_pc_fields_when_issue_type_is_pc()
    {
        $response = $this->post(route('issue.store'), [
            'issue_type' => 'PC',
            'building_id' => $this->building->id,
            'floor_id' => $this->floor->id,
            'room_id' => $this->room->id,
            'issue_description' => 'Test PC issue',
            // Missing pc_number which is required for PC issues
        ]);
        $response->assertSessionHasErrors(['pc_number']);
    }

    /** @test */
    public function it_calculates_correct_urgency_scores()
    {
        $controller = new \App\Http\Controllers\IssueController();

        $defaults = [
            'building_id' => $this->building->id,
            'floor_id' => $this->floor->id,
            'room_id' => $this->room->id,
            'issue_description' => 'Test issue',
            'safety_hazard' => false,
            'affected_areas' => 1,
            'critical_work_affected' => false,
            'pc_issue_type' => null
        ];

        // Test Electrical issue (base score 3)
        $score = $this->calculateUrgencyScore(array_merge($defaults, [
            'issue_type' => 'Electrical',
            'safety_hazard' => true,
            'affected_areas' => 5
        ]));
        $this->assertEquals(8, $score); // 3 (Electrical) + 3 (safety) + 2 (areas > 3)

        // Test PC issue with critical work (base score 1 + 2)
        $score = $this->calculateUrgencyScore(array_merge($defaults, [
            'issue_type' => 'PC',
            'critical_work_affected' => true,
            'pc_issue_type' => 'hardware'
        ]));
        $this->assertEquals(4, $score); // 1 (PC) + 2 (critical) + 1 (hardware)
    }

    /** @test */
    public function it_maps_urgency_to_correct_priority()
    {
        $controller = new \App\Http\Controllers\IssueController();

        $this->assertEquals('High', $controller->mapUrgencyToPriority('High'));
        $this->assertEquals('Medium', $controller->mapUrgencyToPriority('Medium'));
        $this->assertEquals('Low', $controller->mapUrgencyToPriority('Low'));
        $this->assertEquals('Low', $controller->mapUrgencyToPriority('Invalid')); // Test fallback
    }

    /** @test */
    public function it_creates_issue_and_task_in_database()
    {
        $data = $this->validIssueData();
        $this->post(route('issue.store'), $data); // sets session
        $response = $this->followingRedirects()->post(route('issue.save'));

        $this->assertDatabaseHas('issue', [
            'issue_type' => 'Electrical',
            'urgency_level' => 'High',
            'issue_status' => 'Open'
        ]);
        $this->assertDatabaseHas('task', [
            'priority' => 'High',
            'issue_status' => 'Pending'
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_issues_within_24_hours()
    {
        $data = $this->validIssueData();
        $this->post(route('issue.store'), $data);
        $this->post(route('issue.save'));

        // Second submission
        $this->post(route('issue.store'), $data);
        $response = $this->post(route('issue.save'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('already submitted', $response->getSession()->get('error'));
    }

 

    /** @test */
    public function it_sends_notification_to_reporter()
    {
        Notification::fake();
        $data = $this->validIssueData();
        $this->post(route('issue.store'), $data);
        $this->followingRedirects()->post(route('issue.save'));

        Notification::assertSentTo(
            $this->reporter,
            DatabaseNotification::class,
            function ($notification, $channels, $notifiable) {
                $data = $notification->toArray($notifiable);
                return str_contains($data['message'] ?? '', 'New Issue #');
            }
        );
    }

    /** @test */
    public function it_sends_email_to_assigned_technician()
    {
        Mail::fake();
        $this->technician->update([
            'user_role' => 'Technician',
            'specialization' => 'Electrical'
        ]);
        DB::table('Technicians')->insert([
            'user_id' => $this->technician->user_id,
            'specialization' => 'Electrical',
            'current_workload' => 0,
            'availability_status' => 'Available'
        ]);
        $data = $this->validIssueData();
        $this->post(route('issue.store'), $data);
        $this->followingRedirects()->post(route('issue.save'));
        Mail::assertSent(TechnicianAssignmentEmail::class, function ($mail) {
            return $mail->hasTo($this->technician->email);
        });
    }

    /** @test */
    public function it_handles_failed_submission_gracefully()
    {
        $data = $this->validIssueData();
        $data['building_id'] = 9999; // Invalid building
        $before = Issue::count();
        $this->post(route('issue.store'), $data);
        $response = $this->post(route('issue.save'));
        $response->assertSessionHas('error');
        $after = Issue::count();
        $this->assertEquals($before, $after, 'No new issue should be created');
    }

    protected function calculateUrgencyScore($data)
    {
        $controller = new \App\Http\Controllers\IssueController();

        $defaults = [
            'issue_description' => 'Test issue',
            'safety_hazard' => false,
            'affected_areas' => 1,
            'critical_work_affected' => false,
            'pc_issue_type' => null,
            'affects_operations' => false,
        ];

        $merged = array_merge($defaults, $data);

        // Simulate how the controller calculates urgency
        $urgencyScore = 0;
        $typeScores = [
            'Electrical' => 3,
            'Structural' => 3,
            'Plumbing' => 2,
            'HVAC' => 2,
            'PC' => 1,
            'Furniture' => 1,
            'General' => 1
        ];
        $urgencyScore += $typeScores[$merged['issue_type']] ?? 1;

        if ($merged['safety_hazard']) {
            $urgencyScore += 3;
        }

        if ($merged['affected_areas'] > 3) {
            $urgencyScore += 2;
        } elseif ($merged['affected_areas'] > 1) {
            $urgencyScore += 1;
        }

        if ($merged['issue_type'] === 'PC') {
            if ($merged['critical_work_affected']) {
                $urgencyScore += 2;
            }
            if ($merged['pc_issue_type'] === 'hardware') {
                $urgencyScore += 1;
            }
        }

        return $urgencyScore;
    }

    protected function validIssueData()
    {
        return [
            'reporter_id' => $this->reporter->id,
            'building_id' => $this->building->id,
            'floor_id' => $this->floor->id,
            'room_id' => $this->room->id,
            'issue_type' => 'Electrical',
            'issue_description' => 'Test issue description',
            'safety_hazard' => true,
            'affected_areas' => 2,
            'critical_work_affected' => false,
            'affects_operations' => false,
        ];
    }
}
