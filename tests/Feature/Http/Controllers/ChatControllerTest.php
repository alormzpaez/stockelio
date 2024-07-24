<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\RolesEnum;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    public string $url = '/chats';

    public function test_guest(): void
    {
        $chat = Chat::factory()->create();

        $this->get($this->url)->assertMethodNotAllowed(); // index
        $this->get("{$this->url}/{$chat->id}")->assertRedirect(route('login')); // show
        $this->get("{$this->url}/create")->assertRedirect(route('login')); // create
        $this->post($this->url)->assertRedirect(route('login')); // post
        $this->get("{$this->url}/{$chat->id}/edit")->assertNotFound(); // edit
        $this->put("{$this->url}/{$chat->id}")->assertMethodNotAllowed(); // update
        $this->delete("{$this->url}/{$chat->id}")->assertMethodNotAllowed(); // destroy
    }

    public function test_customer(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Sanctum::actingAs($user1 = User::factory()->create()->assignRole(RolesEnum::Customer));
        $user2 = User::factory()->create()->assignRole(RolesEnum::TechnicalSupportSpecialist);
        $chat = Chat::factory()
            ->hasAttached([$user1, $user2])
        ->create();

        $this->get($this->url)->assertMethodNotAllowed(); // index
        $this->get("{$this->url}/{$chat->id}")->assertOk(); // show
        $this->get("{$this->url}/create")->assertNotFound(); // create
        $this->post($this->url)->assertValid(); // post
        $this->get("{$this->url}/{$chat->id}/edit")->assertNotFound(); // edit
        $this->put("{$this->url}/{$chat->id}")->assertMethodNotAllowed(); // update
        $this->delete("{$this->url}/{$chat->id}")->assertMethodNotAllowed(); // destroy
    }

    public function test_show(): void
    {
        Sanctum::actingAs($user1 = User::factory()->create());

        $user2 = User::factory()->create();

        $chat = Chat::factory()
            ->hasAttached([$user1, $user2])
        ->create();

        $this->get(route('chats.show', $chat->id))
            ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Chats/Show')
            ->has('chat', fn (AssertableInertia $page) =>
                $page->has('id')
                ->has('receiver', fn (AssertableInertia $page) =>
                    $page->has('id')
                    ->has('name')
                )
            )
        );
    }

    public function test_show_non_belonging_chat(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $chat = Chat::factory()
            ->hasAttached([$user1, $user2])
        ->create();

        $this->get(route('chats.show', $chat->id))->assertForbidden();
        
        Sanctum::actingAs($user1);
        
        $this->get(route('chats.show', $chat->id))->assertOk();
    }

    public function test_store(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Sanctum::actingAs($customer = User::factory()->create()->assignRole(RolesEnum::Customer));
        $worker = User::factory()->create()->assignRole(RolesEnum::TechnicalSupportSpecialist);
        
        $this->get(route('dashboard'))->assertOk();
        
        $this->assertDatabaseEmpty('chats');

        $data = [];

        $request = $this->post(route('chats.store'), $data)->assertValid();

        $chat = Chat::with('users')->first();

        $request->assertRedirect(route('chats.show', $chat->id));

        $this->assertDatabaseCount('chats', 1);
        $this->assertCount(2, $chat->users);
        $this->assertContains($worker->id, $chat->users->flatMap(fn (User $user) => [
            $user->id
        ]));
        $this->assertContains($customer->id, $chat->users->flatMap(fn (User $user) => [
            $user->id
        ]));
    }

    public function test_store_error_due_to_repeated_receiver_for_new_chat(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Sanctum::actingAs($customer = User::factory()->create()->assignRole(RolesEnum::Customer));
        $worker = User::factory()->create()->assignRole(RolesEnum::TechnicalSupportSpecialist);

        Chat::factory()
            ->hasAttached([$customer, $worker])
        ->create();
        
        $this->get(route('dashboard'))->assertOk();
        
        $this->assertDatabaseCount('chats', 1);

        $data = [];

        $this->post(route('chats.store'), $data)
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('type', 'error')
        ->assertSessionHas('message', 'No hay más personal de soporte al cliente para tener una conversación nueva.');

        $this->assertDatabaseCount('chats', 1);
    }
}
