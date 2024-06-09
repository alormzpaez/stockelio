<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->numberBetween(1),
            'name' => fake()->words(2, true),
            'thumbnail_url' => fake()->imageUrl(),
            'description' => fake()->paragraphs(3, true),
            'stripe_product_id' => fake()->text(50),
        ];
    }
}
