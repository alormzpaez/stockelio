<?php

namespace Tests\Unit\Models;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user(): void
    {
        $location = Location::factory()->create();

        $this->assertInstanceOf(User::class, $location->user);
    }

    public function test_full_address(): void
    {
        $location = Location::factory()->create([
            'country_name' => 'Mexico',
            'country_code' => 'MX',
            'state_name' => 'Aguascalientes',
            'city' => 'Asientos',
            'locality' => 'Colonia del Norte',
            'address' => 'Andamio #220 3er Sector',
            'zip' => '10101',
            'phone' => '8181818181',
        ]);

        $this->assertEquals(
            $location->full_address, 
            'Andamio #220 3er Sector, Colonia del Norte. Asientos, Aguascalientes. C.P.: 10101'
        );
    }
}
