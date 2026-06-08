<?php

use App\Http\Controllers\Site\ProfileController;
use App\Http\Controllers\Site\CartController;
use App\Http\Controllers\Site\CheckoutController;
use App\Http\Controllers\Site\ProductController as SiteProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-address', [ProfileController::class, 'updateAddress'])->name('profile.update-address');
    Route::delete('/profile/delete-account', [ProfileController::class, 'destroy'])->name('profile.delete-account');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/order-summary/{order}', [CheckoutController::class, 'orderSummary'])->name('order-summary');
});

Route::view('/privacy', 'pages.site.privacy')->name('privacy');

Route::get('/', [SiteProductController::class, 'index'])->name('products.index');
Route::get('/product/{slug}', [SiteProductController::class, 'show'])->name('product.show');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('add-to-cart');
    Route::post('/remove-product', [CartController::class, 'removeProduct'])->name('remove-product');
    Route::post('/update-quantity', [CartController::class, 'updateQuantity'])->name('update-quantity');
});

require __DIR__.'/auth.php';
