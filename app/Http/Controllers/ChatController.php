<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\RolesEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user1 = request()->user();
        $user2 = User::role(RolesEnum::TechnicalSupportSpecialist->value)
            ->whereDoesntHave('chats', fn (Builder $q) =>
                $q->whereRelation('users', 'users.id', $user1->id)
            )
            ->inRandomOrder()
        ->first();

        if (!$user2) {
            Session::flash('type', 'error');
            Session::flash('message', 'No hay más personal de soporte al cliente para tener una conversación nueva.');

            return to_route('dashboard');
        }

        $chat = Chat::create();
        
        $chat->users()->attach([
            $user1->id,
            $user2->id,
        ]);

        return to_route('chats.show', $chat->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat): Response
    {
        $chat->load('users');

        return Inertia::render('Chats/Show', [
            'chat' => [
                'id' => $chat->id,
                'receiver' => $chat->users->firstWhere('id', '!=', request()->user()->id)->only('name')
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chat $chat)
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
