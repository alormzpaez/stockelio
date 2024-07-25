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

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $endpoint = 'api/v1/chats';

    public function test_guest(): void
    {
        $chat = Chat::factory()->create();

        $this->getJson($this->endpoint)->assertUnauthorized(); // index
        $this->getJson("{$this->endpoint}/{$chat->id}")->assertNotFound(); // show
        $this->postJson($this->endpoint)->assertMethodNotAllowed(); // store
        $this->putJson("{$this->endpoint}/{$chat->id}")->assertNotFound(); // update
        $this->deleteJson("{$this->endpoint}/{$chat->id}")->assertNotFound(); // destroy
    }

    public function test_user(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $chat = Chat::factory()->create();

        $this->getJson($this->endpoint)->assertOk(); // index
        $this->getJson("{$this->endpoint}/{$chat->id}")->assertNotFound(); // show
        $this->postJson($this->endpoint)->assertMethodNotAllowed(); // store
        $this->putJson("{$this->endpoint}/{$chat->id}")->assertNotFound(); // update
        $this->deleteJson("{$this->endpoint}/{$chat->id}")->assertNotFound(); // destroy
    }

    public function test_index(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $chat1 = Chat::factory()
            ->hasAttached([$user])
        ->create();
        $chat2 = Chat::factory()
            ->hasAttached([$user])
        ->create();
        $chat3 = Chat::factory()
            ->hasAttached([$user])
            ->hasMessages()
        ->create();
        
        $this->travelTo(now()->addMinute());

        Message::factory()
            ->for($chat2)
        ->create();
        
        $this->getJson($this->endpoint)
            ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json
                ->has('data', 3, fn (AssertableJson $json) =>
                    $json->has('id')
                    ->has('receiver', fn (AssertableJson $json) =>
                        $json->has('name')
                    )
                    ->has('latest_message', fn (AssertableJson $json) =>
                        $json->has('body')
                        ->has('created_at')
                    )
                )
                ->where('data.0.id', $chat2->id)
                ->where('data.1.id', $chat3->id)
                ->where('data.2.id', $chat1->id)
            ->etc()
        );
    }
}
