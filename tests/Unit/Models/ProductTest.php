<?php

namespace Tests\Unit\Models;

use App\Models\File;
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

    public function test_has_many_files(): void
    {
        $product = Product::factory()->hasFiles()->create();

        $this->assertInstanceOf(Collection::class, $product->files);
        $this->assertInstanceOf(File::class, $product->files->get(0));
    }
}
