<?php

namespace Feature\Api;


use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_show_returns_auth_error(): void
    {
        $response = $this->getJson(route("api.user_show"));
        $response->assertStatus(401);
    }

    public function test_user_show_returns_successful(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson(route("api.user_show"));

        $response->assertStatus(200);

    }

    public function test_user_show_returns_valid_json_structure(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson(route("api.user_show"));

        $response->assertJsonStructure([
            "status",
            "data" => [
                "user" => [
                    "name",
                    "email"
                ]
            ]
        ]);
    }

    public function test_user_update_returns_auth_error(): void
    {
        $user = User::factory()->create();

        $response = $this->putJson(route("api.user_update", [
            "name" => $user->name,
            "email" => $user->email,
            "password" => UserFactory::PASSWORD
        ]));

        $response->assertStatus(401);
    }

    public function test_user_update_returns_validation_error_when_password_is_wrong(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route("api.user_update"), [
            "name" => $user->name,
            "email" => "mamad@gmail.com",
            "password" => "hck"
        ]);

        $response->assertStatus(422);
    }

    public function test_user_update_returns_validation_error_with_empty_request(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route("api.user_update"), [

        ]);

        $response->assertStatus(422);
    }

    public function test_user_update_returns_validation_error_with_email_and_without_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route("api.user_update"), [
            "email" => "new@test.com",
        ]);

        $response->assertStatus(422);
    }

    public function test_user_update_returns_validation_error_with_duplicate_email(): void
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $response = $this->actingAs($userOne)->putJson(route("api.user_update"), [
            "name" => $userOne->name,
            "email" => $userTwo->email,
        ]);

        $response->assertStatus(422);

    }

    public function test_user_update_dont_require_password_when_updating_only_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route("api.user_update"), [
            "name" => "mamad"
        ]);

        $response->assertStatus(202);
    }

    public function test_user_update_dont_require_password_when_email_is_not_changed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route("api.user_update"), [
            "name" => "a new name like mmd",
            "email" => $user->email
        ]);

        $response->assertStatus(202);
    }

    public function test_user_update_updates_user(): void
    {
        $user = User::factory()->create();

        $request = [
            "name" => "amir",
            "email" => "amir@idkmail.ji",
            "password" => UserFactory::PASSWORD
        ];

        $this->actingAs($user)->putJson(route("api.user_update"), $request);

        $userAfterUpdate = User::find($user->id);

        $this->assertNotSame($user->getAttributes(), $userAfterUpdate->getAttributes());
    }

    public function test_user_update_returns_accepted_status_on_successful(): void
    {
        $user = User::factory()->create();

        $request = [
            "name" => "amir",
            "email" => "amir@idkmail.ji",
            "password" => UserFactory::PASSWORD
        ];

        $response = $this->actingAs($user)->putJson(route("api.user_update"), $request);

        $response->assertStatus(202);
    }

    public function test_user_update_returns_valid_json_structure(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route("api.user_update"), [
            "name" => "new name bruh",
            "email" => $user->email,
        ]);

        $response->assertJsonStructure([
            "status",
            "data" => [
                "updated"
            ],
            "message"
        ]);
    }

    public function test_user_delete_returns_auth_error(): void
    {
        User::factory(20)->create();

        $response = $this->deleteJson(route("api.user_delete"));

        $response->assertStatus(401);
    }


    public function test_user_delete_deletes_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->deleteJson(route("api.user_delete"), [
            "password" => UserFactory::PASSWORD
        ]);

        $user = User::find($user->id);

        $this->assertNull($user);
    }

    public function test_user_delete_returns_accepted_status_on_successful(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route("api.user_delete"));

        $response->assertStatus(202);
    }

    public function test_user_delete_returns_valid_json_structure(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route("api.user_delete"));

        $response->assertJsonStructure([
            "status",
            "data" => [
                "deleted"
            ],
            "message"
        ]);
    }
}
