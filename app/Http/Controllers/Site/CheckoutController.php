<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartData = (new CartRepository())->getCartData();
        return view('pages.site.checkout', ['cartData' => $cartData]);
    }
}