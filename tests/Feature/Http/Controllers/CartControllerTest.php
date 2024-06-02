<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public string $url = '/carts';

    public function test_guest(): void
    {
        $user = User::factory()->create();

        $this->get($this->url)->assertNotFound(); // index
        $this->get("{$this->url}/{$user->cart->id}")->assertRedirect(route('login')); // show
        $this->get("{$this->url}/create")->assertRedirect(route('login')); // create
        $this->post($this->url)->assertNotFound(); // post
        $this->get("{$this->url}/edit")->assertRedirect(route('login')); // edit
        $this->put("{$this->url}/{$user->cart->id}")->assertMethodNotAllowed(); // update
        $this->delete("{$this->url}/{$user->cart->id}")->assertMethodNotAllowed(); // destroy
    }

    public function test_user(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->get($this->url)->assertNotFound(); // index
        $this->get("{$this->url}/{$user->cart->id}")->assertOk(); // show
        $this->get("{$this->url}/create")->assertNotFound(); // create
        $this->post($this->url)->assertNotFound(); // post
        $this->get("{$this->url}/edit")->assertNotFound(); // edit
        $this->put("{$this->url}/{$user->cart->id}")->assertMethodNotAllowed(); // update
        $this->delete("{$this->url}/{$user->cart->id}")->assertMethodNotAllowed(); // destroy
    }

    public function test_show(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $order1 = Order::factory()
            ->for($user->cart)
            ->for(Variant::factory()->state([
                'retail_price' => 100
            ]))
        ->create([
            'quantity' => 2
        ]);
        $order2 = Order::factory()
            ->for($user->cart)
            ->for(Variant::factory()->state([
                'retail_price' => 50
            ]))
        ->create([
            'quantity' => 3
        ]);

        $this->get(route('carts.show', $user->cart->id))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Carts/Show')
            ->has('cart', fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('user_id')
                    ->has('created_at')
                    ->has('updated_at')
                    ->has('total')
                ->has('orders', 2, fn (AssertableInertia $page) =>
                    $page->has('id')
                        ->has('status')
                        ->has('cart_id')
                        ->has('variant_id')
                        ->has('quantity')
                    ->has('variant', fn (AssertableInertia $page) =>
                        $page->has('id')
                            ->has('retail_price')
                            ->has('product_id')
                        ->has('product', fn (AssertableInertia $page) =>
                            $page->has('id')
                                ->has('name')
                            ->has('thumbnail_url')
                        )
                    )
                )
                ->where('total', 350)
                ->where('orders.0.id', $order2->id)
                ->where('orders.1.id', $order1->id)
            )
        );
    }

    public function test_show_not_own_cart(): void
    {
        $user1 = User::factory()
            ->has(Cart::factory())
        ->createQuietly();

        Sanctum::actingAs($user2 = User::factory()
            ->has(Cart::factory())
        ->createQuietly());

        $this->get(route('carts.show', $user1->cart->id))->assertForbidden();
        $this->get(route('carts.show', $user2->cart->id))->assertOk();
    }
}
