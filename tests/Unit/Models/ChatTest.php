<?php

namespace Tests\Unit\Models;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_many_messages(): void
    {
        $chat = Chat::factory()->hasMessages()->create();

        $this->assertInstanceOf(Collection::class, $chat->messages);
        $this->assertInstanceOf(Message::class, $chat->messages->get(0));
    }

    public function test_has_one_latest_message(): void
    {
        $chat = Chat::factory()->create();

        Message::factory()
            ->for($chat)
        ->create();

        $this->travelTo(now()->addMinute());

        $message = Message::factory()
            ->for($chat)
        ->create();

        $this->assertInstanceOf(Message::class, $chat->latestMessage);
        $this->assertEquals($message->id, $chat->latestMessage->id);
    }

    public function test_belongs_to_many_users(): void
    {
        $chat = Chat::factory()->create();

        $this->assertInstanceOf(Collection::class, $chat->users);
        $this->assertEmpty($chat->users);

        $chat = Chat::factory()->hasUsers()->create();

        $this->assertInstanceOf(Collection::class, $chat->users);
        $this->assertInstanceOf(User::class, $chat->users->get(0));
    }
}
