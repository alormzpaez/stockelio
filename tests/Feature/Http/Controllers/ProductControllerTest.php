<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public string $url = '/products';

    public function test_guest(): void
    {
        $product = Product::factory()->create();

        $this->get($this->url)->assertRedirect(route('login')); // index
        $this->get("{$this->url}/{$product->id}")->assertRedirect(route('login')); // show
        $this->get("{$this->url}/create")->assertRedirect(route('login')); // create
        $this->post($this->url)->assertMethodNotAllowed(); // post
        $this->get("{$this->url}/edit")->assertRedirect(route('login')); // edit
        $this->put("{$this->url}/{$product->id}")->assertMethodNotAllowed(); // update
        $this->delete("{$this->url}/{$product->id}")->assertMethodNotAllowed(); // destroy
    }

    public function test_user(): void
    {
        $product = Product::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->get($this->url)->assertOk(); // index
        $this->get("{$this->url}/{$product->id}")->assertOk(); // show
        $this->get("{$this->url}/create")->assertNotFound(); // create
        $this->post($this->url)->assertMethodNotAllowed(); // post
        $this->get("{$this->url}/edit")->assertNotFound(); // edit
        $this->put("{$this->url}/{$product->id}")->assertMethodNotAllowed(); // update
        $this->delete("{$this->url}/{$product->id}")->assertMethodNotAllowed(); // destroy
    }

    public function test_index(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Product::factory(2)->hasVariants(2)->create();

        $this->get(route('products.index'))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Products/Index')
            ->has('products.data', 2, fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('name')
                    ->has('thumbnail_url')
                    ->has('variants_count')
                    ->has('cheapest_variant', fn (AssertableInertia $page) =>
                        $page->has('id') 
                            ->has('product_id') 
                        ->has('retail_price') 
                    )
                ->where('variants_count', 2)
            )
        );
    }

    public function test_show(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->hasVariants(2)->create();

        $this->get(route('products.show', $product->id))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Products/Show')
            ->has('product', fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('name')
                    ->has('thumbnail_url')
                    ->has('description')
                    ->has('created_at')
                    ->has('updated_at')
                    ->has('cheapest_variant', fn (AssertableInertia $page) =>
                        $page->has('id') 
                            ->has('product_id') 
                        ->has('retail_price') 
                    )
                ->has('variants', 2, fn (AssertableInertia $page) =>
                    $page->has('id')
                        ->has('product_id')
                    ->has('retail_price')
                )
            )
        );
    }
}
