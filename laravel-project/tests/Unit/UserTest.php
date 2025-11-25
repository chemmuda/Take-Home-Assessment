<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_determine_if_user_is_admin()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        $regularUser = User::factory()->create(['role' => 'user']);

        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($regularUser->isAdmin());
    }

    /** @test */
    public function it_has_orders_relationship()
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->orders);
        $this->assertTrue($user->orders->first() instanceof Order);
    }

    /** @test */
    public function it_hides_password_in_array()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
    }

    /** @test */
    public function it_hides_remember_token_in_array()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = User::factory()->make([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'admin',
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('admin', $user->role);
    }

    /** @test */
    public function it_defaults_to_user_role()
    {
        $user = User::factory()->create();
        
        $this->assertEquals('user', $user->role);
    }
}
