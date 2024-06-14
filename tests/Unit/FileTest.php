<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Str;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_product(): void
    {
        $file = File::factory()->create();

        $this->assertInstanceOf(Product::class, $file->product);
    }

    public function test_url(): void
    {
        Storage::fake('products');

        $product = Product::factory()->create();

        $filename = Storage::disk('products')->put(
            $product->id, 
            UploadedFile::fake()->create('image.png', 0, 'image/png')
        );

        $file = File::factory()->for($product)->create([
            'filename' => $filename
        ]);

        $this->assertTrue(Str::startsWith($file->url, config('app.url')));
        $this->assertTrue(Str::endsWith($file->url, '.png'));
    }
}
