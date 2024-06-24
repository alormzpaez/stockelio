<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->words(3, true),
            'retail_price' => fake()->randomFloat(2, 1, 1000),
            'currency' => fake()->currencyCode(),
            'stripe_price_id' => fake()->text(50),
            'printful_sync_variant_id' => fake()->unique()->numberBetween(1),
        ];
    }
}
