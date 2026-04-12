<a href="{{ route('cart.index') ?? '#' }}"
    class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-medium hover:bg-white/20 transition-colors">
    <span class="mr-1">Cart</span>
    <span class="inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-[11px]">
        {{ $cartData['totalProducts'] }}
    </span>
</a>

<div class="invisible absolute right-0 top-full z-20 mt-2 w-[27rem] translate-y-1 rounded-lg border border-slate-200 bg-white p-4 text-slate-800 opacity-0 shadow-lg transition-all duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
    <div class="mb-2 flex items-center justify-between">
        <p class="text-sm font-semibold text-slate-900">Cart preview</p>
        <span class="text-xs text-slate-500">{{ $cartData['totalProducts'] }} items</span>
    </div>

    @if ($cartData['items']->isEmpty())
        <p class="rounded-md bg-slate-50 px-3 py-2 text-xs text-slate-600">
            Your cart is empty.
        </p>
    @else
        <ul class="max-h-72 space-y-2 overflow-y-auto pr-1">
            @foreach ($cartData['items']->take(4) as $item)
                <li class="rounded-md border border-slate-100 px-3 py-2 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-md border border-slate-200 bg-slate-50">
                            @if (!empty($item['image']))
                                <img
                                    src="{{ str_starts_with($item['image'], 'http') ? $item['image'] : asset('storage/'.$item['image']) }}"
                                    alt="{{ $item['name'] }}"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center text-[10px] text-slate-400">No image</div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <p class="truncate pr-2 font-medium text-slate-800">{{ $item['name'] }}</p>
                                <button type="button" 
                                        data-product-id="{{ $item['id'] }}" 
                                        class="text-sm font-semibold leading-none text-slate-400 transition-colors hover:text-red-500 remove-product-btn" 
                                        aria-label="Remove item">
                                    x
                                </button>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <div class="inline-flex items-center rounded border border-slate-200">
                                    <button type="button" data-product-id="{{ $item['id'] }}" data-action="decrement" class="px-2 py-1 text-sm text-slate-700 hover:bg-slate-100 update-cart-quantity-btn" aria-label="Decrease quantity">
                                        -
                                    </button>
                                    <span class="min-w-7 border-x border-slate-200 px-2 py-1 text-center text-[11px] text-slate-700">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button type="button" data-product-id="{{ $item['id'] }}" data-action="increment" class="px-2 py-1 text-sm text-slate-700 hover:bg-slate-100 update-cart-quantity-btn" aria-label="Increase quantity">
                                        +
                                    </button>
                                </div>
                                <span class="whitespace-nowrap font-medium text-slate-700">
                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        @if (count($cartData['items']) > 4)
            <p class="mt-2 text-[11px] text-slate-500">
                +{{ count($cartData['items']) - 4 }} more item(s) in cart
            </p>
        @endif

        <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 text-xs">
            <span class="font-medium text-slate-700">Subtotal</span>
            <span class="font-semibold text-slate-900">${{ $cartData['totalPrice'] }}</span>
        </div>
    @endif

    <a href="{{ route('cart.index') ?? '#' }}"
        class="mt-3 inline-flex w-full items-center justify-center rounded-md px-3 py-2 text-xs font-semibold text-white"
        style="background-color: var(--color-accent);">
        Go to cart
    </a>
</div>