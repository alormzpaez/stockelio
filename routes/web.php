<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureContactDetailsAreFilled;
use App\PermissionsEnum;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// System routes
Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class)->only(['index', 'show']);
    Route::resource('products', ProductController::class)->only(['edit', 'update'])->middleware([
        Authorize::using(PermissionsEnum::UpdateProduct->value)
    ]);
    Route::resource('carts', CartController::class)->only(['show']);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'destroy']);

    Route::middleware(EnsureContactDetailsAreFilled::class)->group(function () {
        Route::resource('orders', OrderController::class)->only(['store', 'update']);

        Route::get('checkout', [CheckoutController::class, 'checkout'])->name('checkout');
        Route::get('checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
        Route::get('checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    });
});
