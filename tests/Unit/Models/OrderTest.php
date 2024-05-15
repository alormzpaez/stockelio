<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_cart(): void
    {
        $order = Order::factory()->create();

        $this->assertInstanceOf(Cart::class, $order->cart);
    }

    public function test_belongs_to_variant(): void
    {
        $order = Order::factory()->create();

        $this->assertInstanceOf(Variant::class, $order->variant);
    }
}
