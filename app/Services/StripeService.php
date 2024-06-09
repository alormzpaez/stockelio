<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class StripeService
{
    /**
     * Returns the created product.
     */
    public function createAProduct(string $name): array
    {
        $response = Http::asForm()->withHeaders([
            'Authorization' => 'Bearer ' . config('cashier.secret'),
        ])->post('https://api.stripe.com/v1/products', [
            'name' => $name,
        ]);

        if ($response->status() != 200) {
            throw new \Exception('Error in external api.');
        }

        return $response->json();
    }

    /**
     * Returns the created price.
     */
    public function createAPrice(string $currency, int $unitAmount, string $stripeProductId): array
    {
        $response = Http::asForm()->withHeaders([
            'Authorization' => 'Bearer ' . config('cashier.secret'),
        ])->post('https://api.stripe.com/v1/prices', [
            'currency' => $currency,
            'unit_amount' => $unitAmount,
            'product' => $stripeProductId,
        ]);

        if ($response->status() != 200) {
            throw new \Exception('Error in external api.');
        }

        return $response->json();
    }
}
