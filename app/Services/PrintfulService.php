<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class PrintfulService
{
    /**
     * Returns the sync product.
     */
    public function getASyncProduct(int $printfulSyncProductId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('printful.key'),
            'X-PF-Language' => 'es_ES',
        ])->get('https://api.printful.com/store/products/' . $printfulSyncProductId);

        if ($response->status() != 200) {
            throw new \Exception('Error getting the sync product from printful.');
        }

        return $response->json();
    }

    /**
     * Returns the order.
     */
    public function createANewOrder(int $userId, int $printfulSyncVariantId, int $quantity): array
    {
        $user = User::find($userId);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('printful.key'),
            'X-PF-Language' => 'es_ES',
        ])->post('https://api.printful.com/orders', [
            'recipient' => $this->getRecipientFromPreferredLocation($user->id),
            'items' => [
                [
                    'sync_variant_id' => $printfulSyncVariantId,
                    'quantity' => $quantity,
                ]
            ]
        ]);

        if ($response->status() != 200) {
            throw new \Exception('Error creating a new order in printful.');
        }

        return $response->json();
    }

    /**
     * Transform the preferred location of user to an understandable format for Printful.
     */
    private function getRecipientFromPreferredLocation(int $userId): array
    {
        $user = User::with('preferredLocation')->find($userId);

        return [
            'name' => $user->name,
            'address1' => $user->preferredLocation->full_address,
            'city' => $user->preferredLocation->city,
            'country_code' => $user->preferredLocation->country_code,
            'zip' => $user->preferredLocation->zip,
        ];
    }

    /**
     * Returns the shipping rate for the location of user.
     */
    public function calculateShippingRate(int $userId, int $printfulVariantId, int $quantity): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('printful.key'),
            'X-PF-Language' => 'es_ES',
        ])->post('https://api.printful.com/shipping/rates', [
            'recipient' => $this->getRecipientFromPreferredLocation($userId),
            'items' => [
                [
                    'variant_id' => $printfulVariantId,
                    'quantity' => $quantity,
                ]
            ],
            'currency' => 'MXN',
            'locale' => 'es_ES',
        ]);

        if (
            $response->status() != 200 ||
            empty($response->json())
        ) {
            throw new \Exception('Error getting shipping rate for order in printful.');
        }

        return $response->json();
    }
}
