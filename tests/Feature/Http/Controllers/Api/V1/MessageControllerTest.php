<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest(): void
    {
        $user = User::factory()->create();
        $chat = Chat::factory()
            ->hasAttached([$user])
        ->create();
        $message = Message::factory()
            ->for($chat)
        ->create();

        $this->getJson("api/v1/chats/{$chat->id}/messages")->assertUnauthorized(); // index
        $this->getJson("api/v1/chats/{$chat->id}/messages/{$message->id}")
        ->assertNotFound(); // show
        $this->postJson("api/v1/chats/{$chat->id}/messages")
        ->assertMethodNotAllowed(); // store
        $this->putJson("api/v1/chats/{$chat->id}/messages/{$message->id}")
        ->assertNotFound(); // update
        $this->deleteJson("api/v1/chats/{$chat->id}/messages/{$message->id}")
        ->assertNotFound(); // destroy
    }

    public function test_user(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        $chat = Chat::factory()
            ->hasAttached([$user])
        ->create();
        $message = Message::factory()
            ->for($chat)
        ->create();

        $this->getJson("api/v1/chats/{$chat->id}/messages")->assertOk(); // index
        $this->getJson("api/v1/chats/{$chat->id}/messages/{$message->id}")
        ->assertNotFound(); // show
        $this->postJson("api/v1/chats/{$chat->id}/messages")
        ->assertMethodNotAllowed(); // store
        $this->putJson("api/v1/chats/{$chat->id}/messages/{$message->id}")
        ->assertNotFound(); // update
        $this->deleteJson("api/v1/chats/{$chat->id}/messages/{$message->id}")
        ->assertNotFound(); // destroy
    }

    public function test_user_with_non_belonging_chat(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $chat = Chat::factory()
            ->hasUsers()
        ->create();
        $message = Message::factory()
            ->for($chat)
        ->create();

        $this->getJson("api/v1/chats/{$chat->id}/messages")->assertForbidden(); // index
        $this->getJson("api/v1/chats/{$chat->id}/messages/{$message->id}")
        ->assertNotFound(); // show
        $this->postJson("api/v1/chats/{$chat->id}/messages")
        ->assertMethodNotAllowed(); // store
        $this->putJson("api/v1/chats/{$chat->id}/messages/{$message->id}")
        ->assertNotFound(); // update
        $this->deleteJson("api/v1/chats/{$chat->id}/messages/{$message->id}")
        ->assertNotFound(); // destroy
    }

    public function test_index(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        $chat = Chat::factory()
            ->hasAttached([$user])
            ->hasMessages()
        ->create();

        $this->getJson("api/v1/chats/{$chat->id}/messages")
            ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 1, fn (AssertableJson $json) =>
                $json->has('body')
                    ->has('read_at')
                    ->has('created_at')
                ->has('user_id')
            )->etc()
        );
    }
}
