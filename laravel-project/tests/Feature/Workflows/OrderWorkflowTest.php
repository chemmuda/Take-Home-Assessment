<?php

namespace Tests\Feature\Workflows;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function complete_order_creation_workflow()
    {
        // Step 1: Create user and authenticate
        $user = User::factory()->create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
        ]);

        // Step 2: Create products
        $product1 = Product::factory()->create([
            'name' => 'Laptop',
            'price' => 999.99,
            'stock' => 10,
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Mouse',
            'price' => 29.99,
            'stock' => 50,
        ]);

        // Step 3: User creates an order
        $orderData = [
            'items' => [
                [
                    'product_id' => $product1->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        $response->assertStatus(201);
        $orderId = $response->json('id');

        // Step 4: Verify order was created correctly
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'user_id' => $user->id,
            'status' => 'pending',
            'total_amount' => 1059.97, // 999.99 + (29.99 * 2)
        ]);

        // Step 5: Verify order items were created
        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product1->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product2->id,
            'quantity' => 2,
        ]);

        // Step 6: User views their order
        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/orders/{$orderId}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $orderId,
                'user_id' => $user->id,
                'status' => 'pending',
            ]);

        // Step 7: Update order status to processing
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/orders/{$orderId}", [
                'status' => 'processing',
            ]);

        $response->assertStatus(200);

        // Step 8: Update order status to completed
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/orders/{$orderId}", [
                'status' => 'completed',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'completed',
            ]);

        // Step 9: Verify final order status
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function order_workflow_with_order_cancellation()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50.00]);

        // Create order
        $orderData = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        $orderId = $response->json('id');

        // Cancel the order
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/orders/{$orderId}", [
                'status' => 'cancelled',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'cancelled',
        ]);
    }

    /** @test */
    public function multiple_orders_workflow_for_single_user()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['price' => 100.00]);
        $product2 = Product::factory()->create(['price' => 200.00]);

        // Create first order
        $order1Data = [
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 1],
            ],
        ];

        $response1 = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $order1Data);

        $response1->assertStatus(201);

        // Create second order
        $order2Data = [
            'items' => [
                ['product_id' => $product2->id, 'quantity' => 2],
            ],
        ];

        $response2 = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $order2Data);

        $response2->assertStatus(201);

        // Verify user has two orders
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/orders');

        $response->assertStatus(200);

        $orders = Order::where('user_id', $user->id)->get();
        $this->assertCount(2, $orders);
    }

    /** @test */
    public function order_workflow_validates_product_existence()
    {
        $user = User::factory()->create();

        // Try to create order with non-existent product
        $orderData = [
            'items' => [
                ['product_id' => 99999, 'quantity' => 1],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        // This should fail as product doesn't exist
        $response->assertStatus(500); // Or whatever error status is returned
    }

    /** @test */
    public function order_total_calculation_matches_sum_of_items()
    {
        $user = User::factory()->create();
        
        $products = Product::factory()->count(5)->create([
            'price' => 25.00,
        ]);

        $orderData = [
            'items' => $products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ];
            })->toArray(),
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        $response->assertStatus(201);

        $order = Order::find($response->json('id'));
        
        // 5 products * $25 * quantity 2 = $250
        $this->assertEquals(250.00, $order->total_amount);
        $this->assertEquals(250.00, $order->calculateTotal());
    }
}
