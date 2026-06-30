@extends('pages.site.layout')

@section('title', ($product->name ?? 'Product') . ' – ShopDemo')

@section('content')
    @php
        $stock = $product->stock > $lowStockThreshold 
            ? \App\Constants\ProductStockConstant::IN_STOCK 
            : ($product->stock > 0 ? \App\Constants\ProductStockConstant::LOW_STOCK : \App\Constants\ProductStockConstant::OUT_OF_STOCK);

        $stockBadgeClass = match ($stock) {
            \App\Constants\ProductStockConstant::IN_STOCK => 'text-black bg-emerald-200',
            \App\Constants\ProductStockConstant::LOW_STOCK => 'text-black bg-amber-200',
            \App\Constants\ProductStockConstant::OUT_OF_STOCK => 'text-black bg-red-200',
            default => 'text-black bg-slate-200',
        };
    @endphp
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid gap-10 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6">
                <div class="flex aspect-[4/3] items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-100 mb-4">
                    @if (!empty($product->image))
                        <img
                            src="{{ asset('storage/' . ltrim($product->image, '/')) }}"
                            alt="{{ $product->name }}"
                            class="max-h-full max-w-full object-contain"
                        >
                    @else
                        <span class="text-sm text-slate-500">No image</span>
                    @endif
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

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $stockBadgeClass }}">
                        {{ \App\Constants\ProductStockConstant::PRODUCT_STOCK_STATE_LABELS[$stock] }}
                    </span>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if ($stock != \App\Constants\ProductStockConstant::OUT_OF_STOCK)
                        <button type="button"
                                data-product-id="{{ $product->id }}"
                                class="inline-flex items-center rounded-md px-5 py-2.5 text-sm font-medium text-white shadow-sm add-to-cart"
                                style="background-color: var(--color-accent);">
                            Add to cart
                        </button>
                    @endif
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