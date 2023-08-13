<?php

namespace Feature\Api;

use App\Models\Url;
use App\Models\User;
use Tests\TestCase;

class UrlTest extends TestCase
{

    public function test_all_urls_return_authentication_error(): void
    {
        $response = $this->getJson(route("api.urls_all"));
        $response->assertStatus(401);
    }

    public function test_all_urls_return_no_content_when_user_has_no_urls(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson(route("api.urls_all"));
        $response->assertStatus(204);
    }

    public function test_all_urls_return_successful_response(): void
    {
        $user = User::factory()->create();
        Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.urls_all"));
        $response->assertStatus(200);
    }
}
