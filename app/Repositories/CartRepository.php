<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartRepository
{
    /**
     * Get the cart data
     *
     * @return array
     */
    public function getCartData()
    {
        $cartId = null;

        if (Auth::check()) {
            $cartId = Cart::query()
                ->where('user_id', Auth::id())
                ->value('id');
        }

        if (!$cartId) {
            $cartId = session('guest_cart_id');
        }

        $cartItems = $cartId
            ? CartProduct::query()->with('product')->where('cart_id', $cartId)->get()
            : new Collection;

        $items = $cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? 'Product',
                'quantity' => $item->quantity,
                'price' => (float) ($item->product->price ?? 0),
                'discount_price' => (float) ($item->product->discount_price ?? 0),
                'image' => $item->product->image ?? null,
            ];
        });

        $cartData = [
            'items' => $items,
            'totalProducts' => $items->sum('quantity'),
            'totalPrice' => round($items->sum(fn ($item) => ($item['discount_price'] > 0 ? $item['discount_price'] : $item['price']) * $item['quantity']), 2),
        ];

        return $cartData;
    }
}
