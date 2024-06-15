<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\File;
use App\Models\Product;
use App\Models\User;
use App\PermissionsEnum;
use App\RolesEnum;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Str;
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
        $this->get("{$this->url}/{$product->id}/edit")->assertRedirect(route('login')); // edit
        $this->put("{$this->url}/{$product->id}")->assertRedirect(route('login')); // update
        $this->delete("{$this->url}/{$product->id}")->assertMethodNotAllowed(); // destroy
    }

    public function test_customer(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $product = Product::factory()->create();

        Sanctum::actingAs(User::factory()->create()->assignRole(RolesEnum::Customer));

        $this->get($this->url)->assertOk(); // index
        $this->get("{$this->url}/{$product->id}")->assertOk(); // show
        $this->get("{$this->url}/create")->assertNotFound(); // create
        $this->post($this->url)->assertMethodNotAllowed(); // post
        $this->get("{$this->url}/{$product->id}/edit")->assertForbidden(); // edit
        $this->put("{$this->url}/{$product->id}")->assertForbidden(); // update
        $this->delete("{$this->url}/{$product->id}")->assertMethodNotAllowed(); // destroy
    }

    public function test_admin(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $product = Product::factory()->create();
        Sanctum::actingAs(User::factory()->create()->assignRole(RolesEnum::Admin));

        $this->get($this->url)->assertOk(); // index
        $this->get("{$this->url}/{$product->id}")->assertOk(); // show
        $this->get("{$this->url}/create")->assertNotFound(); // create
        $this->post($this->url)->assertMethodNotAllowed(); // post
        $this->get("{$this->url}/{$product->id}/edit")->assertOk(); // edit
        $this->put("{$this->url}/{$product->id}")->assertInvalid(); // update
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

    public function test_show_as_customer(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Sanctum::actingAs(User::factory()->create()->assignRole(RolesEnum::Customer));
        $product = Product::factory()
            ->hasVariants(2)
            ->has(File::factory(2)->state([
                'filename' => 'image.png',
            ]))
        ->create();

        $this->get(route('products.show', $product->id))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Products/Show')
            ->has('product', fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('name')
                    ->has('thumbnail_url')
                    ->has('description')
                    ->has('stripe_product_id')
                    ->has('printful_product_id')
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
                ->has('files', 2, fn (AssertableInertia $page) =>
                    $page->has('id')
                        ->has('product_id')
                        ->has('url')
                        ->has('filename')
                    ->where('url', fn (string $url) => Str::endsWith($url, 'image.png'))
                )
            )
            ->has('can', fn (AssertableInertia $page) =>
                $page->has(PermissionsEnum::UpdateProduct->value)
                ->where(PermissionsEnum::UpdateProduct->value, false)
            )
        );
    }

    public function test_show_as_admin(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Sanctum::actingAs(User::factory()->create()->assignRole(RolesEnum::Admin));
        $product = Product::factory()
            ->hasVariants(2)
            ->has(File::factory(2)->state([
                'filename' => 'image.png',
            ]))
        ->create();

        $this->get(route('products.show', $product->id))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Products/Show')
            ->has('product', fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('name')
                    ->has('thumbnail_url')
                    ->has('description')
                    ->has('stripe_product_id')
                    ->has('printful_product_id')
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
                ->has('files', 2, fn (AssertableInertia $page) =>
                    $page->has('id')
                        ->has('product_id')
                        ->has('url')
                        ->has('filename')
                    ->where('url', fn (string $url) => Str::endsWith($url, 'image.png'))
                )
            )
            ->has('can', fn (AssertableInertia $page) =>
                $page->has(PermissionsEnum::UpdateProduct->value)
                ->where(PermissionsEnum::UpdateProduct->value, true)
            )
        );
    }

    public function test_edit(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Sanctum::actingAs(User::factory()->create()->assignRole(RolesEnum::Admin));
        $product = Product::factory()
            ->hasVariants(2)
            ->hasFiles(2)
        ->create();

        $this->get(route('products.edit', $product->id))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Products/Edit')
            ->has('product', fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('name')
                    ->has('thumbnail_url')
                    ->has('description')
                    ->has('created_at')
                    ->has('updated_at')
                    ->has('variants_count')
                    ->has('files', 2, fn (AssertableInertia $page) =>
                        $page->has('id')
                            ->has('url')
                        ->has('product_id')
                    )
                ->where('variants_count', 2)
            )
        );
    }

    public function test_update(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Carbon::setTestNow();
        Storage::fake('products');
        
        Sanctum::actingAs(User::factory()->create()->assignRole(RolesEnum::Admin));
        $product = Product::factory()->create();
        
        $this->travelTo($now = now()->addMinute());

        $data = [
            'description' => 'Some text here...',
            'files' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ]
        ];

        $this->put(route('products.update', $product->id), $data)
            ->assertValid()
            ->assertRedirect(route('products.edit', $product->id))
        ->assertSessionHas('message', 'El producto ha sido actualizado.');

        $product->load('files');
        $product->refresh();

        $this->assertEquals($product->description, $data['description']);
        $this->assertEquals($product->updated_at, $now->toDateTimeString());
        $this->assertCount(2, $product->files);
        $this->assertTrue(Storage::disk('products')->exists($product->files->get(0)->filename));
        $this->assertTrue(Storage::disk('products')->exists($product->files->get(1)->filename));
    }

    public function test_update_invalid(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Sanctum::actingAs(User::factory()->create()->assignRole(RolesEnum::Admin));
        $product = Product::factory()->create();

        $this->get(route('products.edit', $product->id))->assertOk();

        $data = [];

        $this->put(route('products.update', $product->id), $data)
            ->assertInvalid([
                'description',
                'files',
            ])
            ->assertRedirect(route('products.edit', $product->id))
        ->assertSessionHasErrors();

        $data = [
            'description',
            'files',
        ];

        $this->put(route('products.update', $product->id), $data)
            ->assertInvalid([
                'description',
                'files',
            ])
            ->assertRedirect(route('products.edit', $product->id))
        ->assertSessionHasErrors();

        $data = [
            'description' => null,
            'files' => null,
        ];

        $this->put(route('products.update', $product->id), $data)
            ->assertInvalid([
                'description',
                'files',
            ])
            ->assertRedirect(route('products.edit', $product->id))
        ->assertSessionHasErrors();

        $data = [
            'description' => '',
            'files' => [
                UploadedFile::fake()->create('file.pdf', mimeType: 'application/pdf'),
            ]
        ];

        $this->put(route('products.update', $product->id), $data)
            ->assertInvalid([
                'description',
                'files.0',
            ])
            ->assertRedirect(route('products.edit', $product->id))
        ->assertSessionHasErrors();
    }

    public function test_update_with_empty_files(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Carbon::setTestNow();
        Storage::fake('products');

        Sanctum::actingAs(User::factory()->create()->assignRole(RolesEnum::Admin));
        $product = Product::factory()->create();

        $this->travelTo($now = now()->addMinute());

        $data = [
            'description' => 'Some text here...',
            'files' => []
        ];

        $this->put(route('products.update', $product->id), $data)
            ->assertValid()
            ->assertRedirect(route('products.edit', $product->id))
        ->assertSessionHas('message', 'El producto ha sido actualizado.');

        $product->refresh();

        $this->assertEquals($product->description, $data['description']);
        $this->assertEquals($product->updated_at, $now->toDateTimeString());
        $this->assertDatabaseEmpty('files');
    }

    public function test_update_with_same_description_and_new_files(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Carbon::setTestNow();
        Storage::fake('products');
        
        Sanctum::actingAs(User::factory()->create()->assignRole(RolesEnum::Admin));
        $product = Product::factory()->create([
            'description' => $description = 'Some text here...',
        ]);
        
        $this->travelTo($now = now()->addMinute());

        $data = [
            'description' => $description,
            'files' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ]
        ];

        $this->put(route('products.update', $product->id), $data)
            ->assertValid()
            ->assertRedirect(route('products.edit', $product->id))
        ->assertSessionHas('message', 'El producto ha sido actualizado.');

        $product->load('files');
        $product->refresh();

        $this->assertEquals($product->description, $data['description']);
        $this->assertEquals($product->updated_at, $now->toDateTimeString());
        $this->assertCount(2, $product->files);
        $this->assertTrue(Storage::disk('products')->exists($product->files->get(0)->filename));
        $this->assertTrue(Storage::disk('products')->exists($product->files->get(1)->filename));
    }
}
