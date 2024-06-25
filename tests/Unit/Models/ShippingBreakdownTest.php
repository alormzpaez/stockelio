<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\ShippingBreakdown;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingBreakdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_order(): void
    {
        $shippingBreakdown = ShippingBreakdown::factory()->create();

        $this->assertInstanceOf(Order::class, $shippingBreakdown->order);
    }
}
