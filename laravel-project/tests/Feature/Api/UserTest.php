<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_list_all_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        User::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'name', 'email', 'role', 'created_at']
                    ]
                ]);
    }

    /** @test */
    public function regular_user_cannot_list_all_users()
    {
        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_list_users()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_view_own_profile()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users/' . $user->id);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name
                    ]
                ]);
    }

    /** @test */
    public function user_cannot_view_other_users_profile()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $token = $user1->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users/' . $user2->id);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_any_user_profile()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users/' . $user->id);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $user->id,
                        'email' => $user->email
                    ]
                ]);
    }

    /** @test */
    public function returns_404_for_nonexistent_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_update_own_profile()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/users/' . $user->id, [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name' => 'Updated Name',
                        'email' => 'updated@example.com'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_profile()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $token = $user1->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/users/' . $user2->id, [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_any_user_profile()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/users/' . $user->id, [
            'name' => 'Admin Updated Name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Admin Updated Name'
        ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function regular_user_cannot_delete_other_users()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $token = $user1->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/users/' . $user2->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $user2->id]);
    }

    /** @test */
    public function user_cannot_delete_own_account()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(403);
    }
}