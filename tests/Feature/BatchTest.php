<?php

namespace Tests\Feature;

use App\Jobs\ShortUrlJob;
use App\Models\Batch;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_all_batches_return_unauthenticated_error(): void
    {
        $response = $this->getJson(route("api.user_all_batches"));
        $response->assertStatus(401);
    }

    public function test_user_all_batches_return_not_found(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson(route("api.user_all_batches"));

        $response->assertStatus(404);
    }

    public function test_user_all_batches_return_ok_status_on_successful(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->for($user)->for($url)->create()->count(10);

        $response = $this->actingAs($user)->getJson(route("api.user_all_batches"));

        $response->assertStatus(200);
    }


    public function test_user_all_batches_return_data_on_successful(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $response = $this->actingAs($user)->getJson(route("api.user_all_batches"));

        $this->assertCount(10, $response["data"]["data"]);
    }

    public function test_batch_show_returns_unauthenticated_error(): void
    {
        $response = $this->getJson(route("api.user_show_batch", ["id" => 100]));
        $response->assertStatus(401);
    }

    public function test_batch_show_returns_unauthorized_as_not_found_error(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->getJson(route("api.user_show_batch", ["id" => 5]));

        $response->assertStatus(404);
    }

    public function test_batch_show_returns_not_found(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();


        $response = $this->actingAs($user)->getJson(route("api.user_show_batch", ["id" => 11]));

        $response->assertStatus(404);
    }

    public function test_batch_show_returns_ok_status(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $response = $this->actingAs($user)->getJson(route("api.user_show_batch", ["id" => 10]));

        $response->assertStatus(200);
    }

    public function test_batch_show_returns_valid_json_structure(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $response = $this->actingAs($user)->getJson(route("api.user_show_batch", ["id" => 10]));

        $response->assertJsonStructure([
            "status",
            "data" => [
                "id",
                "name",
                "status",
                "user_id",
                "url_id",
                "amount",
                "created_at",
                "updated_at"
            ],
            "message"
        ]);
    }

    public function test_batch_delete_returns_unauthenticated_error(): void
    {
        $response = $this->deleteJson(route("api.user_delete_batch", ["id" => 5]));
        $response->assertStatus(401);
    }

    public function test_batch_delete_returns_unauthorized_as_not_found_error(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->deleteJson(route("api.user_delete_batch", ["id" => 5]));

        $response->assertStatus(404);
    }

    public function test_batch_delete_returns_successful_response(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();

        $response = $this->actingAs($user)->deleteJson(route("api.user_delete_batch", ["id" => $batch->id]));

        $response->assertStatus(200);
    }

    public function test_batch_delete_can_delete_batch_from_database(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $this->assertDatabaseCount(Batch::class, 1);

        $this->actingAs($user)->deleteJson(route("api.user_delete_batch", ["id" => $batch->id]));

        $this->assertDatabaseCount(Batch::class, 0);
    }

    public function test_batch_delete_returns_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route("api.user_delete_batch", ["id" => 50]));

        $response->assertStatus(404);
    }

    public function test_batch_delete_can_delete_short_urls_on_cascade(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();

        ShortUrlJob::dispatch($url->id, 50, $user->id, $batch);

        $this->assertDatabaseCount(ShortUrl::class, 50);
        $this->actingAs($user)->deleteJson(route("api.user_delete_batch", ["id" => $batch->id]));
        $this->assertDatabaseCount(ShortUrl::class, 0);
    }


}
