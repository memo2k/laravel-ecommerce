<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SelectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/product/edit/{id?}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/save', [ProductController::class, 'save'])->name('product.save');
    Route::delete('/product/delete', [ProductController::class, 'delete'])->name('product.delete');
    Route::post('/product/add-attribute-option', [ProductController::class, 'addAttributeOption'])->name('product.add-attribute-option');
    Route::post('/product/remove-attribute-option', [ProductController::class, 'removeAttributeOption'])->name('product.remove-attribute-option');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/order/view/{id}', [OrderController::class, 'view'])->name('order.view');
    Route::get('/order/edit/{id?}', [OrderController::class, 'edit'])->name('order.edit');
    Route::post('/order/save', [OrderController::class, 'save'])->name('order.save');
    
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/user/edit/{id?}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/save', [UserController::class, 'save'])->name('user.save');
    
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('/role/edit/{id?}', [RoleController::class, 'edit'])->name('role.edit');
    Route::post('/role/save', [RoleController::class, 'save'])->name('role.save');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions');
    Route::get('/permission/edit/{id?}', [PermissionController::class, 'edit'])->name('permission.edit');
    Route::post('/permission/save', [PermissionController::class, 'save'])->name('permission.save');

    Route::get('/product-categories', [ProductCategoryController::class, 'index'])->name('product-categories');
    Route::get('/product-category/edit/{id?}', [ProductCategoryController::class, 'edit'])->name('product-category.edit');
    Route::post('/product-category/save', [ProductCategoryController::class, 'save'])->name('product-category.save');
    Route::delete('/product-category/delete', [ProductCategoryController::class, 'delete'])->name('product-category.delete');
    Route::post('/product-category/add-attribute', [ProductCategoryController::class, 'addAttribute'])->name('product-category.add-attribute');
    Route::post('/product-category/remove-attribute', [ProductCategoryController::class, 'removeAttribute'])->name('product-category.remove-attribute');

    Route::get('/attributes', [AttributeController::class, 'index'])->name('attributes');
    Route::get('/attribute/edit/{id?}', [AttributeController::class, 'edit'])->name('attribute.edit');
    Route::post('/attribute/save', [AttributeController::class, 'save'])->name('attribute.save');
    Route::delete('/attribute/delete', [AttributeController::class, 'delete'])->name('attribute.delete');

    Route::get('/select', [SelectController::class, 'index'])->name('select');
});