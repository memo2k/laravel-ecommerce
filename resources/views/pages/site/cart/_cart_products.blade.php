@if ($cartData['items']->isEmpty())
    <p class="text-slate-600">No products in your cart.</p>
@else
<div class="grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
    <!-- Cart items -->
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-left text-xs font-medium text-slate-500 uppercase tracking-wide">
                <th class="px-4 py-3">Product</th>
                <th class="px-4 py-3">Price</th>
                <th class="px-4 py-3">Qty</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3"></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @foreach ($cartData['items'] as $item)
                <tr>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-md bg-slate-100 flex-shrink-0">
                                @if (!empty($item['image']))
                                    <img src="{{ asset('storage/' . ltrim($item['image'], '/')) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-[10px] text-slate-400">No image</div>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $item['name'] }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-700">
                        {{ $item['price'] }}
                    </td>
                    <td class="px-4 py-4">
                        <div class="inline-flex items-stretch rounded-md border border-slate-300 bg-white shadow-sm"
                             role="group" aria-label="Quantity for {{ $item['name'] }}">
                            <button type="button"
                                    class="update-cart-quantity-btn px-2.5 py-1 text-sm font-medium text-slate-600 hover:bg-slate-50 border-r border-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-slate-300"
                                    data-product-id="{{ $item['id'] }}"
                                    data-action="decrement"
                                    aria-label="Decrease quantity">
                                −
                            </button>
                            <span class="flex min-w-[2.75rem] items-center justify-center px-2 py-1 text-sm font-medium text-slate-900 tabular-nums select-none border-r border-slate-200 bg-slate-50/50">
                                {{ $item['quantity'] }}
                            </span>
                            <button type="button"
                                    class="update-cart-quantity-btn px-2.5 py-1 text-sm font-medium text-slate-600 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-slate-300"
                                    data-product-id="{{ $item['id'] }}"
                                    data-action="increment"
                                    aria-label="Increase quantity">
                                +
                            </button>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm font-medium text-slate-900">
                        {{ $item['price'] * $item['quantity'] }}
                    </td>
                    <td class="px-4 py-4 text-right">
                        <button type="button" data-product-id="{{ $item['id'] }}"
                                class="text-xs text-slate-400 hover:text-slate-600 remove-product-btn">
                            Remove
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <aside class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">
                Order summary
            </h2>
            <dl class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-slate-500">Subtotal</dt>
                    <dd class="font-medium text-slate-900">{{ $cartData['totalPrice'] }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-slate-500">Shipping</dt>
                    <dd class="text-slate-500">Calculated at checkout</dd>
                </div>
                <div class="border-t border-slate-200 pt-3 mt-2 flex items-center justify-between text-sm">
                    <dt class="font-semibold text-slate-900">Total</dt>
                    <dd class="font-semibold text-slate-900">{{ $cartData['totalPrice'] }}</dd>
                </div>
            </dl>

            <a href="{{ route('checkout.index') ?? '#' }}"
               class="mt-5 block w-full text-center rounded-md px-4 py-2.5 text-sm font-medium text-white shadow-sm"
               style="background-color: var(--color-accent);">
                Checkout
            </a>
        </div>

        <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-xs text-slate-600">
            <p class="mb-1 font-medium text-slate-700">
                Need a promo code?
            </p>
            <p>Apply discounts in a future step once you wire up backend logic.</p>
        </div>
    </aside>
</div>
@endif