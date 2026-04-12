<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Site\CartController;
use App\Http\Controllers\Site\CheckoutController;
use App\Http\Controllers\Site\HomepageController;
use App\Http\Controllers\Site\ProductController as SiteProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomepageController::class, 'index'])->name('homepage');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::get('/products', [SiteProductController::class, 'index'])->name('products.index');
Route::get('/product/{slug}', [SiteProductController::class, 'show'])->name('product.show');
Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::post('/add-to-cart', [SiteProductController::class, 'addToCart'])->name('add-to-cart');
Route::post('/remove-from-cart', [SiteProductController::class, 'removeFromCart'])->name('remove-from-cart');
Route::post('/update-cart-quantity', [SiteProductController::class, 'updateCartQuantity'])->name('update-cart-quantity');

require __DIR__.'/auth.php';
