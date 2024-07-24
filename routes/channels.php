<?php

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('App.Models.Chat.{chat}', function (User $user, Chat $chat) {
    if (
        Chat::whereId($chat->id)
            ->whereRelation('users', 'users.id', $user->id)
        ->exists()
    ) {
        return $user->only(['id', 'name']);
    }
});