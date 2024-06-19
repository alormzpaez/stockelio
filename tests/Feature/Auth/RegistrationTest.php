<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\RolesEnum;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $this->assertDatabaseEmpty(User::class);
        $this->assertEmpty(User::role(RolesEnum::Customer->value)->get());
        
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseCount(User::class, 1);
        $this->assertCount(1, User::role(RolesEnum::Customer->value)->get());

        $user = User::with('cart')->first();
        $this->assertNotNull($user->cart);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
