<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_short_urls_create_returns_auth_error()
    {
        User::factory()->count(10);
        $response = $this->postJson(route("api.shorturl_create"));

        $response->assertStatus(401);
    }

    public function test_short_urls_create_returns_validation_error()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson(route("api.shorturl_create"));

        $response->assertStatus(422);
    }

    public function test_short_urls_create_creates_batch()
    {

    }
}
