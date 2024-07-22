<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreMessageRequest;
use App\Http\Resources\V1\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Chat $chat)
    {
        Gate::authorize('view', $chat);

        $messages = Message::whereRelation('chat', 'id', $chat->id)
            ->latest('id')
        ->cursorPaginate();

        return MessageResource::collection($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request, Chat $chat)
    {
        Gate::authorize('view', $chat);

        $message = $chat->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        return new MessageResource($message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat, Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat, Message $message)
    {
        //
    }
}
