<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Cart;
use App\Models\Location;
use App\Models\Order;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public string $url = '/orders';

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
        $order = Order::factory()->create();

        $this->get($this->url)->assertRedirect(route('login')); // index
        $this->get("{$this->url}/{$order->id}")->assertRedirect(route('login')); // show
        $this->get("{$this->url}/create")->assertRedirect(route('login')); // create
        $this->post($this->url)->assertRedirect(route('login')); // post
        $this->get("{$this->url}/edit")->assertRedirect(route('login')); // edit
        $this->put("{$this->url}/{$order->id}")->assertRedirect(route('login')); // update
        $this->delete("{$this->url}/{$order->id}")->assertRedirect(route('login')); // destroy
    }

    public function test_user_without_contact_details_filled(): void
    {
        Sanctum::actingAs($user = User::factory()
            ->has(Cart::factory()->hasOrders())
        ->createQuietly());

        $order = $user->cart->orders->get(0);
        $order->load('variant');

        $this->get(route('products.show', $order->variant->product_id))->assertOk();

        $this->get($this->url)->assertOk(); // index
        $this->get("{$this->url}/{$order->id}")->assertOk(); // show
        $this->get("{$this->url}/create")->assertNotFound(); // create

        $this->get(route('products.show', $order->variant->product_id))->assertOk();
        $this->post($this->url)
            ->assertRedirect(route('products.show', $order->variant->product_id))
            ->assertSessionHas('type', 'error')
        ->assertSessionHas('message', 'Es necesario llenar los datos de dirección primero.'); // post

        $this->get("{$this->url}/edit")->assertNotFound(); // edit

        $this->get(route('carts.show', $user->cart->id))->assertOk();
        $this->put("{$this->url}/{$order->id}")
            ->assertRedirect(route('carts.show', $user->cart->id))
            ->assertSessionHas('type', 'error')
        ->assertSessionHas('message', 'Es necesario llenar los datos de dirección primero.'); // update
        
        $this->delete("{$this->url}/{$order->id}")->assertRedirect(); // destroy
    }

    public function test_user_with_contact_details_filled(): void
    {
        Sanctum::actingAs($user = User::factory()
            ->has(Cart::factory()->hasOrders())
            ->withPreferredLocation()
        ->createQuietly());

        $order = $user->cart->orders->get(0);

        $this->get($this->url)->assertOk(); // index
        $this->get("{$this->url}/{$order->id}")->assertOk(); // show
        $this->get("{$this->url}/create")->assertNotFound(); // create
        $this->post($this->url)->assertInvalid(); // post
        $this->get("{$this->url}/edit")->assertNotFound(); // edit
        $this->put("{$this->url}/{$order->id}")->assertInvalid(); // update
        $this->delete("{$this->url}/{$order->id}")->assertRedirect(); // destroy
    }

    public function test_index(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $order1 = Order::factory()
            ->for($user->cart)
            ->for(Variant::factory()->state([
                'retail_price' => 100
            ]))
        ->create([
            'quantity' => 2,
            'status' => Order::PENDING_STATUS,
        ]);
        $order2 = Order::factory()
            ->for($user->cart)
            ->for(Variant::factory()->state([
                'retail_price' => 50
            ]))
        ->create([
            'quantity' => 3,
            'status' => Order::PENDING_STATUS,
        ]);

        // This won't be appear, because doesn't have 'pending' status (the default status required in this endpoint).
        Order::factory()
            ->for($user->cart)
        ->create();

        $this->get(route('orders.index'))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Orders/Index')
            ->has('orders', 2, fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('variant_id')
                    ->has('quantity')
                    ->has('status')
                    ->has('total')
                ->has('variant', fn (AssertableInertia $page) =>
                    $page->has('id') 
                        ->has('name')
                        ->has('product_id')
                        ->has('retail_price') 
                    ->has('product', fn (AssertableInertia $page) => 
                        $page->has('id')
                        ->has('thumbnail_url')
                    )
                )
            )
            ->where('orders.0.id', $order2->id)
            ->where('orders.0.total', 150)
            ->where('orders.1.id', $order1->id)
            ->where('orders.1.total', 200)
        );
    }

    public function test_show(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $order = Order::factory()
            ->for($user->cart)
        ->create([
            'status' => Order::PENDING_STATUS,
        ]);

        $this->get(route('orders.show', $order->id))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Orders/Show')
            ->has('order', fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('variant_id')
                    ->has('quantity')
                    ->has('status')
                ->has('variant', fn (AssertableInertia $page) =>
                    $page->has('id') 
                        ->has('name')
                        ->has('product_id')
                        ->has('retail_price') 
                    ->has('product', fn (AssertableInertia $page) => 
                        $page->has('id')
                        ->has('thumbnail_url')
                    )
                )
            )
        );
    }

    public function test_store(): void
    {
        $shippingRateBody = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/CalculateShippingRateOkResponse.json')), true);

        Http::fake([
            'https://api.printful.com/shipping/rates' => Http::response($shippingRateBody, 200),
        ]);

        Sanctum::actingAs($user = User::factory()
            ->has(Location::factory()->state([
                'country_code' => 'MX',
                'is_preferred' => true,
            ]))
        ->create());
        $cart = $user->cart;
        $cart->load('orders');
        $variant = Variant::factory()->create();

        $this->get(route('products.show', $variant->product_id))->assertOk();

        $this->assertDatabaseEmpty('orders');
        $this->assertDatabaseEmpty('shipping_breakdowns');
        $this->assertEmpty($cart->orders);

        $data = [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ];

        $this->post($this->url, $data)
            ->assertValid()
            ->assertRedirect(route('products.show', $variant->product_id))
        ->assertSessionHas('message', 'Producto agregado a tu carrito.');

        $cart->load([
            'orders.shippingBreakdown',
            'orders.variant'
        ]);
        $order = $cart->orders->get(0);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('shipping_breakdowns', 1);
        $this->assertCount(1, $cart->orders);
        $this->assertEquals($order->shippingBreakdown->rate, 13.60);
        $this->assertEquals($order->shippingBreakdown->min_delivery_days, 4);
        $this->assertEquals($order->shippingBreakdown->max_delivery_days, 7);
        $this->assertEquals($order->shippingBreakdown->min_delivery_date, '2022-10-17');
        $this->assertEquals($order->shippingBreakdown->max_delivery_date, '2022-10-20');

        Http::assertSent(function (Request $request) use ($user, $order) {
            $body = json_decode($request->body(), true);

            return $request->method() == 'POST' &&
                $request->url() == 'https://api.printful.com/shipping/rates' &&
                $body['recipient']['address1'] == $user->preferredLocation->full_address &&
                $body['recipient']['country_code'] == 'MX' &&
                $body['items'][0]['variant_id'] == $order->variant->printful_variant_id &&
                $body['items'][0]['quantity'] == $order->quantity &&
                $body['currency'] == 'MXN' &&
            $body['locale'] == 'es_ES';
        });
    }

    public function test_store_with_error_calculating_shipping_rates(): void
    {
        $this->withoutExceptionHandling();

        $shippingRateBody = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/CalculateShippingRateOkResponse.json')), true);

        Http::fake([
            'https://api.printful.com/shipping/rates' => Http::response($shippingRateBody, 400),
        ]);

        Sanctum::actingAs($user = User::factory()
            ->has(Location::factory()->state([
                'country_code' => 'MX',
                'is_preferred' => true,
            ]))
        ->create());
        $cart = $user->cart;
        $cart->load('orders');
        $variant = Variant::factory()->create();

        $this->get(route('products.show', $variant->product_id))->assertOk();

        $this->assertDatabaseEmpty('orders');
        $this->assertDatabaseEmpty('shipping_breakdowns');
        $this->assertEmpty($cart->orders);

        $data = [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ];

        $this->post($this->url, $data)
            ->assertValid()
            ->assertRedirect(route('products.show', $variant->product_id))
            ->assertSessionHas('message', 'Error calculando los gastos de envío.')
        ->assertSessionHas('type', 'error');

        $this->assertDatabaseEmpty('orders');
        $this->assertDatabaseEmpty('shipping_breakdowns');
        $this->assertEmpty($cart->orders);

        Http::assertSent(function (Request $request) use ($user, $variant) {
            $body = json_decode($request->body(), true);

            return $request->method() == 'POST' &&
                $request->url() == 'https://api.printful.com/shipping/rates' &&
                $body['recipient']['address1'] == $user->preferredLocation->full_address &&
                $body['recipient']['country_code'] == 'MX' &&
                $body['items'][0]['variant_id'] == $variant->printful_variant_id &&
                $body['items'][0]['quantity'] == 1 &&
                $body['currency'] == 'MXN' &&
            $body['locale'] == 'es_ES';
        });
    }

    public function test_store_invalid(): void
    {
        $variant = Variant::factory()->create();
        Sanctum::actingAs(User::factory()->withPreferredLocation()->create());
        
        $this->get(route('products.show', $variant->product_id))->assertOk();

        $data = [];

        $this->post($this->url, $data)
            ->assertInvalid([
                'variant_id',
                'quantity',
            ])
            ->assertRedirect(route('products.show', $variant->product_id))
        ->assertSessionHasErrors();

        $data = [
            'variant_id',
            'quantity',
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'variant_id',
                'quantity',
            ])
            ->assertRedirect(route('products.show', $variant->product_id))
        ->assertSessionHasErrors();

        $data = [
            'variant_id' => null,
            'quantity' => null,
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'variant_id',
                'quantity',
            ])
            ->assertRedirect(route('products.show', $variant->product_id))
        ->assertSessionHasErrors();

        $data = [
            'variant_id' => '',
            'quantity' => '',
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'variant_id',
                'quantity',
            ])
            ->assertRedirect(route('products.show', $variant->product_id))
        ->assertSessionHasErrors();

        $data = [
            'variant_id' => ' ',
            'quantity' => ' ',
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'variant_id',
                'quantity',
            ])
            ->assertRedirect(route('products.show', $variant->product_id))
        ->assertSessionHasErrors();

        $data = [
            'variant_id' => 0,
            'quantity' => 0,
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'variant_id',
                'quantity',
            ])
            ->assertRedirect(route('products.show', $variant->product_id))
        ->assertSessionHasErrors();
    }

    public function test_update(): void
    {
        Sanctum::actingAs($user = User::factory()
            ->has(Cart::factory()->has(Order::factory()->state([
                'quantity' => 1,
            ])))
            ->withPreferredLocation()
        ->createQuietly());

        $order = Order::first();

        $this->assertEquals($order->quantity, 1);

        $this->get(route('carts.show', $user->cart->id))->assertOk();

        $data = [
            'quantity' => 2,
        ];

        $this->put(route('orders.update', $order->id), $data)
            ->assertValid()
            ->assertRedirect(route('carts.show', $user->cart->id))
        ->assertSessionMissing('message');

        $order->refresh();
        $this->assertEquals($order->quantity, 2);
    }

    public function test_update_invalid(): void
    {
        Sanctum::actingAs($user = User::factory()
            ->has(Cart::factory()->hasOrders())
            ->withPreferredLocation()
        ->createQuietly());

        $order = $user->cart->orders->get(0);
        
        $this->get(route('carts.show', $user->cart->id))->assertOk();

        $data = [];

        $this->put(route('orders.update', $order->id), $data)
            ->assertInvalid([
                'quantity',
            ])
            ->assertRedirect(route('carts.show', $user->cart->id))
        ->assertSessionHasErrors();

        $data = [
            'quantity',
        ];

        $this->put(route('orders.update', $order->id), $data)
            ->assertInvalid([
                'quantity',
            ])
            ->assertRedirect(route('carts.show', $user->cart->id))
        ->assertSessionHasErrors();

        $data = [
            'quantity' => null,
        ];

        $this->put(route('orders.update', $order->id), $data)
            ->assertInvalid([
                'quantity',
            ])
            ->assertRedirect(route('carts.show', $user->cart->id))
        ->assertSessionHasErrors();

        $data = [
            'quantity' => '',
        ];

        $this->put(route('orders.update', $order->id), $data)
            ->assertInvalid([
                'quantity',
            ])
            ->assertRedirect(route('carts.show', $user->cart->id))
        ->assertSessionHasErrors();

        $data = [
            'quantity' => ' ',
        ];

        $this->put(route('orders.update', $order->id), $data)
            ->assertInvalid([
                'quantity',
            ])
            ->assertRedirect(route('carts.show', $user->cart->id))
        ->assertSessionHasErrors();

        $data = [
            'quantity' => 0,
        ];

        $this->put(route('orders.update', $order->id), $data)
            ->assertInvalid([
                'quantity',
            ])
            ->assertRedirect(route('carts.show', $user->cart->id))
        ->assertSessionHasErrors();
    }

    public function test_destroy(): void
    {
        Sanctum::actingAs($user = User::factory()
            ->has(Cart::factory()->hasOrders())
        ->createQuietly());

        $cart = $user->cart;
        $cart->load('orders');

        $this->assertDatabaseCount('orders', 1);
        $this->assertNotEmpty($cart->orders);

        $order = Order::first();

        $this->get(route('carts.show', $user->cart->id))->assertOk();

        $this->delete(route('orders.destroy', $order->id))
            ->assertRedirect(route('carts.show', $user->cart->id))
        ->assertSessionHas('message', 'Tu carrito ha sido actualizado.');

        $cart->refresh();
        $this->assertEmpty($cart->orders);
        $this->assertDatabaseEmpty('orders');
    }
}
