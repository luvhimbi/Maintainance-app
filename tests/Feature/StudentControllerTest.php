<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Issue;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Support\Str;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $building;
    protected $floor;
    protected $room;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = User::factory()->create(['user_role' => 'Student']);
        $this->actingAs($this->student);

        // Create test building, floor, and room
        $this->building = Building::factory()->create(['building_name' => 'Test Building']);
        $this->floor = Floor::factory()->create(['building_id' => $this->building->id]);
        $this->room = Room::factory()->create(['floor_id' => $this->floor->id, 'room_number' => '101']);
    }

    /** @test */
    public function it_shows_dashboard_with_issues_for_student()
    {
        Issue::factory()->count(3)->create([
            'reporter_id' => $this->student->user_id,
            'issue_status' => 'Open',
            'room_id' => $this->room->id
        ]);
        $response = $this->get(route('Student.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('Student.dashboard');
        $response->assertViewHas('issues');
    }

    /** @test */
    public function it_filters_issues_by_status()
    {
        Issue::factory()->create([
            'reporter_id' => $this->student->user_id,
            'issue_status' => 'Open',
            'room_id' => $this->room->id
        ]);
        Issue::factory()->create([
            'reporter_id' => $this->student->user_id,
            'issue_status' => 'Resolved',
            'room_id' => $this->room->id
        ]);
        $response = $this->get(route('Student.dashboard', ['status' => ['Open']]));
        $response->assertStatus(200);
        $issues = $response->viewData('issues');
        $this->assertTrue($issues->every(fn($issue) => $issue->issue_status === 'Open'));
    }

    /** @test */
    public function it_searches_issues_by_description()
    {
        Issue::factory()->create([
            'reporter_id' => $this->student->user_id,
            'issue_description' => 'WiFi broken',
            'issue_status' => 'Open',
            'room_id' => $this->room->id
        ]);
        Issue::factory()->create([
            'reporter_id' => $this->student->user_id,
            'issue_description' => 'Printer jam',
            'issue_status' => 'Open',
            'room_id' => $this->room->id
        ]);
        $response = $this->get(route('Student.dashboard', ['search' => 'wifi']));
        $response->assertStatus(200);
        $issues = $response->viewData('issues');
        $this->assertTrue($issues->count() > 0);
        $this->assertStringContainsStringIgnoringCase('wifi', $issues->first()->issue_description);
    }

    /** @test */
    public function it_paginates_issues()
    {
        Issue::factory()->count(7)->create([
            'reporter_id' => $this->student->user_id,
            'issue_status' => 'Open',
            'room_id' => $this->room->id
        ]);
        $response = $this->get(route('Student.dashboard'));
        $response->assertStatus(200);
        $issues = $response->viewData('issues');
        $this->assertEquals(5, $issues->count()); // Default pagination is 5
    }
} 