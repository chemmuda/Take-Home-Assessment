<?php

namespace Tests\Feature\Workflows;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserManagementWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function complete_user_lifecycle_workflow()
    {
        // Step 1: Admin creates/exists
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
        ]);

        // Step 2: View all users
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200);

        // Step 3: View specific user
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/users/{$admin->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $admin->id,
                'email' => 'admin@example.com',
            ]);

        // Step 4: Update user profile
        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$admin->id}", [
                'name' => 'Updated Admin Name',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Updated Admin Name',
        ]);

        // Step 5: Create another user for deletion test
        $userToDelete = User::factory()->create();

        // Step 6: Admin deletes the user
        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$userToDelete->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
        ]);
    }

    /** @test */
    public function regular_user_workflow_with_restrictions()
    {
        // Create regular user
        $user = User::factory()->create([
            'role' => 'user',
            'email' => 'user@example.com',
        ]);

        // User can view own profile
        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);

        // User can update own profile
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/users/{$user->id}", [
                'name' => 'Updated User Name',
            ]);

        $response->assertStatus(200);

        // Create another user
        $otherUser = User::factory()->create();

        // User cannot view other user's profile
        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/users/{$otherUser->id}");

        $response->assertStatus(403);

        // User cannot update other user's profile
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/users/{$otherUser->id}", [
                'name' => 'Hacked Name',
            ]);

        $response->assertStatus(403);

        // User cannot delete other user
        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/users/{$otherUser->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_manage_multiple_users()
    {
        $admin = User::factory()->admin()->create();

        // Create multiple users
        $users = User::factory()->count(5)->create();

        // Admin can list all users
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200);

        $allUsers = User::all();
        $this->assertCount(6, $allUsers); // 5 users + 1 admin

        // Admin can update any user
        $userToUpdate = $users->first();
        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$userToUpdate->id}", [
                'name' => 'Admin Updated Name',
            ]);

        $response->assertStatus(200);

        // Admin can delete any user
        $userToDelete = $users->last();
        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$userToDelete->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
        ]);
    }

    /** @test */
    public function user_profile_update_maintains_data_integrity()
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'role' => 'user',
        ]);

        // Update user profile
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/users/{$user->id}", [
                'name' => 'New Name',
                'email' => 'newemail@example.com',
            ]);

        $response->assertStatus(200);

        // Verify changes persisted
        $updatedUser = User::find($user->id);
        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertEquals('newemail@example.com', $updatedUser->email);
        $this->assertEquals('user', $updatedUser->role); // Role should remain unchanged
    }

    /** @test */
    public function user_cannot_escalate_privileges()
    {
        $user = User::factory()->create(['role' => 'user']);

        // Try to update own role to admin
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/users/{$user->id}", [
                'role' => 'admin',
            ]);

        // Even if this succeeds, verify role wasn't changed improperly
        $updatedUser = User::find($user->id);
        
        // Note: This test assumes there's validation preventing role changes
        // Adjust based on actual implementation
        $this->assertEquals('user', $updatedUser->role);
    }
}
