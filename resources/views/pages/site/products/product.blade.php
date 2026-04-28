@extends('pages.site.layout')

@section('title', ($product->name ?? 'Product') . ' – ShopDemo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid gap-10 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6">
                <div class="aspect-[4/3] rounded-lg bg-slate-100 mb-4 overflow-hidden border border-slate-200">
                    @if (!empty($product->image))
                        <img
                            src="{{ asset('storage/' . ltrim($product->image, '/')) }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover"
                        >
                    @endif
                </div>
                <div class="grid grid-cols-4 gap-2">
                    @foreach (range(1, 4) as $i)
                        <button type="button" class="aspect-square rounded-md bg-slate-100 border border-slate-200"></button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 mb-1">
                        {{ $product->name }}
                    </h1>
                    <p class="text-sm text-slate-500">
                        {{ $product->productCategory?->name ?? 'Category' }}
                    </p>
                </div>

                <div class="flex items-baseline gap-3">
                    @if ($product->discount_price > 0)
                        <span class="text-2xl font-semibold text-red-500">${{ number_format($product->discount_price, 2) }}</span>
                        <span class="text-xs text-slate-400 line-through">${{ number_format($product->price, 2) }}</span>
                    @else
                        <span class="text-2xl font-semibold text-slate-900">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <p class="text-sm text-slate-600">
                    {{ $product->description }}
                </p>

                <div class="flex flex-wrap gap-3">
                    <button type="button"
                            data-product-id="{{ $product->id }}"
                            class="inline-flex items-center rounded-md px-5 py-2.5 text-sm font-medium text-white shadow-sm add-to-cart"
                            style="background-color: var(--color-accent);">
                        Add to cart
                    </button>
                </div>

                <div class="border-t border-slate-200 pt-4 text-xs text-slate-500 space-y-1">
                    <div>• Stock available: {{ max((int) $product->stock, 0) }}</div>
                    <div>• Free shipping on orders over $50.</div>
                    <div>• Secure checkout with major payment providers.</div>
                    <div>• Easy returns within 30 days.</div>
                </div>
            </div>
        </div>
    </div>
@endsection