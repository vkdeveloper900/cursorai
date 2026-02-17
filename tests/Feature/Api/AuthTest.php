<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'dob' => '2000-01-01',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'tests',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'message',
            'data' => [
                'user' => ['id', 'first_name', 'last_name', 'email', 'dob', 'phone', 'status'],
                'token',
                'token_type',
            ],
        ]);

        $this->assertDatabaseHas('users', ['email' => 'ada@example.com']);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_register_validates_input(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['first_name', 'last_name', 'dob', 'email', 'password']);
    }

    public function test_register_validation_returns_json_even_without_accept_header(): void
    {
        // This mimics Postman/browser-like requests that may not send Accept: application/json.
        $response = $this->post('/api/auth/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['first_name', 'last_name', 'dob', 'email', 'password']);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        User::factory()->create([
            'email' => 'grace@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'grace@example.com',
            'password' => 'password123',
            'device_name' => 'tests',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'data' => [
                'user' => ['id', 'first_name', 'last_name', 'email', 'dob', 'phone', 'status'],
                'token',
                'token_type',
            ],
        ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertUnauthorized();
    }

    public function test_logout_unauthenticated_returns_json_even_without_accept_header(): void
    {
        // Mimic Postman requests without Accept: application/json.
        $response = $this->post('/api/auth/logout');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_user_can_logout_and_token_is_revoked(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
            'device_name' => 'tests',
        ])->assertOk();

        $plainTextToken = $loginResponse->json('data.token');

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer '.$plainTextToken,
        ])->postJson('/api/auth/logout');

        $logoutResponse->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertNull(PersonalAccessToken::findToken($plainTextToken));
    }
}

