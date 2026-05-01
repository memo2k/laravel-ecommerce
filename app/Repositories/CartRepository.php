<?php

namespace App\Repositories;

use App\Constants\SettingConstant;
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
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'price' => (float) ($item->product->price ?? 0),
                'discount_price' => (float) ($item->product->discount_price ?? 0),
                'image' => $item->product->image ?? null,
            ];
        })->toArray();

        $itemsTotalAmount = array_sum(array_map(fn ($item) => ($item['discount_price'] > 0 ? $item['discount_price'] : $item['price']) * $item['quantity'], $items));
        $shippingAmount = $itemsTotalAmount > setting_value(SettingConstant::FREE_SHIPPING_MIN_ORDER_AMOUNT) 
            ? 0 
            : setting_value(SettingConstant::SHIPPING_AMOUNT);

        $totalAmount = $itemsTotalAmount + $shippingAmount;

        $cartData = [
            'items' => $items,
            'totalProducts' => array_sum(array_column($items, 'quantity')),
            'itemsTotalAmount' => (float) $itemsTotalAmount,
            'shippingAmount' => (float) $shippingAmount,
            'totalPrice' => (float) $totalAmount,
        ];

        return $cartData;
    }
}
