@extends('pages.site.layout')

@section('title', 'Checkout – ShopDemo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6">
            Checkout
        </h1>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <!-- Shipping / billing form -->
            <form class="space-y-6 rounded-xl border border-slate-200 bg-white p-5 text-sm">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            First name
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            Last name
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                        Email
                    </label>
                    <input type="email"
                           class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                        Address
                    </label>
                    <input type="text"
                           class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            City
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            ZIP / Postal code
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                            Country
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-4">
                    <label class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-1">
                        Notes (optional)
                    </label>
                    <textarea rows="3"
                              class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"></textarea>
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
                        @foreach (range(1, 2) as $i)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-md bg-slate-100 flex-shrink-0"></div>
                                    <div>
                                        <div class="font-medium text-slate-900">
                                            Demo product {{ $i }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            Qty 1
                                        </div>
                                    </div>
                                </div>
                                <div class="font-medium text-slate-900">
                                    $29.00
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <dl class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Subtotal</dt>
                            <dd class="font-medium text-slate-900">$58.00</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Shipping</dt>
                            <dd class="text-slate-500">$5.00</dd>
                        </div>
                        <div class="border-t border-slate-200 pt-3 mt-2 flex items-center justify-between">
                            <dt class="font-semibold text-slate-900">Total</dt>
                            <dd class="font-semibold text-slate-900">$63.00</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                    <p class="mb-1 font-medium text-slate-700">
                        Demo notice
                    </p>
                    <p>This checkout is for portfolio/demo purposes. Hook it to real payment logic when you are ready.</p>
                </div>
            </aside>
        </div>
    </div>
@endsection

