<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\CartProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('pages.components.header', function ($view) {
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
                : new Collection();

            $cartData = $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product->name ?? 'Product',
                    'quantity' => $item->quantity,
                    'price' => (float) ($item->product->price ?? 0),
                    'image' => $item->product->image ?? null,
                ];
            });

            $view->with('cartData', $cartData);
            $view->with('cartTotalProducts', $cartData->sum('quantity'));
            $view->with(
                'cartTotalPrice',
                number_format($cartData->sum(fn ($item) => $item['price'] * $item['quantity']), 2)
            );
        });
    }
}
