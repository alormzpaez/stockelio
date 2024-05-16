<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
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
        Sanctum::actingAs($user = User::factory()
            ->has(Cart::factory()->hasOrders(2))
        ->createQuietly());

        $this->get(route('carts.show', $user->cart->id))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Carts/Show')
            ->has('cart', fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('user_id')
                    ->has('created_at')
                    ->has('updated_at')
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
            )
        );
    }
}
