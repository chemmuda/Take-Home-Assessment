<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\OrderItem;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_determine_if_product_is_in_stock()
    {
        $inStockProduct = Product::factory()->create(['stock' => 10]);
        $outOfStockProduct = Product::factory()->create(['stock' => 0]);

        $this->assertTrue($inStockProduct->isInStock());
        $this->assertFalse($outOfStockProduct->isInStock());
    }

    /** @test */
    public function it_has_order_items_relationship()
    {
        $product = Product::factory()->create();
        OrderItem::factory()->count(2)->create(['product_id' => $product->id]);

        $this->assertCount(2, $product->orderItems);
        $this->assertTrue($product->orderItems->first() instanceof OrderItem);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $product = Product::factory()->make([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 50,
            'category' => 'Electronics',
        ]);

        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals('Test Description', $product->description);
        $this->assertEquals(99.99, $product->price);
        $this->assertEquals(50, $product->stock);
        $this->assertEquals('Electronics', $product->category);
    }

    /** @test */
    public function it_can_be_created_with_minimum_required_fields()
    {
        $product = Product::factory()->create([
            'name' => 'Minimal Product',
            'price' => 10.00,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Minimal Product',
            'price' => 10.00,
        ]);
    }

    /** @test */
    public function it_returns_correct_stock_status()
    {
        $productWithStock = Product::factory()->create(['stock' => 5]);
        $productWithoutStock = Product::factory()->create(['stock' => 0]);
        $productWithHighStock = Product::factory()->create(['stock' => 100]);

        $this->assertTrue($productWithStock->isInStock());
        $this->assertFalse($productWithoutStock->isInStock());
        $this->assertTrue($productWithHighStock->isInStock());
    }
}
