<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => redirect()->route('products.index'));

// Products - publicly accessible
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/dashboard', fn () => redirect()->route('products.index'))->middleware(['auth', 'verified'])->name('dashboard');

// Cart & Orders - require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.addItem');
    Route::patch('/cart/items/{cartItem}', [CartController::class, 'updateItem'])->name('cart.updateItem');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem'])->name('cart.removeItem');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/pay', [OrderController::class, 'retryPayment'])->name('orders.pay');
    Route::get('/checkout/{order}/success', [OrderController::class, 'checkoutSuccess'])->name('checkout.success');
    Route::get('/checkout/{order}/cancel', [OrderController::class, 'checkoutCancel'])->name('checkout.cancel');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Stripe webhook (excluded from CSRF via bootstrap/app.php)
Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

require __DIR__.'/auth.php';
