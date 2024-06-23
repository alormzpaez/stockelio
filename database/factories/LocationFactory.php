<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'country_name' => fake()->country(),
            'country_code' => fake()->countryCode(),
            'state_name' => fake()->word(),
            'city' => fake()->city(),
            'locality' => fake()->word(),
            'address' => fake()->streetAddress(),
            'zip' => fake()->postcode(),
            'phone' => fake()->e164PhoneNumber(),
        ];
    }
}
