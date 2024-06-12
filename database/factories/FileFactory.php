<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileableType = Arr::random([
            Product::class,
            Variant::class,
        ]);

        $fileableId = ($fileableType == Product::class) ?
            Product::factory()
        :
            Variant::factory();

        return [
            'fileable_type' => $fileableType,
            'fileable_id' => $fileableId,
            'type' => fake()->words(2, true),
            'thumbnail_url' => fake()->imageUrl(),
            'preview_url' => fake()->imageUrl(),
            'filename' => fake()->name(),
            'mime_type' => fake()->mimeType(),
            'size' => fake()->numberBetween(1, 1000),
            'width' => fake()->numberBetween(1, 1000),
            'height' => fake()->numberBetween(1, 1000),
            'printful_file_id' => fake()->unique()->numberBetween(1),
        ];
    }
}
