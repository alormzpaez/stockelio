<?php

use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PrintfulWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('printful/webhook', PrintfulWebhookController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('notifications', [NotificationController::class, 'updateAll']);
    Route::resource('notifications', NotificationController::class)->only([
        'index', 'update'
    ]);
    Route::resource('chats', ChatController::class)->only('index');
    Route::resource('chats.messages', MessageController::class)->only([
        'index',
        'store',
    ]);
});