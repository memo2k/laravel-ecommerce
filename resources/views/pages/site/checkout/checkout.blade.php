@extends('pages.site.layout')

@section('title', 'Checkout – ShopDemo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6">
            Checkout
        </h1>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <!-- Shipping / billing form -->
            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6 rounded-xl border border-slate-200 bg-white p-5 text-sm">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            First name
                        </label>
                        <input type="text" name="first_name"
                               value="{{ old('first_name') }}"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        @error('first_name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            Last name
                        </label>
                        <input type="text" name="last_name"
                               value="{{ old('last_name') }}"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        @error('last_name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            Email
                        </label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        @error('email')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            Phone
                        </label>
                        <input type="tel" name="phone" autocomplete="tel"
                               value="{{ old('phone') }}"
                               placeholder="+1 555 0100"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        @error('phone')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                        Address
                    </label>
                    <input type="text" name="address"
                           value="{{ old('address') }}"
                           class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        @error('address')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            City
                        </label>
                        <input type="text" name="city"
                               value="{{ old('city') }}"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        @error('city')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            ZIP / Postal code
                        </label>
                        <input type="text" name="zip"
                               value="{{ old('zip') }}"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        @error('zip')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            Country
                        </label>
                        <input type="text" name="country"
                               value="{{ old('country') }}"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        @error('country')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-4">
                    <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                        Notes (optional)
                    </label>
                    <textarea rows="3" name="notes"
                              class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">{{ old('notes') }}</textarea>
                    @error('notes')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="border-t border-slate-200 pt-4">
                    <fieldset>
                        <legend class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-3">
                            Payment method
                        </legend>
                        <div class="space-y-3">
                            @foreach (\App\Constants\PaymentMethodConstant::PAYMENT_METHODS as $key => $value)
                                <label class="flex cursor-pointer items-start gap-3 rounded-md border border-slate-200 px-3 py-2.5 has-[:checked]:border-slate-400 has-[:checked]:bg-slate-50">
                                    <input type="radio" name="payment_method" value="{{ $key }}"
                                           class="mt-0.5 h-4 w-4 border-slate-300 text-slate-900 focus:ring-slate-400"
                                           {{ old('payment_method', 'cash_on_delivery') === $key ? 'checked' : '' }}>
                                    <span>
                                        <span class="block font-medium text-slate-900">{{ $value }}</span>
                                        <span class="text-xs text-slate-500">{{ $value === 'Cash on delivery' ? 'Pay when your order arrives.' : 'Demo only — no card details collected.' }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_method')
                            <span class="mt-2 block text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </fieldset>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center rounded-md px-5 py-2.5 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        Place order
                    </button>
                </div>
            </form>

            <!-- Order summary -->
            <aside class="space-y-4">
                <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm">
                    <h2 class="text-sm font-semibold text-slate-900 mb-4">
                        Order summary
                    </h2>

                    <div class="space-y-3 mb-4">
                        @foreach ($cartData['items'] as $item)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-md bg-slate-100 flex-shrink-0">
                                        @if ($item['image'])
                                            <img src="{{ asset('storage/' . ltrim($item['image'], '/')) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                                <span class="text-xs text-slate-500">No image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-900">
                                            {{ $item['name'] }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            Qty {{ $item['quantity'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="font-medium text-slate-900">
                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <dl class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Subtotal</dt>
                            <dd class="font-medium text-slate-900">${{ $cartData['totalPrice'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Shipping</dt>
                            <dd class="text-slate-500">Calculated at checkout</dd>
                        </div>
                        <div class="border-t border-slate-200 pt-3 mt-2 flex items-center justify-between">
                            <dt class="font-semibold text-slate-900">Total</dt>
                            <dd class="font-semibold text-slate-900">${{ $cartData['totalPrice'] }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                    <p class="mb-1 font-medium text-slate-700">
                        This checkout is for portfolio/demo purposes. Hook it to real payment logic when you are ready.
                    </p>
                </div>
            </aside>
        </div>
    </div>
@endsection

