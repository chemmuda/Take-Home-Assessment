<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_an_order()
    {
        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);

        $this->assertTrue($orderItem->order instanceof Order);
        $this->assertEquals($order->id, $orderItem->order->id);
    }

    /** @test */
    public function it_belongs_to_a_product()
    {
        $product = Product::factory()->create();
        $orderItem = OrderItem::factory()->create(['product_id' => $product->id]);

        $this->assertTrue($orderItem->product instanceof Product);
        $this->assertEquals($product->id, $orderItem->product->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create();
        
        $orderItem = OrderItem::factory()->make([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => 25.50,
        ]);

        $this->assertEquals($order->id, $orderItem->order_id);
        $this->assertEquals($product->id, $orderItem->product_id);
        $this->assertEquals(5, $orderItem->quantity);
        $this->assertEquals(25.50, $orderItem->price);
    }

    /** @test */
    public function it_stores_price_at_time_of_order()
    {
        $product = Product::factory()->create(['price' => 100.00]);
        $orderItem = OrderItem::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        // Product price changes after order
        $product->update(['price' => 150.00]);

        // Order item should still have original price
        $this->assertEquals(100.00, $orderItem->price);
        $this->assertEquals(150.00, $product->fresh()->price);
    }
}
