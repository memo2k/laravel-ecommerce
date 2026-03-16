@extends('pages.site.layout')

@section('title', 'Product – ShopDemo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid gap-10 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6">
                <div class="aspect-[4/3] rounded-lg bg-slate-100 mb-4"></div>
                <div class="grid grid-cols-4 gap-2">
                    @foreach (range(1, 4) as $i)
                        <button type="button" class="aspect-square rounded-md bg-slate-100 border border-slate-200"></button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 mb-1">
                        Demo product title
                    </h1>
                    <p class="text-sm text-slate-500">
                        Category name
                    </p>
                </div>

                <div class="flex items-baseline gap-3">
                    <span class="text-2xl font-semibold text-slate-900">$49.00</span>
                    <span class="text-sm text-slate-400 line-through">$69.00</span>
                </div>

                <p class="text-sm text-slate-600">
                    A short description of the product goes here. Keep it concise and easy to scan,
                    focusing on the main value for the customer.
                </p>

                <form class="space-y-4">
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-medium text-slate-700 uppercase tracking-wide">
                            Quantity
                        </label>
                        <input type="number" min="1" value="1"
                               class="w-20 rounded-md border border-slate-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit"
                                class="inline-flex items-center rounded-md px-5 py-2.5 text-sm font-medium text-white shadow-sm"
                                style="background-color: var(--color-accent);">
                            Add to cart
                        </button>
                        <button type="button"
                                class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium border border-slate-300 text-slate-700 bg-white hover:bg-slate-50">
                            Save for later
                        </button>
                    </div>
                </form>

                <div class="border-t border-slate-200 pt-4 text-xs text-slate-500 space-y-1">
                    <div>• Free shipping on orders over $50.</div>
                    <div>• Secure checkout with major payment providers.</div>
                    <div>• Easy returns within 30 days.</div>
                </div>
            </div>
        </div>
    </div>
@endsection

