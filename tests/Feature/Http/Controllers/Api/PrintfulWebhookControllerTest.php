<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Product;
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
                'id',
                'name',
                'thumbnail_url',
            ]),
            'description' => '',
            'stripe_product_id' => $stripeProductBody['id'],
        ]);
        $this->assertDatabaseCount('variants', 1);
        $this->assertDatabaseHas('variants', [
            ...Arr::only($printfulProductBody['result']['sync_variants'][0], [
                'id',
                'currency',
            ]),
            'retail_price' => 29.99,
            'product_id' => $product->id,
            'stripe_price_id' => $stripePriceBody['id'],
        ]);
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
