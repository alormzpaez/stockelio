<?php

namespace Database\Factories;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'id' => fake()->unique()->numberBetween(),
            'variant_id' => Variant::factory(),
            'url' => fake()->url(),
            'filename' => fake()->name(),
            'mime_type' => fake()->mimeType(),
            'size' => fake()->numberBetween(1, 1000),
            'width' => fake()->numberBetween(1, 1000),
            'height' => fake()->numberBetween(1, 1000),
            'dpi' => fake()->numberBetween(1, 1000),
            'stitch_count_tier' => fake()->word(),
        ];
    }
}
