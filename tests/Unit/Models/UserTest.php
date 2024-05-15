<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use App\Models\User;
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
}
