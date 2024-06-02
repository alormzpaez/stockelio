<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public string $url = '/orders';

    public function test_guest(): void
    {
        $order = Order::factory()->create();

        $this->get($this->url)->assertMethodNotAllowed(); // index
        $this->get("{$this->url}/{$order->id}")->assertMethodNotAllowed(); // show
        $this->get("{$this->url}/create")->assertMethodNotAllowed(); // create
        $this->post($this->url)->assertRedirect(route('login')); // post
        $this->get("{$this->url}/edit")->assertMethodNotAllowed(); // edit
        $this->put("{$this->url}/{$order->id}")->assertMethodNotAllowed(); // update
        $this->delete("{$this->url}/{$order->id}")->assertRedirect(route('login')); // destroy
    }

    public function test_user(): void
    {
        $order = Order::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->get($this->url)->assertMethodNotAllowed(); // index
        $this->get("{$this->url}/{$order->id}")->assertMethodNotAllowed(); // show
        $this->get("{$this->url}/create")->assertMethodNotAllowed(); // create
        $this->post($this->url)->assertInvalid(); // post
        $this->get("{$this->url}/edit")->assertMethodNotAllowed(); // edit
        $this->put("{$this->url}/{$order->id}")->assertMethodNotAllowed(); // update
        $this->delete("{$this->url}/{$order->id}")->assertRedirect(); // destroy
    }

    public function test_store(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        $cart = $user->cart;
        $cart->load('orders');
        $variant = Variant::factory()->create();

        $this->get(route('products.show', $variant->product_id))->assertOk();

        $this->assertDatabaseEmpty('orders');
        $this->assertEmpty($cart->orders);

        $data = [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ];

        $this->post($this->url, $data)
            ->assertValid()
            ->assertRedirect(route('products.show', $variant->product_id))
        ->assertSessionHas('message', 'Producto agregado a tu carrito.');

        $cart->refresh();
        $this->assertDatabaseCount('orders', 1);
        $this->assertNotEmpty($cart->orders);
    }

    public function test_store_invalid(): void
    {
        $variant = Variant::factory()->create();
        Sanctum::actingAs(User::factory()->create());
        
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
        ->assertRedirect(route('carts.show', $user->cart->id));

        $cart->refresh();
        $this->assertEmpty($cart->orders);
        $this->assertDatabaseEmpty('orders');
    }
}
