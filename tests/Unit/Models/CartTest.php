<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_belongs_to_user(): void
    {
        $cart = Cart::factory()->create();

        $this->assertInstanceOf(User::class, $cart->user);
    }

    public function test_has_many_orders(): void
    {
        $cart = Cart::factory()->hasOrders()->create();

        $this->assertInstanceOf(Collection::class, $cart->orders);
        $this->assertInstanceOf(Order::class, $cart->orders->get(0));
    }
    
    public function test_has_many_incart_orders(): void
    {
        $cart = Cart::factory()->create();

        Order::factory()->for($cart)->create([
            'status' => Order::FULFILLED_STATUS,
        ]);
        $order = Order::factory()->for($cart)->create([
            'status' => Order::IN_CART_STATUS,
        ]);

        $cart->load('incartOrders');

        $this->assertInstanceOf(Collection::class, $cart->incartOrders);
        $this->assertCount(1, $cart->incartOrders);
        $this->assertInstanceOf(Order::class, $cart->incartOrders->get(0));
        $this->assertEquals($cart->incartOrders->get(0)->id, $order->id);
    }
}
