<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\Variant;
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
}
