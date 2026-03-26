@extends('pages.site.layout')

@section('title', 'Cart – ShopDemo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6">
            Shopping cart
        </h1>

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
                    @foreach (range(1, 3) as $i)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-md bg-slate-100 flex-shrink-0"></div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-900">
                                            Demo product {{ $i }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            Category
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-700">
                                $29.00
                            </td>
                            <td class="px-4 py-4">
                                <input type="number" min="1" value="1"
                                       class="w-16 rounded-md border border-slate-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-slate-900">
                                $29.00
                            </td>
                            <td class="px-4 py-4 text-right">
                                <button type="button"
                                        class="text-xs text-slate-400 hover:text-slate-600">
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
                            <dd class="font-medium text-slate-900">$87.00</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Shipping</dt>
                            <dd class="text-slate-500">Calculated at checkout</dd>
                        </div>
                        <div class="border-t border-slate-200 pt-3 mt-2 flex items-center justify-between text-sm">
                            <dt class="font-semibold text-slate-900">Total</dt>
                            <dd class="font-semibold text-slate-900">$87.00</dd>
                        </div>
                    </dl>

                    <a href="{{ route('checkout') ?? '#' }}"
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
    </div>
@endsection

