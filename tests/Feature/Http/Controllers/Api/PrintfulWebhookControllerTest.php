<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PrintfulWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $endpoint = 'api/printful/webhook';

    public function setUp(): void
    {
        parent::setUp();

        config([
            'cashier.secret' => 'test',
            'printful.key' => 'test',
        ]);
    }

    public function test_guest(): void
    {
        $this->getJson($this->endpoint)->assertMethodNotAllowed(); // index
        $this->getJson("{$this->endpoint}/1")->assertNotFound(); // show
        $this->postJson($this->endpoint)->assertUnprocessable(); // store
        $this->putJson("{$this->endpoint}/1")->assertNotFound(); // update
        $this->deleteJson("{$this->endpoint}/1")->assertNotFound(); // destroy
    }

    /**
     * Stripe price will be the same for all of the variants in this test.
     * That's the reason why all of the variants have the same stripe_price_id.
     * 
     * This test also assures that id's from printful and stripe don't mix with internal id's.
     */
    public function test_product_created(): void
    {
        $printfulProductBody = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/GetASyncProductOkResponse.json')), true);
        $stripeProductBody = json_decode(file_get_contents(base_path('tests/Fixtures/Stripe/CreateAProductOkResponse.json')), true);
        $stripePriceBody = json_decode(file_get_contents(base_path('tests/Fixtures/Stripe/CreateAPriceOkResponse.json')), true);

        Http::fake([
            'https://api.printful.com/store/products/*' => Http::response($printfulProductBody, 200),
            'https://api.stripe.com/v1/products' => Http::response($stripeProductBody, 200),
            'https://api.stripe.com/v1/prices' => Http::response($stripePriceBody, 200),
        ]);

        $this->assertDatabaseEmpty('products');
        $this->assertDatabaseEmpty('variants');

        // Printful brings this data
        $data = [
            'type' => 'product_updated',
            'created' => 1622456737,
            'retries' => 2,
            'store' => 12,
            'data' => [
                'sync_product' => $syncProduct = [
                    'id' => 346388995,
                    'external_id' => '4235234213',
                    'name' => 'T-shirt',
                    'variants' => 10,
                    'synced' => 10,
                    'thumbnail_url' => '​https://your-domain.com/path/to/image.png',
                    'is_ignored' => true,
                ]
            ],
        ];

        $this->postJson($this->endpoint, $data)->assertOk();

        $product = Product::first();

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', [
            ...Arr::only($syncProduct, [
                'name',
                'thumbnail_url',
            ]),
            'description' => '',
            'printful_product_id' => $syncProduct['id'],
            'stripe_product_id' => $stripeProductBody['id'],
        ]);
        $this->assertDatabaseMissing('products', Arr::only($syncProduct, 'id'));

        $this->assertDatabaseCount('variants', 2);
        $this->assertDatabaseHas('variants', [
            'currency' => $printfulProductBody['result']['sync_variants'][0]['currency'],
            'retail_price' => 29.99,
            'product_id' => $product->id,
            'printful_variant_id' => $printfulProductBody['result']['sync_variants'][0]['id'],
            'stripe_price_id' => $stripePriceBody['id'],
        ]);
        $this->assertDatabaseHas('variants', [
            'currency' => $printfulProductBody['result']['sync_variants'][1]['currency'],
            'retail_price' => 39.99,
            'product_id' => $product->id,
            'printful_variant_id' => $printfulProductBody['result']['sync_variants'][1]['id'],
            'stripe_price_id' => $stripePriceBody['id'],
        ]);
        $this->assertDatabaseMissing('variants', Arr::only(
            $printfulProductBody['result']['sync_variants'][0], 
            'id'
        ));
        $this->assertDatabaseMissing('variants', Arr::only(
            $printfulProductBody['result']['sync_variants'][1], 
            'id'
        ));

        $firstVariant = Variant::where('retail_price', 29.99)->with('files')->first();
        $arrayFirstFilesIds = $firstVariant->files->map(fn ($file) => $file->printful_file_id)->toArray();
        $secondVariant = Variant::where('retail_price', 39.99)->with('files')->first();
        $arraySecondFilesIds = $secondVariant->files->map(fn ($file) => $file->printful_file_id)->toArray();

        $mergedFilesIds = array_merge($arrayFirstFilesIds, $arraySecondFilesIds);

        $this->assertDatabaseCount('files', 4);
        $this->assertCount(2, $firstVariant->files);
        $this->assertContains(
            $printfulProductBody['result']['sync_variants'][0]['files'][0]['id'],
            $arrayFirstFilesIds,
        );
        $this->assertContains(
            $printfulProductBody['result']['sync_variants'][0]['files'][1]['id'],
            $arrayFirstFilesIds,
        );
        $this->assertCount(2, $secondVariant->files);
        $this->assertContains(
            $printfulProductBody['result']['sync_variants'][1]['files'][0]['id'],
            $arraySecondFilesIds,
        );
        $this->assertContains(
            $printfulProductBody['result']['sync_variants'][1]['files'][1]['id'],
            $arraySecondFilesIds,
        );
        $this->assertNotContains($firstVariant->files->get(0)->id, $mergedFilesIds);
        $this->assertNotContains($firstVariant->files->get(1)->id, $mergedFilesIds);
        $this->assertNotContains($secondVariant->files->get(0)->id, $mergedFilesIds);
        $this->assertNotContains($secondVariant->files->get(1)->id, $mergedFilesIds);
    }

    public function test_product_created_with_error_when_post_stripe_product(): void
    {
        $printfulProductBody = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/GetASyncProductOkResponse.json')), true);
        $stripeProductBody = json_decode(file_get_contents(base_path('tests/Fixtures/Stripe/CreateAProductOkResponse.json')), true);
        $stripePriceBody = json_decode(file_get_contents(base_path('tests/Fixtures/Stripe/CreateAPriceOkResponse.json')), true);

        // Instead of 200, simply replaced with a 40x error
        Http::fake([
            'https://api.printful.com/store/products/*' => Http::response($printfulProductBody, 200),
            'https://api.stripe.com/v1/products' => Http::response($stripeProductBody, 400),
            'https://api.stripe.com/v1/prices' => Http::response($stripePriceBody, 200),
        ]);

        $this->assertDatabaseEmpty('products');
        $this->assertDatabaseEmpty('variants');

        // Printful brings this data
        $data = [
            'type' => 'product_updated',
            'created' => 1622456737,
            'retries' => 2,
            'store' => 12,
            'data' => [
                'sync_product' => $syncProduct = [
                    'id' => 346388995,
                    'external_id' => '4235234213',
                    'name' => 'T-shirt',
                    'variants' => 10,
                    'synced' => 10,
                    'thumbnail_url' => '​https://your-domain.com/path/to/image.png',
                    'is_ignored' => true,
                ]
            ],
        ];

        $this->postJson($this->endpoint, $data)->assertServerError();

        $this->assertDatabaseEmpty('products');
        $this->assertDatabaseEmpty('variants');
    }

    public function test_product_created_with_error_when_get_printful_product(): void
    {
        $printfulProductBody = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/GetASyncProductOkResponse.json')), true);
        $stripeProductBody = json_decode(file_get_contents(base_path('tests/Fixtures/Stripe/CreateAProductOkResponse.json')), true);
        $stripePriceBody = json_decode(file_get_contents(base_path('tests/Fixtures/Stripe/CreateAPriceOkResponse.json')), true);

        // Instead of 200, simply replaced with a 40x error
        Http::fake([
            'https://api.printful.com/store/products/*' => Http::response($printfulProductBody, 400),
            'https://api.stripe.com/v1/products' => Http::response($stripeProductBody, 200),
            'https://api.stripe.com/v1/prices' => Http::response($stripePriceBody, 200),
        ]);

        $this->assertDatabaseEmpty('products');
        $this->assertDatabaseEmpty('variants');

        // Printful brings this data
        $data = [
            'type' => 'product_updated',
            'created' => 1622456737,
            'retries' => 2,
            'store' => 12,
            'data' => [
                'sync_product' => $syncProduct = [
                    'id' => 346388995,
                    'external_id' => '4235234213',
                    'name' => 'T-shirt',
                    'variants' => 10,
                    'synced' => 10,
                    'thumbnail_url' => '​https://your-domain.com/path/to/image.png',
                    'is_ignored' => true,
                ]
            ],
        ];

        $this->postJson($this->endpoint, $data)->assertServerError();

        $this->assertDatabaseEmpty('products');
        $this->assertDatabaseEmpty('variants');
    }

    public function test_product_created_with_error_when_post_stripe_price(): void
    {
        $printfulProductBody = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/GetASyncProductOkResponse.json')), true);
        $stripeProductBody = json_decode(file_get_contents(base_path('tests/Fixtures/Stripe/CreateAProductOkResponse.json')), true);
        $stripePriceBody = json_decode(file_get_contents(base_path('tests/Fixtures/Stripe/CreateAPriceOkResponse.json')), true);

        // Instead of 200, simply replaced with a 40x error
        Http::fake([
            'https://api.printful.com/store/products/*' => Http::response($printfulProductBody, 200),
            'https://api.stripe.com/v1/products' => Http::response($stripeProductBody, 200),
            'https://api.stripe.com/v1/prices' => Http::response($stripePriceBody, 400),
        ]);

        $this->assertDatabaseEmpty('products');
        $this->assertDatabaseEmpty('variants');

        // Printful brings this data
        $data = [
            'type' => 'product_updated',
            'created' => 1622456737,
            'retries' => 2,
            'store' => 12,
            'data' => [
                'sync_product' => $syncProduct = [
                    'id' => 346388995,
                    'external_id' => '4235234213',
                    'name' => 'T-shirt',
                    'variants' => 10,
                    'synced' => 10,
                    'thumbnail_url' => '​https://your-domain.com/path/to/image.png',
                    'is_ignored' => true,
                ]
            ],
        ];

        $this->postJson($this->endpoint, $data)->assertServerError();

        $this->assertDatabaseEmpty('products');
        $this->assertDatabaseEmpty('variants');
    }
}
