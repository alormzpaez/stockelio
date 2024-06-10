<?php

namespace App\Services;

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
}
