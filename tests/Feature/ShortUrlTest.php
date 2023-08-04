<?php

namespace Tests\Feature;

use App\Jobs\ShortUrlJob;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;
use Mockery;
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

    public function test_short_url_create_returns_successful()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route("api.shorturl_create"), [
            "url" => "https://rockstargames.com",
            "amount" => 100000,
            "batch_name" => "mamad_batch"
        ]);

        $response->assertStatus(202);
    }

    public function test_short_urls_create_creates_batch()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route("api.shorturl_create"), [
            "url" => "https://rockstargames.com",
            "amount" => 100000,
            "batch_name" => "mamad_batch"
        ]);

        $batch = Batch::query()->find(1);

        $this->assertNotNull($batch);
        $this->assertSame("mamad_batch", $batch->name);
    }

}
