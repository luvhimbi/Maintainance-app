<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeaveSiteControllerTest extends TestCase
{
    /** @test */
    public function it_shows_leave_site_page_for_valid_url()
    {
        $url = 'https://example.com';
        $response = $this->get(route('leaving', ['url' => $url]));
        $response->assertStatus(200);
        $response->assertViewIs('leave-site');
        $response->assertViewHas('url', $url);
    }

    /** @test */
    public function it_redirects_back_with_error_for_invalid_url()
    {
        $response = $this->get(route('leaving', ['url' => 'not-a-valid-url']));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid URL.');
    }

    /** @test */
    public function it_redirects_back_with_error_when_url_is_missing()
    {
        $response = $this->get(route('leaving'));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid URL.');
    }
} 