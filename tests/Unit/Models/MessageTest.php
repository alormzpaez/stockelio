<?php

namespace Tests\Unit\Models;

use App\Models\Chat;
use App\Models\Message;
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
}
