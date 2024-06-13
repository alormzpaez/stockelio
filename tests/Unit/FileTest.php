<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_product(): void
    {
        $file = File::factory()->create();

        $this->assertInstanceOf(Product::class, $file->product);
    }
}
