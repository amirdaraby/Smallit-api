<?php

namespace Feature\Api;

use App\Jobs\ShortUrlJob;
use App\Models\Batch;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlTest extends TestCase
{

    use RefreshDatabase;

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

    public function test_url_show_returns_unauthenticated_error()
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->getJson(route("api.url_show", ["id" => $url->id]));
        $response->assertStatus(401);
    }

    public function test_user_url_show_returns_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.url_show", ["id" => 1]));
        $response->assertStatus(404);
    }

    public function test_url_show_returns_unauthorized_error_as_not_found()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user2)->getJson(route("api.url_show", ["id" => $url->id]));

        $response->assertStatus(404);
    }

    public function test_url_show_returns_success_response()
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.url_show", ["id" => $url->id]));
        $response->assertStatus(200);
    }

    public function test_url_delete_returns_unauthenticated_error(){
        $user = User::factory()->create();
        Url::factory()->for($user)->create();

        $response = $this->deleteJson(route("api.url_delete", ["id" => 1]));

        $response->assertStatus(401);

    }
    public function test_url_delete_returns_not_found_error(){
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => 1]));
        $response->assertStatus(404);
    }

    public function test_url_delete_returns_unauthorized_error_as_not_found(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $user2 = User::factory()->create();
        $response = $this->actingAs($user2)->deleteJson(route("api.url_delete", ["id" => $url->id]));
        $response->assertStatus(404);
    }

    public function test_url_delete_deletes_url_successfully(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $this->assertDatabaseCount(Url::class, 1);

        $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => $url->id]));

        $this->assertDatabaseCount(Url::class, 0);
    }

    public function test_url_delete_deletes_batches_on_cascade(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();

        ShortUrlJob::dispatch($url->id, 10, $user->id, $batch);

        $this->assertDatabaseCount(Batch::class, 1);

        $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => $url->id]));

        $this->assertDatabaseCount(Batch::class, 0);
    }

    public function test_url_delete_deletes_short_urls_on_cascade(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();
        ShortUrlJob::dispatch($url->id, 10, $user->id, $batch);

        $this->assertDatabaseCount(ShortUrl::class, 10);

        $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => $url->id]));

        $this->assertDatabaseCount(ShortUrl::class, 0);
    }

    public function test_url_delete_returns_response_success(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => $url->id]));

        $response->assertStatus(200);
    }
}
