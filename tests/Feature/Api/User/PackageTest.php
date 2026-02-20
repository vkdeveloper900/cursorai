<?php

namespace Tests\Feature\Api\User;

use App\Models\Package;
use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/user/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
            'device_name' => 'tests',
        ]);

        $this->token = $response->json('data.token');
    }

    public function test_user_can_list_active_packages(): void
    {
        $package = Package::factory()->create(['status' => 1]);
        $tests = Test::factory()->count(2)->create(['status' => 'published']);
        $package->tests()->attach($tests->pluck('id'));

        // Inactive package
        Package::factory()->create(['status' => 0]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/user/packages');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'description', 'price', 'validity_days', 'total_tests', 'is_purchased', 'expiry_date', 'tests'],
            ],
        ]);
        
        $response->assertJsonFragment(['id' => $package->id, 'title' => $package->name]);
    }

    public function test_user_can_view_single_package(): void
    {
        $package = Package::factory()->create(['status' => 1]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/user/packages/{$package->id}");

        $response->assertOk();
        $response->assertJsonFragment(['id' => $package->id]);
    }
}
