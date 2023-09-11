<?php

namespace Feature\Api;


use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\Api\Auth\AuthController
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;


    public function test_register_validation_returns_error_on_invalid_request_body(): void
    {
        $response = $this->postJson(route("api.register"), [
            "name" => 1,
            "email" => "WrongEmail",
            "password" => null
        ]);
        $response->assertStatus(422);
    }

    public function test_register_creates_user(): void
    {
        $response = $this->postJson(route("api.register"), [
            "name" => "test",
            "email" => "test@tester.com",
            "password" => 123456789
        ]);

        $response->assertCreated();
    }


    public function test_register_creates_and_returns_personal_access_token(): void
    {
        $response = $this->postJson(route("api.register"), [
            "name" => "test",
            "email" => "test@tester.com",
            "password" => 123456789
        ])->getOriginalContent();

        $this->assertNotNull(PersonalAccessToken::findToken($response["data"]["token"]));
        $this->assertArrayHasKey("token", $response["data"]);
    }

    public function test_register_creates_and_returns_valid_json_structure(): void
    {
        $response = $this->postJson(route("api.register"), [
            "name" => "test",
            "email" => "test@tester.com",
            "password" => UserFactory::PASSWORD
        ]);

        $response->assertJsonStructure([
            "status",
            "data" => [
                "token",
                "name",
                "email"
            ],
            "message"
        ]);
    }

    public function test_login_validation_returns_error_on_invalid_request_body(): void
    {
        $response = $this->postJson(route("api.login"), [
            "email" => "WrongEmail",
            "password" => null
        ]);
        $response->assertStatus(422);
    }

    public function test_login_returns_error_response_when_user_not_found(): void
    {
        $user = [
            "email" => "test@gmail.com",
            "password" => "someSecurePassword"
        ];
        $response = $this->postJson(route("api.login"), $user);

        $response->assertStatus(401);
    }

    public function test_login_returns_error_when_password_is_wrong(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route("api.login"), ["email" => $user->email, "password" => "123"]);

        $response->assertStatus(422);
    }

    public function test_login_returns_success(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route("api.login"), ["email" => $user->email, "password" => UserFactory::PASSWORD]);

        $response->assertStatus(202);
    }

    public function test_login_returns_user_data_on_success(): void
    {
        $user = User::factory()->create();


        $response = $this->postJson(route("api.login"), ["email" => $user->email, "password" => UserFactory::PASSWORD])->getOriginalContent();

        $this->assertArrayHasKey("name", $response["data"]);
        $this->assertArrayHasKey("email", $response["data"]);
    }

    public function test_login_creates_and_returns_personal_access_token_on_success(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route("api.login"), ["email" => $user->email, "password" => UserFactory::PASSWORD]);

        $this->assertNotNull(PersonalAccessToken::findToken($response["data"]["token"]));
        $this->assertArrayHasKey("token", $response["data"]);
    }

    public function test_logout_returns_error_when_request_has_invalid_authorization_header(): void
    {
        $response = $this->deleteJson(route("api.logout"), headers: ["Authorization" => "123"]);

        $response->assertStatus(401);
    }

    public function test_logout_returns_success_status(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken("token")->plainTextToken;

        $response = $this->deleteJson(route("api.logout"), headers: ["Authorization" => "Bearer " . $token]);

        $response->assertAccepted();
    }

    public function test_logout_deletes_personal_access_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken("token")->plainTextToken;

        $this->deleteJson(route("api.logout"), headers: ["Authorization" => "Bearer " . $token]);

        $token = PersonalAccessToken::findToken($token);

        $this->assertNull($token);
    }
}
