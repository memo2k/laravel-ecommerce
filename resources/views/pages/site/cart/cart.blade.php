@extends('pages.site.layout')

@section('title', 'Cart – ShopDemo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6">
            Shopping cart
        </h1>

        <div id="cart_products">
            @include('pages.site.cart._cart_products', ['cartData' => $cartData])
        </div>
    </div>
@endsection