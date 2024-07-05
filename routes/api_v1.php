<?php

use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PrintfulWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('printful/webhook', PrintfulWebhookController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('users.notifications', NotificationController::class)->only('index');
});