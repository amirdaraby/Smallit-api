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
 * @covers \App\Http\Controllers\Api\UrlController
 */
class UrlTest extends TestCase
{

    use RefreshDatabase;

    public function testAllUrlsReturnsAuthenticationError(): void
    {
        $response = $this->getJson(route("api.urls_all"));
        $response->assertUnauthorized();
    }

    public function testAllUrlsReturnsNoContentErrorWhenUserHasNoUrls(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson(route("api.urls_all"));
        $response->assertNotFound();
    }

    public function testAllUrlsReturnsSuccessfulResponse(): void
    {
        $user = User::factory()->create();
        Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.urls_all"));
        $response->assertOk();
    }

    public function testUrlShowReturnsAuthenticationError(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->getJson(route("api.url_show", ["id" => $url->id]));
        $response->assertUnauthorized();
    }

    public function testUrlShowReturnsNotFoundError(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.url_show", ["id" => 1]));
        $response->assertNotFound();
    }

    public function testUrlShowReturnsForbiddenErrorWhenUserIsNotUrlOwner(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user2)->getJson(route("api.url_show", ["id" => $url->id]));

        $response->assertForbidden();
    }

    public function testUrlShowReturnsSuccessfulResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.url_show", ["id" => $url->id]));
        $response->assertOk();
    }

    public function testUrlDeleteReturnsAuthenticationError(): void
    {
        $user = User::factory()->create();
        Url::factory()->for($user)->create();

        $response = $this->deleteJson(route("api.url_delete", ["id" => 1]));

        $response->assertUnauthorized();

    }

    public function testUrlDeleteReturnsNotFoundError(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => 1]));
        $response->assertNotFound();
    }

    public function testUrlDeleteReturnsForbiddenErrorWhenUserIsNotUrlOwner(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $user2 = User::factory()->create();
        $response = $this->actingAs($user2)->deleteJson(route("api.url_delete", ["id" => $url->id]));
        $response->assertForbidden();
    }

    public function testUrlDeleteCanDeleteUrl(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $this->assertDatabaseCount(Url::class, 1);

        $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => $url->id]));

        $this->assertDatabaseCount(Url::class, 0);
    }

    public function testUrlDeleteReturnsSuccessfulResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => $url->id]));

        $response->assertAccepted();
    }

    public function testUrlDeleteDeletesBatchesOnCascade(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();

        ShortUrlJob::dispatch($url->id, 10, $user->id, $batch);

        $this->assertDatabaseCount(Batch::class, 1);

        $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => $url->id]));

        $this->assertDatabaseCount(Batch::class, 0);
    }

    public function testUrlDeleteDeletesShortUrlsOnCascade(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 10)->create();
        ShortUrlJob::dispatch($url->id, 10, $user->id, $batch);

        $this->assertDatabaseCount(ShortUrl::class, 10);

        $this->actingAs($user)->deleteJson(route("api.url_delete", ["id" => $url->id]));

        $this->assertDatabaseCount(ShortUrl::class, 0);
    }

    public function testUrlShowShortUrlsReturnsNotFoundError(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route("api.url_short_urls", ["id" => 1]));

        $response->assertNotFound();
    }

    public function testUrlShowShortUrlsReturnsAuthenticationError(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->getJson(route("api.url_short_urls", ["id" => $url->id]));

        $response->assertUnauthorized();
    }

    public function testUrlShowShortUrlsReturnsForbiddenErrorWhenUserIsNotUrlOwner(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user2)->getJson(route("api.url_short_urls", ["id" => $url->id]));

        $response->assertForbidden();
    }


    public function testUrlShowShortUrlsReturnsNotFoundErrorWhenUrlHasNoShortUrls(): void
    {

        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.url_short_urls", ["id" => $url->id]));

        $response->assertNotFound();
    }

    public function testUrlShowShortUrlsReturnsSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();
        $batch = Batch::factory()->for($user)->for($url)->set("amount", 2)->create();
        ShortUrlJob::dispatch($url->id, 2, $user->id, $batch);

        $response = $this->actingAs($user)->getJson(route("api.url_short_urls", ["id" => $url->id]));

        $response->assertOk();
    }

    public function testUrlShowShortUrlsReturnsValidJsonStructureOnSuccessResponse(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.url_short_urls", ["id" => $url->id]));

        $response->assertJsonStructure([
            "status",
            "data" => [
                "data" => [],
            ],
            "message"
        ]);
    }

    public function testUrlSearchReturnsAuthenticationError(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->getJson(route("api.url_search", ["q" => $url->url]));
        $response->assertUnauthorized();
    }

    public function testUrlSearchReturnsNotFoundWhenThereIsNoResult(): void
    {
        $user = User::factory()->create();
        Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.url_search", ["q" => "cxvnbbsdjf"]));
        $response->assertNotFound();
    }

    public function testUrlSearchResponseSuccessWhenQueryStringHasFirstCharacters(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.url_search", ["q" => substr($url->url, 0, 3)]));
        $response->assertOk();
    }

    public function testUrlSearchResponseSuccessWhenQueryStringHasLastCharacters(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.url_search", ["q" => substr($url->url, strlen($url->url) - 3, 3)]));
        $response->assertOk();
    }

    public function testUrlSearchResponseSuccessWhenQueryStringHasSomeCharactersInMiddleOfString(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.url_search", ["q" => substr($url->url, strlen($url->url) / 2, 3)]));
        $response->assertOk();
    }

    public function testUrlSearchResponseSuccessWhenQueryStringHasAllCharacters(): void
    {
        $user = User::factory()->create();
        $url = Url::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson(route("api.url_search", ["q" => $url->url]));
        $response->assertOk();
    }

}
