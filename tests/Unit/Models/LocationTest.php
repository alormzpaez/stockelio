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
}
