<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintfulWebhookRequest;
use App\Models\Product;
use App\Services\PrintfulService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PrintfulWebhookController extends Controller
{
    public function __construct(
        private StripeService $stripeService,
        private PrintfulService $printfulService,
    ) {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(PrintfulWebhookRequest $request)
    {
        DB::beginTransaction();

        try {
            if ($request->validated('type') == 'product_updated') {
                $productRequest = $request->validated('data')['sync_product'];

                if (!(
                    $product = Product::where('printful_sync_product_id', $productRequest['id'])->first()
                )) {
                    $response = $this->stripeService->createAProduct($productRequest['name']);

                    $product = Product::create([
                        ...Arr::only($productRequest, [
                            'name',
                            'thumbnail_url',
                        ]),
                        'description' => '',
                        'stripe_product_id' => $response['id'],
                        'printful_sync_product_id' => $productRequest['id'],
                    ]);

                    $response = $this->printfulService->getASyncProduct($product->printful_sync_product_id);

                    $variantsRequest = $response['result']['sync_variants'];

                    foreach ($variantsRequest as $variantRequest) {
                        $response = $this->stripeService->createAPrice(
                            $variantRequest['currency'],
                            intval(doubleval($variantRequest['retail_price']) * 100),
                            $product->stripe_product_id,
                        );

                        $product->variants()->create([
                            'name' => $variantRequest['name'],
                            'currency' => $variantRequest['currency'],
                            'retail_price' => doubleval($variantRequest['retail_price']),
                            'stripe_price_id' => $response['id'],
                            'printful_sync_variant_id' => $variantRequest['id'],
                            'printful_variant_id' => $variantRequest['variant_id'],
                        ]);
                    }
                } else {
                    // Just update the new data for the product
                    $product->update(Arr::only($productRequest, 'thumbnail_url'));
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        DB::commit();

        return response()->noContent(200);
    }
}
