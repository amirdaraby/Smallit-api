<?php

namespace Feature\Api;

use App\Jobs\ShortUrlJob;
use App\Models\Batch;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    public function testShortUrlCreateReturnsAuthError(): void
    {
        User::factory()->count(10);
        $response = $this->postJson(route("api.short_url_create"));

        $response->assertStatus(401);
    }

    public function testShortUrlCreateReturnsValidationError(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson(route("api.short_url_create"));

        $response->assertStatus(422);
    }

    public function testShortUrlCreateReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();

        Queue::fake();

        $response = $this->actingAs($user)->postJson(route("api.short_url_create"), [
            "url" => "https://rockstargames.com",
            "amount" => 100000,
            "batch_name" => "mamad_batch"
        ]);

        Queue::assertPushed(ShortUrlJob::class);

        $response->assertStatus(202);
    }

    public function testShortUrlCreateCreatesBatch(): void
    {
        $user = User::factory()->create();

        Queue::fake();

        $this->actingAs($user)->postJson(route("api.short_url_create"), [
            "url" => "https://rockstargames.com",
            "amount" => 100000,
            "batch_name" => "mamad_batch"
        ]);

        Queue::assertPushed(ShortUrlJob::class);
        $batch = Batch::query()->find(1);

        $this->assertNotNull($batch);
        $this->assertSame("mamad_batch", $batch->name);
    }

    public function testShortUrlJobHandlesSuccessfully(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $amount = 100000;

        ShortUrlJob::dispatch($url->id, $amount, $user->id, $batch);

        $this->assertDatabaseCount(ShortUrl::class, 100000);
    }

    public function testAllShortUrlsReturnsAuthenticationError(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();
        ShortUrlJob::dispatch($url->id, 10, $user->id, $batch);

        $response = $this->getJson(route("api.short_urls_all"));
        $response->assertUnauthorized();
    }

    public function testAllShortUrlsReturnsNotFoundWhenUserHasNoShortUrls(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.short_urls_all"));
        $response->assertNotFound();
    }

    public function testAllShortUrlsReturnsSuccessResponse(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();
        ShortUrlJob::dispatch($url->id, 10, $user->id, $batch);

        $response = $this->actingAs($user)->getJson(route("api.short_urls_all"));
        $response->assertOk();
    }

    public function testAllShortUrlsReturnsPaginatedSuccessResponse(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();
        ShortUrlJob::dispatch($url->id, 10, $user->id, $batch);

        $response = $this->actingAs($user)->getJson(route("api.short_urls_all"));
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

    public function testShortUrlShowReturnsAuthenticationError(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 1)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->set("short_url", "kdjfn")->create();

        $response = $this->getJson(route("api.short_url_show", ["id" => $shortUrl->id]));
        $response->assertUnauthorized();
    }

    public function testShortUrlShowReturnsAuthorizationErrorAsNotFound(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 1)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->set("short_url", "kdjfn")->create();

        $response = $this->actingAs($user2)->getJson(route("api.short_url_show", ["id" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testShortUrlShowReturnsNotFoundWhenUserDoesntHaveTheShortUrl(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.short_url_show", ["id" => 1]));
        $response->assertNotFound();
    }

    public function testShortUrlShowReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 1)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->set("short_url", "kdjfn")->create();

        $response = $this->actingAs($user)->getJson(route("api.short_url_show", ["id" => $shortUrl->id]));
        $response->assertOk();
    }

    public function testShortUrlShowReturnsShortUrlWithUrlAndTotalClicksOnSuccessResponse(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 1)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->set("short_url", "kdjfn")->create();

        $response = $this->actingAs($user)->getJson(route("api.short_url_show", ["id" => $shortUrl->id]));
        $response->assertJsonStructure([
            "data" => [
                "url", "total_clicks"
            ]
        ]);
    }
}
