<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UpdateAllNotificationsRequest;
use App\Http\Requests\V1\UpdateNotificationRequest;
use App\Http\Resources\V1\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = request()->user()->notifications()->latest('created_at')->cursorPaginate();

        return NotificationResource::collection($notifications);
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
    public function show()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, string $id)
    {
        $notification = $request->user()
            ->unreadNotifications()
            ->whereId($id)
        ->first();

        if ($request->validated('is_read')) {
            $notification->markAsRead();
        }

        return response()->noContent(200);
    }

    /**
     * Update all resources in storage.
     */
    public function updateAll(UpdateAllNotificationsRequest $request)
    {
        if ($request->validated('are_read')) {
            $request->user()
                ->unreadNotifications()
            ->update([
                'read_at' => now()
            ]);
        }

        return response()->noContent(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
