<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $issue;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user and issue
        $this->user = User::factory()->create([
            'user_role' => 'Student', // Ensure the user is a student
        ]);
        $this->issue = Issue::factory()->create([
            'reporter_id' => $this->user->user_id,
            'issue_status' => 'Resolved'
        ]);

        // Authenticate as the user
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_stores_feedback_successfully()
    {
        $response = $this->postJson(route('feedback.submit', $this->issue->issue_id), [
            'rating' => 5,
            'comments' => 'Great service!'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Feedback submitted successfully.'
            ]);

        $this->assertDatabaseHas('feedback', [
            'issue_id' => $this->issue->issue_id,
            'user_id' => $this->user->user_id,
            'rating' => 5,
            'comments' => 'Great service!'
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson(route('feedback.submit', $this->issue->issue_id), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    /** @test */
    public function it_validates_rating_range()
    {
        $response = $this->postJson(route('feedback.submit', $this->issue->issue_id), [
            'rating' => 6, // Invalid rating
            'comments' => 'Test comment'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    /** @test */
    public function it_prevents_duplicate_feedback()
    {
        // Create initial feedback
        Feedback::create([
            'issue_id' => $this->issue->issue_id,
            'user_id' => $this->user->user_id,
            'rating' => 4,
            'comments' => 'First feedback'
        ]);

        // Try to submit feedback again
        $response = $this->postJson(route('feedback.submit', $this->issue->issue_id), [
            'rating' => 5,
            'comments' => 'Second feedback'
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'You have already submitted feedback for this issue.'
            ]);

        // Verify only one feedback exists
        $this->assertDatabaseCount('feedback', 1);
    }

    /** @test */


    /** @test */
    public function it_paginates_feedback_list()
    {
        // Create 20 feedback entries
        Feedback::factory()->count(20)->create();

        // Act as admin
        $admin = User::factory()->create(['user_role' => 'Admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.feedbacks.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.feedbacks.index')
            ->assertViewHas('feedbacks', function ($feedbacks) {
                return $feedbacks->count() === 15; // Default pagination
            });
    }
}
