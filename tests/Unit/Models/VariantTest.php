<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_product(): void
    {
        $variant = Variant::factory()->create();

        $this->assertInstanceOf(Product::class, $variant->product);
    }

    public function test_doesnt_have_many_files(): void
    {
        $variant = Variant::factory()->create();

        $this->assertNull($variant->files);
    }

    public function test_has_many_orders(): void
    {
        $variant = Variant::factory()->hasOrders()->create();

        $this->assertInstanceOf(Collection::class, $variant->orders);
        $this->assertInstanceOf(Order::class, $variant->orders->get(0));
    }
}
