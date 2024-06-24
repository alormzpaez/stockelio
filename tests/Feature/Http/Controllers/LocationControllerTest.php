<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase;

    public string $url = '/locations';

    public function test_guest(): void
    {
        $location = Location::factory()->create();

        $this->get($this->url)->assertMethodNotAllowed(); // index
        $this->get("{$this->url}/{$location->id}")->assertMethodNotAllowed(); // show
        $this->get("{$this->url}/create")->assertRedirect(route('login')); // create
        $this->post($this->url)->assertRedirect(route('login')); // post
        $this->get("{$this->url}/{$location->id}/edit")->assertRedirect(route('login')); // edit
        $this->put("{$this->url}/{$location->id}")->assertRedirect(route('login')); // update
        $this->delete("{$this->url}/{$location->id}")->assertRedirect(route('login')); // destroy
    }

    public function test_user(): void
    {
        $location = Location::factory()->create();
        Sanctum::actingAs($location->user);

        $this->get($this->url)->assertMethodNotAllowed(); // index
        $this->get("{$this->url}/{$location->id}")->assertMethodNotAllowed(); // show
        $this->get("{$this->url}/create")->assertOk(); // create
        $this->post($this->url)->assertInvalid(); // post
        $this->get("{$this->url}/{$location->id}/edit")->assertOk(); // edit
        $this->put("{$this->url}/{$location->id}")->assertInvalid(); // update
        $this->delete("{$this->url}/{$location->id}")->assertRedirect(); // destroy
    }

    public function test_user_with_not_own_location(): void
    {
        $location = Location::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->get($this->url)->assertMethodNotAllowed(); // index
        $this->get("{$this->url}/{$location->id}")->assertMethodNotAllowed(); // show
        $this->get("{$this->url}/create")->assertOk(); // create
        $this->post($this->url)->assertInvalid(); // post
        $this->get("{$this->url}/{$location->id}/edit")->assertForbidden(); // edit
        $this->put("{$this->url}/{$location->id}", [
            'state_name' => 'Some another state',
            'city' => 'Some another city',
            'locality' => 'Some another locality',
            'address' => 'Some another address',
            'zip' => '01010',
            'phone' => '0101010101',
        ])->assertForbidden(); // update
        $this->delete("{$this->url}/{$location->id}")->assertForbidden(); // destroy
    }

    public function test_create(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->get(route('locations.create'))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Locations/Create')
        );
    }

    public function test_store_when_locations_are_empty(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->get(route('locations.create'))->assertOk();

        $this->assertDatabaseEmpty('locations');
        $this->assertEmpty($user->locations);
        $this->assertNull($user->preferredLocation);

        $data = [
            'state_name' => 'Some state',
            'city' => 'Some city',
            'locality' => 'Some locality',
            'address' => 'Some address',
            'zip' => '01010',
            'phone' => '0181818181',
        ];

        $this->post(route('locations.store'), $data)
            ->assertValid()
        ->assertRedirect(route('profile.edit'));

        $user->load([
            'locations',
            'preferredLocation',
        ]);

        $this->assertDatabaseCount('locations', 1);
        $this->assertNotEmpty($user->locations);
        $this->assertNotNull($user->preferredLocation);
    }

    public function test_store_when_locations_are_not_empty(): void
    {
        Sanctum::actingAs($user = User::factory()->withPreferredLocation()->create());

        $initialPreferredLocation = $user->preferredLocation;

        $this->get(route('locations.create'))->assertOk();

        $this->assertDatabaseCount('locations', 1);
        $this->assertCount(1, $user->locations);
        $this->assertEquals($user->preferredLocation->id, $initialPreferredLocation->id);

        $data = [
            'state_name' => 'Some state',
            'city' => 'Some city',
            'locality' => 'Some locality',
            'address' => 'Some address',
            'zip' => '01010',
            'phone' => '0181818181',
        ];

        $this->post(route('locations.store'), $data)
            ->assertValid()
        ->assertRedirect(route('profile.edit'));

        $user->load([
            'locations',
            'preferredLocation',
        ]);

        $this->assertDatabaseCount('locations', 2);
        $this->assertCount(2, $user->locations);
        $this->assertEquals($user->preferredLocation->id, $initialPreferredLocation->id);
    }

    public function test_store_invalid(): void
    {
        Sanctum::actingAs(User::factory()->create());
        
        $this->get(route('locations.create'))->assertOk();

        $data = [];

        $this->post($this->url, $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.create'))
        ->assertSessionHasErrors();

        $data = [
            'state_name',
            'city',
            'locality',
            'address',
            'zip',
            'phone',
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.create'))
        ->assertSessionHasErrors();

        $data = [
            'state_name' => null,
            'city' => null,
            'locality' => null,
            'address' => null,
            'zip' => null,
            'phone' => null,
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.create'))
        ->assertSessionHasErrors();

        $data = [
            'state_name' => '',
            'city' => '',
            'locality' => '',
            'address' => '',
            'zip' => '',
            'phone' => '',
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.create'))
        ->assertSessionHasErrors();

        $data = [
            'state_name' => ' ',
            'city' => ' ',
            'locality' => ' ',
            'address' => ' ',
            'zip' => ' ',
            'phone' => ' ',
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.create'))
        ->assertSessionHasErrors();

        $data = [
            'state_name' => 'Some state',
            'city' => 'Some city',
            'locality' => 'Some locality',
            'address' => 'Some address',
            'zip' => 'Incorrect zip',
            'phone' => 'Incorrect phone',
        ];

        $this->post($this->url, $data)
            ->assertInvalid([
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.create'))
        ->assertSessionHasErrors();
    }

    public function test_edit(): void
    {
        $location = Location::factory()->create();
        Sanctum::actingAs($location->user);

        $this->get(route('locations.edit', $location->id))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Locations/Edit')
            ->has('location', fn (AssertableInertia $page) =>
                $page->has('id')
                    ->has('user_id')
                    ->has('state_name')
                    ->has('city')
                    ->has('locality')
                    ->has('address')
                    ->has('zip')
                    ->has('phone')
                    ->has('is_preferred')
                ->has('full_address')
            )
        );
    }

    public function test_update_except_column_is_preferred(): void
    {
        $location = Location::factory()->create();
        Sanctum::actingAs($location->user);

        $this->get(route('locations.edit', $location->id))->assertOk();

        $data = [
            'state_name' => 'Some another state',
            'city' => 'Some another city',
            'locality' => 'Some another locality',
            'address' => 'Some another address',
            'zip' => '01202',
            'phone' => '0282828282',
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertValid()
        ->assertRedirect(route('profile.edit'));

        $location->refresh();

        $this->assertEquals($location->state_name, $data['state_name']);
        $this->assertEquals($location->city, $data['city']);
        $this->assertEquals($location->locality, $data['locality']);
        $this->assertEquals($location->address, $data['address']);
        $this->assertEquals($location->zip, $data['zip']);
        $this->assertEquals($location->phone, $data['phone']);
    }

    public function test_update_only_column_is_preferred(): void
    {
        Sanctum::actingAs($user = User::factory()
            ->withPreferredLocation()
            ->hasLocations()
        ->create()); // created 2 locations

        $initialPreferredLocation = $user->preferredLocation;
        $notInitialPreferredLocation = $user->locations()
            ->whereNot('id', $initialPreferredLocation->id)
        ->first();

        $this->get(route('profile.edit'))->assertOk();

        $data = [
            'is_preferred' => true,
        ];

        $this->put(route('locations.update', $notInitialPreferredLocation->id), $data)
            ->assertValid()
        ->assertRedirect(route('profile.edit'));

        $user->load('preferredLocation');

        $this->assertEquals($user->preferredLocation->id, $notInitialPreferredLocation->id);
        $this->assertCount(1, $user->locations()->where('is_preferred', true)->get());
    }

    public function test_update_except_column_is_preferred_invalid(): void
    {
        $location = Location::factory()->create();
        Sanctum::actingAs($location->user);

        $this->get(route('locations.edit', $location->id))->assertOk();

        $data = [];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();

        $data = [
            'state_name',
            'city',
            'locality',
            'address',
            'zip',
            'phone',
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();

        $data = [
            'state_name' => null,
            'city' => null,
            'locality' => null,
            'address' => null,
            'zip' => null,
            'phone' => null,
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();

        $data = [
            'state_name' => '',
            'city' => '',
            'locality' => '',
            'address' => '',
            'zip' => '',
            'phone' => '',
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();

        $data = [
            'state_name' => ' ',
            'city' => ' ',
            'locality' => ' ',
            'address' => ' ',
            'zip' => ' ',
            'phone' => ' ',
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();

        $data = [
            'state_name' => 'Some state',
            'city' => 'Some city',
            'locality' => 'Some locality',
            'address' => 'Some address',
            'zip' => 'Incorrect zip',
            'phone' => 'Incorrect phone',
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();
    }

    public function test_update_only_column_is_preferred_invalid(): void
    {
        $location = Location::factory()->create();
        Sanctum::actingAs($location->user);

        $this->get(route('locations.edit', $location->id))->assertOk();

        $data = [];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();

        $data = [
            'is_preferred',
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();

        $data = [
            'is_preferred' => null,
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'is_preferred',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();

        $data = [
            'is_preferred' => '',
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'is_preferred',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();

        $data = [
            'is_preferred' => ' ',
        ];

        $this->put(route('locations.update', $location->id), $data)
            ->assertInvalid([
                'is_preferred',
            ])
            ->assertRedirect(route('locations.edit', $location->id))
        ->assertSessionHasErrors();
    }

    public function test_destroy_from_edit_form(): void
    {
        Sanctum::actingAs($user = User::factory()->withPreferredLocation()->create());

        $this->get(route('locations.edit', $user->preferredLocation->id))->assertOk();

        $this->delete(route('locations.destroy', $user->preferredLocation->id))
        ->assertRedirect(route('profile.edit'));

        $user->load([
            'locations',
            'preferredLocation',
        ]);

        $this->assertDatabaseEmpty('locations');
        $this->assertEmpty($user->locations);
        $this->assertNull($user->preferredLocation);
    }

    public function test_destroy_from_profile_edit_form(): void
    {
        Sanctum::actingAs($user = User::factory()->withPreferredLocation()->create());

        $this->get(route('profile.edit'))->assertOk();

        $this->delete(route('locations.destroy', $user->preferredLocation->id))
        ->assertRedirect(route('profile.edit'));

        $user->load([
            'locations',
            'preferredLocation',
        ]);

        $this->assertDatabaseEmpty('locations');
        $this->assertEmpty($user->locations);
        $this->assertNull($user->preferredLocation);
    }

    public function test_destroy_letting_be_preferred_location_to_next_existing_location_automatically(): void
    {
        Sanctum::actingAs($user = User::factory()
            ->withPreferredLocation()
            ->hasLocations()
        ->create());

        $notInitialPreferredLocation = $user->locations()->where('is_preferred', false)->first();

        $this->delete(route('locations.destroy', $user->preferredLocation->id))
        ->assertRedirect(route('profile.edit'));

        $user->load([
            'locations',
            'preferredLocation',
        ]);

        $this->assertDatabaseCount('locations', 1);
        $this->assertCount(1, $user->locations);
        $this->assertNotNull($user->preferredLocation);
        $this->assertEquals($user->preferredLocation->id, $notInitialPreferredLocation->id);
    }
}
