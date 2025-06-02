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

class IssueReportingTest extends TestCase
{


    protected $reporter;
    protected $technician;
    protected $location;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->reporter = User::factory()->create(['user_role' => 'Student']);
        $this->technician = User::factory()->create(['user_role' => 'Technician']);

        // Create test location
        $this->location = Location::factory()->create();

        // Authenticate as reporter
        $this->actingAs($this->reporter);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->post(route('issue.store'), []);

        $response->assertSessionHasErrors([
            'location_id',
            'issue_type',
            'issue_description',
            'safety_hazard',
            'affected_areas'
        ]);
    }

    /** @test */
    public function it_requires_pc_fields_when_issue_type_is_pc()
    {
        $response = $this->post(route('issue.store'), [
            'issue_type' => 'PC',
            'location_id' => $this->location->id,
            // Missing pc_number which is required for PC issues
        ]);

        $response->assertSessionHasErrors(['pc_number']);
    }

    /** @test */
    public function it_calculates_correct_urgency_scores()
    {
        // Test Electrical issue (base score 3)
        $score = $this->calculateUrgencyScore([
            'issue_type' => 'Electrical',
            'safety_hazard' => true,
            'affected_areas' => 5
        ]);
        $this->assertEquals(8, $score); // 3 (Electrical) + 3 (safety) + 2 (areas > 3)

        // Test PC issue with critical work (base score 1 + 2)
        $score = $this->calculateUrgencyScore([
            'issue_type' => 'PC',
            'critical_work_affected' => true,
            'pc_issue_type' => 'hardware'
        ]);
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

        $response = $this->post(route('issue.store'), $data);

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

        // First submission
        $this->post(route('issue.store'), $data);

        // Second submission
        $response = $this->post(route('issue.store'), $data);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('already submitted', session('error'));
    }

    /** @test */
    public function it_stores_attachments_correctly()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('issue.jpg');

        $data = array_merge($this->validIssueData(), [
            'storage.attachments' => [$file]
        ]);

        $this->post(route('issue.store'), $data);

        $issue = Issue::first();
        $this->assertCount(1, $issue->attachments);
        Storage::disk('public')->assertExists($issue->attachments->first()->file_path);
    }

    /** @test */
    public function it_sends_notification_to_reporter()
    {
        Notification::fake();

        $this->post(route('issue.store'), $this->validIssueData());

        Notification::assertSentTo(
            $this->reporter,
            DatabaseNotification::class,
            function ($notification) {
                return str_contains($notification->message, 'New Issue #');
            }
        );
    }

    /** @test */
    public function it_sends_email_to_assigned_technician()
    {
        Mail::fake();

        // Create a technician to be assigned
        $technician = User::factory()->create(['role' => 'technician']);

        $this->post(route('issue.store'), $this->validIssueData());

        Mail::assertSent(TechnicianAssignmentEmail::class, function ($mail) use ($technician) {
            return $mail->hasTo($technician->email);
        });
    }

    /** @test */
    public function it_handles_failed_submission_gracefully()
    {
        // Force a DB error by violating a constraint
        $data = $this->validIssueData();
        $data['location_id'] = 9999; // Invalid location

        $response = $this->post(route('issue.store'), $data);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('issues', 0);
    }

    protected function calculateUrgencyScore($data)
    {
        $controller = new \App\Http\Controllers\IssueController();

        $defaults = [
            'location_id' => $this->location->id,
            'issue_description' => 'Test issue',
            'safety_hazard' => false,
            'affected_areas' => 1,
            'critical_work_affected' => false,
            'pc_issue_type' => null
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
            'location_id' => $this->location->id,
            'issue_type' => 'Electrical',
            'issue_description' => 'Test issue description',
            'safety_hazard' => true,
            'affected_areas' => 2,
            'critical_work_affected' => false
        ];
    }
}
