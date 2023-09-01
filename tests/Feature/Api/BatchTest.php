<?php

namespace Feature\Api;

use App\Jobs\ShortUrlJob;
use App\Models\Batch;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\Api\BatchController
 */
class BatchTest extends TestCase
{
    use RefreshDatabase;

    public function testAllBatchesReturnUnauthenticatedError(): void
    {
        $response = $this->getJson(route("api.batches_all"));
        $response->assertUnauthorized();
    }

    public function testAllBatchesReturnNotFound(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson(route("api.batches_all"));

        $response->assertNotFound();
    }

    public function testAllBatchesReturnSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->for($user)->for($url)->create()->count(10);

        $response = $this->actingAs($user)->getJson(route("api.batches_all"));

        $response->assertOk();
    }


    public function testALlBatchesReturnDataOnSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $response = $this->actingAs($user)->getJson(route("api.batches_all"));

        $this->assertCount(10, $response["data"]["data"]);
    }

    public function testBatchShowReturnsUnauthenticatedError(): void
    {
        $response = $this->getJson(route("api.batch_show", ["id" => 100]));
        $response->assertUnauthorized();
    }

    public function testBatchShowReturnsUnauthorizedErrorAsNotFound(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->getJson(route("api.batch_show", ["id" => 5]));

        $response->assertNotFound();
    }

    public function testBatchShowReturnsNotFound(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();


        $response = $this->actingAs($user)->getJson(route("api.batch_show", ["id" => 11]));

        $response->assertNotFound();
    }

    public function testBatchShowReturnsSuccessResponse(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $response = $this->actingAs($user)->getJson(route("api.batch_show", ["id" => 10]));

        $response->assertOk();
    }

    public function testBatchShowReturnsValidJsonStructure(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $response = $this->actingAs($user)->getJson(route("api.batch_show", ["id" => 10]));

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

    public function testBatchDeleteReturnsUnauthenticatedError(): void
    {
        $response = $this->deleteJson(route("api.batch_delete", ["id" => 5]));
        $response->assertUnauthorized();
    }

    public function testBatchDeleteReturnsUnauthorizedErrorAsNotFound(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->count(10)->for($user)->for($url)->create();

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->deleteJson(route("api.batch_delete", ["id" => 5]));

        $response->assertNotFound();
    }

    public function testBatchDeleteReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        ShortUrlJob::dispatch($url->id, 1, $user->id, $batch);

        $response = $this->actingAs($user)->deleteJson(route("api.batch_delete", ["id" => $batch->id]));

        $response->assertOk();
    }

    public function testBatchDeleteCanDeleteBatchFromDatabase(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $this->assertDatabaseCount(Batch::class, 1);

        ShortUrlJob::dispatch($url->id, 1, $user->id, $batch);

        $this->actingAs($user)->deleteJson(route("api.batch_delete", ["id" => $batch->id]));

        $this->assertDatabaseCount(Batch::class, 0);
    }

    public function testBatchDeleteReturnsNotFound(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route("api.batch_delete", ["id" => 50]));

        $response->assertNotFound();
    }

    public function testBatchDeleteCanDeleteShortUrlsOnCascade(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();

        ShortUrlJob::dispatch($url->id, 50, $user->id, $batch);

        $this->assertDatabaseCount(ShortUrl::class, 50);
        $this->actingAs($user)->deleteJson(route("api.batch_delete", ["id" => $batch->id]));
        $this->assertDatabaseCount(ShortUrl::class, 0);
    }

    public function testBatchDeleteReturnsBadRequestWhenBatchIsNotSuccess(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();

        $response = $this->actingAs($user)->deleteJson(route("api.batch_delete", ["id" => $batch->id]));

        $response->assertBadRequest();
    }

    public function testBatchShowShortUrlsReturnAuthenticationError(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();

        $response = $this->getJson(route("api.batch_short_urls", ["id" => $batch->id]));

        $response->assertUnauthorized();
    }

    public function testBatchShowShortUrlsReturnNotFound(){
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.batch_short_urls", ["id" => 1]));

        $response->assertNotFound();
    }

    public function testBatchShowShortUrlsReturnUnauthorizedErrorAsNotFound(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->getJson(route("api.batch_short_urls", ["id" => $batch->id]));

        $response->assertNotFound();
    }

    public function testBatchShowShortUrlsReturnConflictErrorWhenBatchIsNotComplete(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        Batch::factory()->for($user)->for($url)->set("status", "queue")->create();
        $response = $this->actingAs($user)->getJson(route("api.batch_short_urls", ["id" => 1]));

        $response->assertConflict();
    }

    public function testBatchShowShortUrlsReturnSuccessResponse(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();
        ShortUrlJob::dispatch($url->id, 10, $user->id, $batch);


        $response = $this->actingAs($user)->getJson(route("api.batch_short_urls", ["id" => $batch->id]));
        $response->assertOk();
    }

    public function testBatchShowShortUrlsReturnPaginatedSuccessResponse(){
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();
        ShortUrlJob::dispatch($url->id, 10, $user->id, $batch);


        $response = $this->actingAs($user)->getJson(route("api.batch_short_urls", ["id" => $batch->id]));
        $response->assertOk()->assertJsonStructure([
            "data" => [
                'current_page',
                'data' => [],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
        ]);
    }

}
