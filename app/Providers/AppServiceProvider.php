<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Repositories\CartRepository;
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
            $cartData = (new CartRepository())->getCartData();
            $view->with('cartData', $cartData);
        });
    }
}
