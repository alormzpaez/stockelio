<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ChatResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();

        $chats = Chat::select('id')
            ->whereRelation('users', 'users.id', $user->id)
            ->with([
                'users:name',
                'latestMessage:messages.chat_id,body,created_at',
            ])
            ->orderByDesc(Message::select('created_at')->whereColumn('messages.chat_id', 'chats.id'))
            ->cursorPaginate()
        ->map(fn (Chat $chat) => (object) [
            'id' => $chat->id,
            'latestMessage' => $chat->latestMessage,
            'receiver' => $chat->users
                ->firstWhere('id', '!=', $user->id)
        ]);

        return ChatResource::collection($chats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        //
    }
}
