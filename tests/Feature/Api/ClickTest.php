<?php

namespace Tests\Feature\Api;


use App\Jobs\StoreClickJob;
use App\Models\Batch;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;


/**
 * @covers \App\Http\Controllers\Api\ClickController
 */
class ClickTest extends TestCase
{
    use RefreshDatabase;
    public function testClickAsApiReturnsNotFoundError(): void
    {
        $response = $this->getJson(route("api.click", ["short_url" => "abc"]));
        $response->assertNotFound();
    }

    public function testClickAsApiReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->set("short_url", "abcde")->create();

        Queue::fake();
        $response = $this->getJson(route("api.click", ["short_url" => $shortUrl->short_url]));
        $response->assertOk();
    }

    public function testClickAsApiDispatchesStoreClickJobWhenShortUrlFoundAndResponseIsOk()
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 1)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->set("short_url", "abcde")->create();

        Queue::fake();
        $response = $this->getJson(route("api.click", ["short_url" => $shortUrl->short_url]));
        Queue::assertPushed(StoreClickJob::class);
        $response->assertOk();
    }

    public function testClickAsApiDontDispatchStoreClickJobWhenRequestHasNoUserAgentHeader()
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 1)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->set("short_url", "abcde")->create();

        Queue::fake();
        $response = $this->getJson(route("api.click", ["short_url" => $shortUrl->short_url]), ["user-agent" => null]);
        Queue::assertNotPushed(StoreClickJob::class);
        $response->assertOk();
    }

}
