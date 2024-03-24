<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_many_variants(): void
    {
        $product = Product::factory()->hasVariants()->create();

        $this->assertInstanceOf(Collection::class, $product->variants);
        $this->assertInstanceOf(Variant::class, $product->variants->get(0));
    }
}
