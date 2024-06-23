<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_one_cart(): void
    {
        $user = User::factory()->hasCart()->create();

        $this->assertInstanceOf(Cart::class, $user->cart);
    }

    public function test_has_many_locations(): void
    {
        $user = User::factory()->hasLocations(2)->create();

        $this->assertInstanceOf(Collection::class, $user->locations);
        $this->assertCount(2, $user->locations);
        $this->assertInstanceOf(Location::class, $user->locations->get(0));
    }

    public function test_has_one_preferred_location(): void
    {
        $user = User::factory()->hasLocations(2)->create();
        $location = $user->locations->get(0);

        $this->assertNull($user->preferredLocation);

        $location->update([
            'is_preferred' => true,
        ]);

        $user->load('preferredLocation');

        $this->assertInstanceOf(Location::class, $user->preferredLocation);
        $this->assertEquals($user->preferredLocation->id, $location->id);
    }

    public function test_set_new_preferred_location(): void
    {
        $user = User::factory()->hasLocations(2)->create();
        $location1 = $user->locations->get(0);
        $location2 = $user->locations->get(1);

        $this->assertNull($user->preferredLocation);
        $this->assertCount(2, Location::where('is_preferred', false)->get());

        $user->setNewPreferredLocation($location1->id);

        $user->load('preferredLocation');

        $this->assertEquals($user->preferredLocation->id, $location1->id);
        $this->assertCount(1, Location::where('is_preferred', false)->get());

        $user->setNewPreferredLocation($location2->id);

        $user->load('preferredLocation');

        $this->assertEquals($user->preferredLocation->id, $location2->id);
        $this->assertCount(1, Location::where('is_preferred', false)->get());
    }
}
