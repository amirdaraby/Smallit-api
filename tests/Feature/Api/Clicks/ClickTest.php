<?php

namespace Tests\Feature\Api\Clicks;

use App\Http\Controllers\Api\Clicks\ClickController;
use App\Jobs\StoreClickJob;
use App\Models\Batch;
use App\Models\Click;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ClickController::class)]
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


    public function testClicksIndexReturnsUnauthenticatedError(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->getJson(route("api.click_index", ["shortUrlId" => $shortUrl->id]));

        $response->assertUnauthorized();
    }

    public function testClicksIndexReturnsNotFoundErrorWhenUserIsUnauthorized(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->actingAs($user2)->getJson(route("api.click_index", ["shortUrlId" => $shortUrl->id]));

        $response->assertNotFound();
    }

    public function testClicksIndexReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_index", ["shortUrlId" => $shortUrl->id]));

        $response->assertOk();
    }

    public function testClicksIndexReturnsTotalAndUniqueClicksCorrectly(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        Click::factory()->for($shortUrl)->createMany(5);
        Click::factory()->for($shortUrl)->set("uid", "abcd")->createMany(5);

        $response = $this->actingAs($user)->getJson(route("api.click_index", ["shortUrlId" => $shortUrl->id]))->getOriginalContent();

        $this->assertEquals(10, $response["data"]["total_clicks"], "Total Clicks are incorrect");
        $this->assertEquals(6, $response["data"]["unique_clicks"], "Unique Clicks are incorrect");
    }

    public function testClicksAllReturnsNotFoundErrorWhenShortUrlHasNoClicks(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_all", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksAllReturnsAuthenticationError(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->getJson(route("api.click_all", ["shortUrlId" => $shortUrl->id]));
        $response->assertUnauthorized();
    }

    public function testClicksAllReturnsAuthorizationErrorAsNotFound(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->createMany(5);
        $response = $this->actingAs($user2)->getJson(route("api.click_all", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksAllReturnsNotFoundWhenShortUrlDidntExists(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.click_all", ["shortUrlId" => "100000"]));
        $response->assertNotFound();
    }

    public function testClicksAllReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();
        $response = $this->actingAs($user)->getJson(route("api.click_all", ["shortUrlId" => $shortUrl->id]));
        $response->assertOk();
    }

    public function testClicksAllReturnsValidPaginatedResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();
        $response = $this->actingAs($user)->getJson(route("api.click_all", ["shortUrlId" => $shortUrl->id]));
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

    public function testClicksShowReturnsNotFoundError(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson(route("api.click_show", ["id" => 1]));
        $response->assertNotFound();
    }

    public function testClicksShowReturnsAuthenticationError(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        $click = Click::factory()->for($shortUrl)->create();

        $response = $this->getJson(route("api.click_show", ["id" => $click->id]));
        $response->assertUnauthorized();
    }

    public function testClicksShowReturnsAuthorizationErrorAsNotFound()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        $click = Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user2)->getJson(route("api.click_show", ["id" => $click->id]));
        $response->assertNotFound();
    }

    public function testClicksShowReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        $click = Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_show", ["id" => $click->id]));
        $response->assertOk();
    }

    public function testClicksBrowsersReturnsNotFoundWhenShortUrlDoesntExist(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson(route("api.click_browsers", ["shortUrlId" => "100000"]));
        $response->assertNotFound();
    }

    public function testClicksBrowsersReturnsAuthenticationError(): void
    {
        $response = $this->getJson(route("api.click_browsers", ["shortUrlId" => "100000"]));
        $response->assertUnauthorized();
    }

    public function testClicksBrowsersReturnsAuthorizationErrorAsNotFound(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user2)->getJson(route("api.click_browsers", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksBrowserReturnsNotFoundErrorWhenShortUrlHasNoClicks(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_browsers", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksBrowserReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_browsers", ["shortUrlId" => $shortUrl->id]));
        $response->assertOk();
    }

    public function testClicksBrowserReturnsValidDataOrderedByClicksInResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->set("uid", "1")->set("browser", "Chrome")->createMany(6);
        Click::factory()->for($shortUrl)->set("uid", "1")->set("browser","Firefox")->createMany(5);

        $response = $this->actingAs($user)->getJson(route("api.click_browsers", ["shortUrlId" => $shortUrl->id]))->getOriginalContent();
        $this->assertEquals("Chrome", $response["data"][0]["browser"]);
        $this->assertEquals(6, $response["data"][0]["total_clicks"]);

        $this->assertEquals("Firefox", $response["data"][1]["browser"]);
        $this->assertEquals(5, $response["data"][1]["total_clicks"]);
    }


    public function testClicksPlatformsReturnsNotFoundWhenShortUrlDoesntExist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.click_platforms", ["shortUrlId" => "100000"]));
        $response->assertNotFound();
    }

    public function testClicksPlatformsReturnsAuthenticationError(): void
    {
        $response = $this->getJson(route("api.click_platforms", ["shortUrlId" => "100000"]));
        $response->assertUnauthorized();
    }

    public function testClicksPlatformsReturnsAuthorizationErrorAsNotFound(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user2)->getJson(route("api.click_platforms", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksPlatformsReturnsNotFoundErrorWhenShortUrlHasNoClicks(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_platforms", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksPlatformsReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_platforms", ["shortUrlId" => $shortUrl->id]));
        $response->assertOk();
    }

    public function testClicksPlatformsReturnsValidDataOrderedByClicksInResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->set("uid", "1")->set("platform", "Linux")->createMany(6);
        Click::factory()->for($shortUrl)->set("uid", "1")->set("platform", "Windows")->createMany(5);

        $response = $this->actingAs($user)->getJson(route("api.click_platforms", ["shortUrlId" => $shortUrl->id]))->getOriginalContent();
        $this->assertEquals("Linux", $response["data"][0]["platform"]);
        $this->assertEquals(6, $response["data"][0]["total_clicks"]);

        $this->assertEquals("Windows", $response["data"][1]["platform"]);
        $this->assertEquals(5, $response["data"][1]["total_clicks"]);
    }
}
