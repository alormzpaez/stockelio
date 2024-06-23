<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public string $url = '/checkout';

    public function setUp(): void
    {
        parent::setUp();

        config([
            'cashier.secret' => 'test',
            'printful.key' => 'test',
        ]);
    }

    public function test_guest(): void
    {
        $this->get($this->url)->assertRedirect(route('login'));
        $this->get("{$this->url}/success")->assertRedirect(route('login'));
        $this->get("{$this->url}/cancel")->assertRedirect(route('login'));
    }

    public function test_user_without_contact_details_filled(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->get($this->url)
            ->assertRedirect(route('carts.show', $user->cart->id))
            ->assertSessionHas('type', 'error')
        ->assertSessionHas('message', 'Es necesario llenar los datos de dirección primero.');
        $this->get("{$this->url}/success")
            ->assertRedirect(route('carts.show', $user->cart->id))
            ->assertSessionHas('type', 'error')
        ->assertSessionHas('message', 'Es necesario llenar los datos de dirección primero.');
        $this->get("{$this->url}/cancel")
            ->assertRedirect(route('carts.show', $user->cart->id))
            ->assertSessionHas('type', 'error')
        ->assertSessionHas('message', 'Es necesario llenar los datos de dirección primero.');
    }

    public function test_checkout_cancel(): void
    {
        Sanctum::actingAs($user = User::factory()->withPreferredLocation()->create());

        $this->get(route('checkout.cancel'))
            ->assertRedirect(route('carts.show', $user->cart->id))
            ->assertSessionHas('message', 'Error intentando completar el pago.')
        ->assertSessionHas('type', 'error');
    }

    public function test_checkout_error_without_incart_orders(): void
    {
        $this->withoutExceptionHandling();

        Sanctum::actingAs($user = User::factory()->withPreferredLocation()->create());

        $this->get(route('checkout'))
            ->assertRedirect(route('carts.show', $user->cart->id))
            ->assertSessionHas('message', 'No hay ninguna orden en tu carrito.')
        ->assertSessionHas('type', 'error');
    }

    public function test_checkout_success_error_trying_to_get_in_directly(): void
    {
        Sanctum::actingAs(User::factory()->withPreferredLocation()->create());

        $this->get(route('checkout.success'))->assertRedirect(route('checkout.cancel'));
    }
}
