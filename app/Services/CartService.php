<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function clearCart()
    {
        if (Auth::check()) {
            Cart::query()->where('user_id', Auth::id())->delete();
        } else {
            session()->forget('guest_cart_id');
        }
    }
}