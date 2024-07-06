<?php

namespace Tests\Unit\Models;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_chat(): void
    {
        $message = Message::factory()->create();

        $this->assertInstanceOf(Chat::class, $message->chat);
    }

    public function test_belongs_to_user(): void
    {
        $message = Message::factory()->create();

        $this->assertInstanceOf(User::class, $message->user);
    }

    public function test_read_at(): void
    {
        $message = Message::factory()->create([
            'read_at' => '2024-07-06 09:33:51',
        ]);

        $this->assertInstanceOf(Carbon::class, $message->read_at);
    }
}
