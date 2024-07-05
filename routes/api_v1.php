<?php

use App\Http\Controllers\Api\V1\PrintfulWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('printful/webhook', PrintfulWebhookController::class);
