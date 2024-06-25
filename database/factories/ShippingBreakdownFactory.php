<?php

namespace Database\Factories;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingBreakdown>
 */
class ShippingBreakdownFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'rate' => fake()->randomFloat(2, 1, 500),
            'min_delivery_days' => $min = fake()->numberBetween(1, 5),
            'max_delivery_days' => $max = fake()->numberBetween(6, 10),
            'min_delivery_date' => $minDate = fake()->date(),
            'max_delivery_date' => Carbon::parse($minDate)->addDays($max - $min)->format('Y-m-d'),
        ];
    }
}
