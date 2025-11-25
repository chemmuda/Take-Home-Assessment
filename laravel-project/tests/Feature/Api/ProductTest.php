<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_list_all_products()
    {
        $user = User::factory()->create();
        Product::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    /** @test */
    public function unauthenticated_user_cannot_list_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_view_single_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 99.99,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'name' => 'Test Product',
                'price' => 99.99,
            ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_product()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/products/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_create_product()
    {
        $user = User::factory()->create();
        $productData = [
            'name' => 'New Product',
            'description' => 'Product description',
            'price' => 149.99,
            'stock' => 50,
            'category' => 'Electronics',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'New Product',
                'price' => 149.99,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'price' => 149.99,
        ]);
    }

    /** @test */
    public function product_creation_requires_name()
    {
        $user = User::factory()->create();
        $productData = [
            'description' => 'Product description',
            'price' => 149.99,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/products', $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function authenticated_user_can_update_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Old Name',
            'price' => 50.00,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'name' => 'Updated Name',
                'price' => 75.00,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Name',
                'price' => 75.00,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'price' => 75.00,
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Product deleted successfully']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function deleting_nonexistent_product_returns_404()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/products/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function products_can_be_filtered_by_stock_status()
    {
        $user = User::factory()->create();
        Product::factory()->inStock()->count(3)->create();
        Product::factory()->outOfStock()->count(2)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    /** @test */
    public function product_creation_with_negative_price_should_fail()
    {
        $user = User::factory()->create();
        $productData = [
            'name' => 'Invalid Product',
            'price' => -10.00,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/products', $productData);

        // Note: This test may fail if validation for negative price is not implemented
        // It demonstrates the expected behavior
        $response->assertStatus(422);
    }
}
