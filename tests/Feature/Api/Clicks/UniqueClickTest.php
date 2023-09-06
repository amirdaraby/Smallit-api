<?php

namespace Tests\Feature\Api\Clicks;

use App\Http\Controllers\Api\Clicks\UniqueClickController;
use App\Models\Batch;
use App\Models\Click;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UniqueClickController::class)]
class UniqueClickTest extends TestCase
{
    use RefreshDatabase;

    public function testClicksUniqueAllReturnsNotFoundWhenShortUrlDoesntExist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_all", ["shortUrlId" => "100000"]));
        $response->assertNotFound();
    }

    public function testClicksUniqueAllReturnsAuthenticationError(): void
    {
        $response = $this->getJson(route("api.click_unique_all", ["shortUrlId" => "100000"]));
        $response->assertUnauthorized();
    }

    public function testClicksUniqueAllReturnsAuthorizationErrorAsNotFound(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user2)->getJson(route("api.click_unique_all", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksUniqueAllReturnsNotFoundErrorWhenShortUrlHasNoClicks(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_all", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksUniqueAllReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_all", ["shortUrlId" => $shortUrl->id]));
        $response->assertOk();
    }

    public function testClicksUniqueAllReturnsValidPaginatedResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_all", ["shortUrlId" => $shortUrl->id]));
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

    public function testClicksUniqueBrowsersReturnsNotFoundWhenShortUrlDoesntExist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_browsers", ["shortUrlId" => "100000"]));
        $response->assertNotFound();
    }

    public function testClicksUniqueBrowsersReturnsAuthenticationError(): void
    {
        $response = $this->getJson(route("api.click_unique_browsers", ["shortUrlId" => "100000"]));
        $response->assertUnauthorized();
    }

    public function testClicksUniqueBrowsersReturnsAuthorizationErrorAsNotFound(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user2)->getJson(route("api.click_unique_browsers", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksUniqueBrowsersReturnsNotFoundWhenShortUrlHasNoClicks(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_browsers", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksUniqueBrowsersReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_browsers", ["shortUrlId" => $shortUrl->id]));
        $response->assertOk();
    }

    public function testClicksUniqueBrowsersReturnsUniqueDataInResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->set("browser", "Chrome")->set("uid", "1")->createMany(5);

        $this->assertDatabaseCount(Click::class, 5);
        $response = $this->actingAs($user)->getJson(route("api.click_unique_browsers", ["shortUrlId" => $shortUrl->id]))->getOriginalContent();
        $this->assertEquals(1, $response["data"][0]["unique_clicks"]);
    }


    public function testClicksUniqueBrowsersReturnsValidDataOrderedByUniqueClicksInResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        Click::factory()->for($shortUrl)->set("browser", "Chrome")->set("uid", "1")->createMany(2);
        Click::factory()->for($shortUrl)->set("browser", "Chrome")->createMany(6);

        Click::factory()->for($shortUrl)->set("browser", "Firefox")->set("uid", "2")->createMany(2);
        Click::factory()->for($shortUrl)->set("browser", "Firefox")->createMany(5);

        $response = $this->actingAs($user)->getJson(route("api.click_unique_browsers", ["shortUrlId" => $shortUrl->id]))->getOriginalContent();

        $this->assertEquals("Chrome", $response["data"][0]["browser"]);
        $this->assertEquals(7, $response["data"][0]["unique_clicks"]);

        $this->assertEquals("Firefox", $response["data"][1]["browser"]);
        $this->assertEquals(6, $response["data"][1]["unique_clicks"]);
    }


    public function testClicksUniquePlatformsReturnsNotFoundWhenShortUrlDoesntExist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_platforms", ["shortUrlId" => "100000"]));
        $response->assertNotFound();
    }

    public function testClicksUniquePlatformsReturnsAuthenticationError(): void
    {
        $response = $this->getJson(route("api.click_unique_platforms", ["shortUrlId" => "100000"]));
        $response->assertUnauthorized();
    }

    public function testClicksUniquePlatformsReturnsAuthorizationErrorAsNotFound(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user2)->getJson(route("api.click_unique_platforms", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksUniquePlatformsReturnsNotFoundWhenShortUrlHasNoClicks(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_platforms", ["shortUrlId" => $shortUrl->id]));
        $response->assertNotFound();
    }

    public function testClicksUniquePlatformsReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->for($shortUrl)->create();

        $response = $this->actingAs($user)->getJson(route("api.click_unique_platforms", ["shortUrlId" => $shortUrl->id]));
        $response->assertOk();
    }

    public function testClicksUniquePlatformsReturnsUniqueDataInResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();
        Click::factory()->set("platform", "Windows")->set("uid", "1")->for($shortUrl)->createMany(5);

        $this->assertDatabaseCount(Click::class, 5);
        $response = $this->actingAs($user)->getJson(route("api.click_unique_platforms", ["shortUrlId" => $shortUrl->id]))->getOriginalContent();

        $this->assertEquals("Windows", $response["data"][0]["platform"]);
        $this->assertEquals(1, $response["data"][0]["unique_clicks"]);
    }

    public function testClicksUniquePlatformsReturnsValidDataOrderedByClicksInResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->create();
        $shortUrl = ShortUrl::factory()->for($user)->for($url)->for($batch)->create();

        Click::factory()->for($shortUrl)->set("platform", "Linux")->set("uid", "2")->createMany(2);
        Click::factory()->for($shortUrl)->set("platform", "Linux")->createMany(6);

        Click::factory()->for($shortUrl)->set("platform", "Windows")->set("uid", "3")->createMany(2);
        Click::factory()->for($shortUrl)->set("platform", "Windows")->createMany(5);


        $response = $this->actingAs($user)->getJson(route("api.click_unique_platforms", ["shortUrlId" => $shortUrl->id]))->getOriginalContent();

        $this->assertEquals("Linux", $response["data"][0]["platform"]);
        $this->assertEquals(7, $response["data"][0]["unique_clicks"]);

        $this->assertEquals("Windows", $response["data"][1]["platform"]);
        $this->assertEquals(6, $response["data"][1]["unique_clicks"]);
    }

}
