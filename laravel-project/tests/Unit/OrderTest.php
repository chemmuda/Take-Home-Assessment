<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($order->user instanceof User);
        $this->assertEquals($user->id, $order->user->id);
    }

    /** @test */
    public function it_has_many_order_items()
    {
        $order = Order::factory()->create();
        OrderItem::factory()->count(3)->create(['order_id' => $order->id]);

        $this->assertCount(3, $order->items);
        $this->assertTrue($order->items->first() instanceof OrderItem);
    }

    /** @test */
    public function it_calculates_total_correctly()
    {
        $order = Order::factory()->create();
        
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'quantity' => 2,
            'price' => 10.00,
        ]);
        
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'quantity' => 3,
            'price' => 15.00,
        ]);

        $total = $order->calculateTotal();
        
        $this->assertEquals(65.00, $total); // (2 * 10) + (3 * 15) = 65
    }

    /** @test */
    public function it_calculates_zero_for_order_with_no_items()
    {
        $order = Order::factory()->create();

        $total = $order->calculateTotal();
        
        $this->assertEquals(0, $total);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = User::factory()->create();
        $order = Order::factory()->make([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_amount' => 100.00,
        ]);

        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(100.00, $order->total_amount);
    }

    /** @test */
    public function it_can_have_different_statuses()
    {
        $pendingOrder = Order::factory()->pending()->create();
        $completedOrder = Order::factory()->completed()->create();
        $cancelledOrder = Order::factory()->cancelled()->create();

        $this->assertEquals('pending', $pendingOrder->status);
        $this->assertEquals('completed', $completedOrder->status);
        $this->assertEquals('cancelled', $cancelledOrder->status);
    }
}
