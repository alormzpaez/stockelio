<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_variant(): void
    {
        $file = File::factory()->for(
            Variant::factory(), 'fileable'
        )->create();

        $this->assertInstanceOf(Variant::class, $file->fileable);
    }

    public function test_belongs_to_product(): void
    {
        $file = File::factory()->for(
            Product::factory(), 'fileable'
        )->create();

        $this->assertInstanceOf(Product::class, $file->fileable);
    }
}
