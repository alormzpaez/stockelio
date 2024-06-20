<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class PrintfulService
{
    /**
     * Returns the sync product.
     */
    public function getASyncProduct(int $printfulProductId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('printful.key'),
            'X-PF-Language' => 'es_ES',
        ])->get('https://api.printful.com/store/products/' . $printfulProductId);

        if ($response->status() != 200) {
            throw new \Exception('Error getting the sync product from printful.');
        }

        return $response->json();
    }

    /**
     * Returns the order.
     */
    public function createANewOrder(int $userId, int $printfulVariantId, int $quantity): array
    {
        $user = User::find($userId);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('printful.key'),
            'X-PF-Language' => 'es_ES',
        ])->post('https://api.printful.com/orders', [
            'recipient' => $user->personalData,
            'items' => [
                [
                    'sync_variant_id' => $printfulVariantId,
                    'quantity' => $quantity,
                ]
            ]
        ]);

        if ($response->status() != 200) {
            throw new \Exception('Error creating a new order in printful.');
        }

        return $response->json();
    }
}
