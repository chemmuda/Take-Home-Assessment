<?php

namespace Tests\Feature\Workflows;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function complete_user_registration_and_authentication_workflow()
    {
        // Step 1: Register a new user
        $registrationData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'SecurePassword123!',
        ];

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email'],
            ]);

        $token = $response->json('access_token');
        $userId = $response->json('user.id');

        // Step 2: Verify user was created in database
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'name' => 'Test User',
        ]);

        // Step 3: Access authenticated endpoint with token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $userId,
                'email' => 'testuser@example.com',
            ]);

        // Step 4: Logout
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);

        // Step 5: Try to access protected endpoint after logout (should fail)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/user');

        $response->assertStatus(401);
    }

    /** @test */
    public function login_workflow_with_existing_user()
    {
        // Step 1: Create a user
        $password = 'Password123!';
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'password' => Hash::make($password),
        ]);

        // Step 2: Login with credentials
        $loginData = [
            'email' => 'existing@example.com',
            'password' => $password,
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user',
            ]);

        $token = $response->json('access_token');

        // Step 3: Access protected resources
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'email' => 'existing@example.com',
            ]);

        // Step 4: Access other protected endpoints
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/products');

        $response->assertStatus(200);
    }

    /** @test */
    public function failed_authentication_workflow()
    {
        // Try to register with invalid data
        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'email' => 'invalid-email',
            'password' => '123',
        ]);

        $response->assertStatus(400);

        // Try to login with non-existent user
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401);

        // Try to access protected endpoint without authentication
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /** @test */
    public function multiple_sessions_workflow()
    {
        $user = User::factory()->create([
            'email' => 'multi@example.com',
            'password' => Hash::make('password'),
        ]);

        // Login from first device
        $response1 = $this->postJson('/api/login', [
            'email' => 'multi@example.com',
            'password' => 'password',
        ]);

        $token1 = $response1->json('access_token');

        // Login from second device
        $response2 = $this->postJson('/api/login', [
            'email' => 'multi@example.com',
            'password' => 'password',
        ]);

        $token2 = $response2->json('access_token');

        // Both tokens should work
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
            'Accept' => 'application/json',
        ])->getJson('/api/user');

        $response->assertStatus(200);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token2,
            'Accept' => 'application/json',
        ])->getJson('/api/user');

        $response->assertStatus(200);

        // Logout from first device
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
            'Accept' => 'application/json',
        ])->postJson('/api/logout');

        $response->assertStatus(200);

        // Token1 should not work anymore
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
            'Accept' => 'application/json',
        ])->getJson('/api/user');

        $response->assertStatus(401);

        // Token2 should still work
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token2,
            'Accept' => 'application/json',
        ])->getJson('/api/user');

        $response->assertStatus(200);
    }

    /** @test */
    public function registration_prevents_duplicate_emails()
    {
        // Create a user
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        // Try to register with same email
        $response = $this->postJson('/api/register', [
            'name' => 'Another User',
            'email' => 'duplicate@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(400);

        // Verify only one user with this email exists
        $this->assertCount(1, User::where('email', 'duplicate@example.com')->get());
    }
}
