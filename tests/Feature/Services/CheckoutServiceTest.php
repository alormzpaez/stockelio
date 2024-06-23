<?php

namespace Tests\Feature\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Variant;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    private CheckoutService $checkoutService;

    public function setUp(): void
    {
        parent::setUp();

        $this->checkoutService = new CheckoutService();

        config('printful.key', 'test');
    }

    public function test_execute_success(): void
    {
        $order1Body = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/CreateANewOrder1OkResponse.json')), true);
        $order2Body = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/CreateANewOrder2OkResponse.json')), true);

        Http::fake([
            'https://api.printful.com/orders' => function (Request $request) use ($order1Body, $order2Body) {
                if ($request->method() == 'POST') {
                    $body = json_decode($request->body(), true);

                    if (isset($body['items'][0]['sync_variant_id'])) {
                        if ($body['items'][0]['sync_variant_id'] === 1) {
                            return Http::response($order1Body, 200);
                        } else if ($body['items'][0]['sync_variant_id'] === 2) {
                            return Http::response($order2Body, 200);
                        }
                    }
                }
            }
        ]);

        User::factory()
            ->has(Cart::factory()->hasOrders())
        ->createQuietly();

        $user = User::factory()->withPreferredLocation()->create();
        $user->load('preferredLocation');

        $cart = $user->cart;

        $order1 = Order::factory()
            ->for($cart)
            ->for(Variant::factory()->state([
                'printful_variant_id' => 1,
            ]))
        ->create();
        $order2 = Order::factory()
            ->for($cart)
            ->for(Variant::factory()->state([
                'printful_variant_id' => 2,
            ]))
        ->create();

        $this->assertCount(3, Order::where('status', Order::INCART_STATUS)->get());
        $this->assertCount(2, $cart->incartOrders);
        $this->assertEquals($order1->status, Order::INCART_STATUS);
        $this->assertNull($order1->printful_order_id);
        $this->assertEquals($order2->status, Order::INCART_STATUS);
        $this->assertNull($order2->printful_order_id);

        $this->checkoutService->executeSuccess($user->id);

        $cart->refresh();
        $order1->refresh();
        $order2->refresh();

        $this->assertCount(1, Order::where('status', Order::INCART_STATUS)->get());
        $this->assertEmpty($cart->incartOrders);
        $this->assertEquals($order1->status, Order::PENDING_STATUS);
        $this->assertEquals($order1->printful_order_id, 13);
        $this->assertEquals($order2->status, Order::PENDING_STATUS);
        $this->assertEquals($order2->printful_order_id, 14);

        Http::assertSentInOrder([
            function (Request $request) use ($user) {
                $body = json_decode($request->body(), true);

                return $request->url() == 'https://api.printful.com/orders' &&
                    $request->method() == 'POST' &&
                    $body['items'][0]['sync_variant_id'] == 1 &&
                    $body['recipient']['name'] == $user->name  &&
                    $body['recipient']['address1'] == $user->preferredLocation->full_address &&
                    $body['recipient']['city'] == $user->preferredLocation->city &&
                    $body['recipient']['country_code'] == $user->preferredLocation->country_code &&
                    $body['recipient']['zip'] == $user->preferredLocation->zip;
            },
            function (Request $request) use ($user) {
                $body = json_decode($request->body(), true);

                return $request->url() == 'https://api.printful.com/orders' &&
                    $request->method() == 'POST' &&
                    $body['items'][0]['sync_variant_id'] == 2 &&
                    $body['recipient']['name'] == $user->name  &&
                    $body['recipient']['address1'] == $user->preferredLocation->full_address &&
                    $body['recipient']['city'] == $user->preferredLocation->city &&
                    $body['recipient']['country_code'] == $user->preferredLocation->country_code &&
                    $body['recipient']['zip'] == $user->preferredLocation->zip;
            }
        ]);
    }

    public function test_execute_success_with_error_when_post_printful_order(): void
    {
        $order1Body = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/CreateANewOrder1OkResponse.json')), true);
        $order2Body = json_decode(file_get_contents(base_path('tests/Fixtures/Printful/CreateANewOrder2OkResponse.json')), true);

        Http::fake([
            'https://api.printful.com/orders' => function (Request $request) use ($order1Body, $order2Body) {
                if ($request->method() == 'POST') {
                    $body = json_decode($request->body(), true);

                    if (isset($body['items'][0]['sync_variant_id'])) {
                        if ($body['items'][0]['sync_variant_id'] === 1) {
                            return Http::response($order1Body, 200);
                        } else if ($body['items'][0]['sync_variant_id'] === 2) {
                            return Http::response($order2Body, 400);
                        }
                    }
                }
            }
        ]);

        User::factory()
            ->has(Cart::factory()->hasOrders())
        ->createQuietly();

        $user = User::factory()->withPreferredLocation()->create();
        $user->load('preferredLocation');

        $cart = $user->cart;

        $order1 = Order::factory()
            ->for($cart)
            ->for(Variant::factory()->state([
                'printful_variant_id' => 1,
            ]))
        ->create();
        $order2 = Order::factory()
            ->for($cart)
            ->for(Variant::factory()->state([
                'printful_variant_id' => 2,
            ]))
        ->create();

        $this->assertCount(3, Order::where('status', Order::INCART_STATUS)->get());
        $this->assertCount(2, $cart->incartOrders);
        $this->assertEquals($order1->status, Order::INCART_STATUS);
        $this->assertNull($order1->printful_order_id);
        $this->assertEquals($order2->status, Order::INCART_STATUS);
        $this->assertNull($order2->printful_order_id);

        try {
            $this->checkoutService->executeSuccess($user->id);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'Error creating a new order in printful.');
        }

        $cart->refresh();
        $order1->refresh();
        $order2->refresh();

        $this->assertCount(3, Order::where('status', Order::INCART_STATUS)->get());
        $this->assertCount(2, $cart->incartOrders);
        $this->assertEquals($order1->status, Order::INCART_STATUS);
        $this->assertNull($order1->printful_order_id);
        $this->assertEquals($order2->status, Order::INCART_STATUS);
        $this->assertNull($order2->printful_order_id);

        Http::assertSentInOrder([
            function (Request $request) use ($user) {
                $body = json_decode($request->body(), true);

                return $request->url() == 'https://api.printful.com/orders' &&
                    $request->method() == 'POST' &&
                    $body['items'][0]['sync_variant_id'] == 1 &&
                    $body['recipient']['name'] == $user->name  &&
                    $body['recipient']['address1'] == $user->preferredLocation->full_address &&
                    $body['recipient']['city'] == $user->preferredLocation->city &&
                    $body['recipient']['country_code'] == $user->preferredLocation->country_code &&
                    $body['recipient']['zip'] == $user->preferredLocation->zip;
            },
            function (Request $request) use ($user) {
                $body = json_decode($request->body(), true);

                return $request->url() == 'https://api.printful.com/orders' &&
                    $request->method() == 'POST' &&
                    $body['items'][0]['sync_variant_id'] == 2 &&
                    $body['recipient']['name'] == $user->name  &&
                    $body['recipient']['address1'] == $user->preferredLocation->full_address &&
                    $body['recipient']['city'] == $user->preferredLocation->city &&
                    $body['recipient']['country_code'] == $user->preferredLocation->country_code &&
                    $body['recipient']['zip'] == $user->preferredLocation->zip;
            }
        ]);
    }
}
