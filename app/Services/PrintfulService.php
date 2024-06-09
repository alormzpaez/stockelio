<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PrintfulService
{
    /**
     * Returns the sync product.
     */
    public function getASyncProduct(int $productId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('printful.key'),
            'X-PF-Language' => 'es_ES',
        ])->get('https://api.printful.com/store/products/' . $productId);

        if ($response->status() != 200) {
            throw new \Exception('Error in external api.');
        }

        return $response->json();
    }
}
