<?php

use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/product/edit/{id?}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/save', [ProductController::class, 'save'])->name('product.save');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/order/edit', [OrderController::class, 'edit'])->name('order.edit');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/user/edit', [UserController::class, 'edit'])->name('user.edit');
    
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('/role/edit', [RoleController::class, 'edit'])->name('role.edit');

    Route::get('/product-categories', [ProductCategoryController::class, 'index'])->name('product-categories');
    Route::get('/product-category/edit/{id?}', [ProductCategoryController::class, 'edit'])->name('product-category.edit');
    Route::post('/product-category/save', [ProductCategoryController::class, 'save'])->name('product-category.save');
    Route::delete('/product-category/delete', [ProductCategoryController::class, 'delete'])->name('product-category.delete');
});