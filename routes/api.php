<?php

use App\Http\Controllers\Api\PrintfulWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('printful/webhook', PrintfulWebhookController::class);