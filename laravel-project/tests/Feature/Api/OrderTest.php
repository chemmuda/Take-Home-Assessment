<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_list_all_orders()
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function unauthenticated_user_cannot_list_orders()
    {
        Order::factory()->count(2)->create();

        $response = $this->getJson('/api/orders');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_view_single_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_amount' => 100.00,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $order->id,
                'status' => 'pending',
                'total_amount' => 100.00,
            ]);
    }

    /** @test */
    public function authenticated_user_can_create_order()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['price' => 50.00]);
        $product2 = Product::factory()->create(['price' => 30.00]);

        $orderData = [
            'items' => [
                [
                    'product_id' => $product1->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'status',
                'total_amount',
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
            'total_amount' => 130.00, // (50 * 2) + (30 * 1)
        ]);
    }

    /** @test */
    public function order_creation_requires_items()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    /** @test */
    public function order_items_require_product_id_and_quantity()
    {
        $user = User::factory()->create();
        $orderData = [
            'items' => [
                [
                    'product_id' => 1,
                    // missing quantity
                ],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        $response->assertStatus(422);
    }

    /** @test */
    public function authenticated_user_can_update_order_status()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/orders/{$order->id}", [
                'status' => 'completed',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'completed',
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function order_calculates_total_correctly_with_multiple_items()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['price' => 25.00]);
        $product2 = Product::factory()->create(['price' => 15.50]);
        $product3 = Product::factory()->create(['price' => 10.00]);

        $orderData = [
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 3], // 75.00
                ['product_id' => $product2->id, 'quantity' => 2], // 31.00
                ['product_id' => $product3->id, 'quantity' => 5], // 50.00
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 156.00, // 75 + 31 + 50
        ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_order()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/orders/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function order_with_items_creates_order_items_records()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);

        $orderData = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        $response->assertStatus(201);

        $orderId = $response->json('id');

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);
    }

    /** @test */
    public function get_order_stats_returns_orders_by_status()
    {
        $user = User::factory()->create();
        Order::factory()->pending()->count(3)->create();
        Order::factory()->completed()->count(2)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/orders/stats?status=pending');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function order_includes_user_relationship()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'John Doe',
            ]);
    }
}
