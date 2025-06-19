<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminStaffControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['user_role' => 'Admin']);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function it_lists_staff_members_with_pagination()
    {
        User::factory()->count(7)->create(['user_role' => 'Staff_Member']);
        $response = $this->get(route('staff.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.staff.index');
        $response->assertViewHas('staff');
        $staff = $response->viewData('staff');
        $this->assertTrue($staff->perPage() === 5);
    }

    /** @test */
    public function it_shows_a_single_staff_member()
    {
        $staff = User::factory()->create(['user_role' => 'Staff_Member']);
        $response = $this->get(route('staff.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.staff.index');
        $response->assertViewHas('staff');
        $staffList = $response->viewData('staff');
        $this->assertTrue($staffList->contains($staff));
    }
}
