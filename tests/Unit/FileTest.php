<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_variant(): void
    {
        $file = File::factory()->create();

        $this->assertInstanceOf(Variant::class, $file->variant);
    }
}
