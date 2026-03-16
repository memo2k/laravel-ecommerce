@extends('pages.site.layout')

@section('title', 'ShopDemo – Simple ecommerce demo')

@section('content')
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid gap-10 md:grid-cols-2 items-center">
            <div>
                <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-slate-900 mb-4">
                    A clean ecommerce demo for your portfolio.
                </h1>
                <p class="text-sm text-slate-600 mb-6">
                    Browse products, add them to your cart, and walk through a simple checkout flow.
                    This UI is intentionally minimal and easy to extend.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('products.index') ?? '#' }}"
                       class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                       style="background-color: var(--color-accent);">
                        Start shopping
                    </a>
                    <a href="{{ route('cart.index') ?? '#' }}"
                       class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium border border-slate-300 text-slate-700 bg-white hover:bg-slate-50">
                        View cart
                    </a>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-6 py-8">
                <div class="text-xs font-medium text-slate-500 mb-4 uppercase tracking-wide">
                    Demo storefront
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach (range(1, 4) as $i)
                        <div class="rounded-lg border border-slate-200 bg-white p-4 flex flex-col gap-2">
                            <div class="aspect-[4/3] rounded-md bg-slate-100"></div>
                            <div class="flex-1">
                                <div class="text-xs font-medium text-slate-500 mb-1">
                                    Category
                                </div>
                                <div class="text-sm font-semibold text-slate-900">
                                    Demo product {{ $i }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-900">$29.00</span>
                                <button type="button"
                                        class="rounded-full px-3 py-1 text-[11px] font-medium text-white"
                                        style="background-color: var(--color-accent);">
                                    Add
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid gap-6 md:grid-cols-3 text-sm">
            <div class="flex items-start gap-3">
                <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold text-white"
                      style="background-color: var(--color-accent);">
                    1
                </span>
                <div>
                    <div class="font-medium text-slate-900">Simple color system</div>
                    <p class="text-slate-600">
                        Configure three colors once and reuse them across the entire UI.
                    </p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold text-white"
                      style="background-color: var(--color-accent);">
                    2
                </span>
                <div>
                    <div class="font-medium text-slate-900">Spacious product cards</div>
                    <p class="text-slate-600">
                        Flat, readable product layouts that work well on any device.
                    </p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold text-white"
                      style="background-color: var(--color-accent);">
                    3
                </span>
                <div>
                    <div class="font-medium text-slate-900">Ready for backend wiring</div>
                    <p class="text-slate-600">
                        Replace placeholders with real data and routes when you are ready.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

