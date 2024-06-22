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

    public function test_personal_data(): void
    {
        $user = User::factory()->create();

        $this->assertIsArray($user->personalData);
        $this->assertEquals($user->personalData['name'], $user->name);
        $this->assertEquals($user->personalData['address1'], $user->address1);
        $this->assertEquals($user->personalData['city'], $user->city);
        $this->assertEquals($user->personalData['state_code'], $user->state_code);
        $this->assertEquals($user->personalData['country_code'], $user->country_code);
        $this->assertEquals($user->personalData['zip'], $user->zip);
    }

    public function test_has_many_locations(): void
    {
        $user = User::factory()->hasLocations(2)->create();

        $this->assertInstanceOf(Collection::class, $user->locations);
        $this->assertCount(2, $user->locations);
        $this->assertInstanceOf(Location::class, $user->locations->get(0));
    }
}
