@extends('pages.admin.layout')

@section('title', 'Edit product – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-white">
                Edit product
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Update product details, pricing, and availability.
            </p>
        </div>
        <a href="#"
           class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-slate-300 border border-slate-700 hover:bg-slate-800">
            Back to products
        </a>
    </div>

    <form class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] text-sm">
        <!-- Main column -->
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-100">
                    Basic information
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wide mb-1">
                            Name
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wide mb-1">
                            Slug
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wide mb-1">
                            Description
                        </label>
                        <textarea rows="4"
                                  class="block w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-100">
                    Pricing & inventory
                </h2>
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wide mb-1">
                            Price
                        </label>
                        <input type="number" step="0.01"
                               class="block w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wide mb-1">
                            Compare at price
                        </label>
                        <input type="number" step="0.01"
                               class="block w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wide mb-1">
                            Stock quantity
                        </label>
                        <input type="number"
                               class="block w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Side column -->
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-100">
                    Catalog
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wide mb-1">
                            Category
                        </label>
                        <select
                            class="block w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option>Demo category</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wide mb-1">
                            Status
                        </label>
                        <select
                            class="block w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option>Active</option>
                            <option>Draft</option>
                            <option>Archived</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-100">
                    Actions
                </h2>
                <div class="space-y-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        Save product
                    </button>
                    <button type="button"
                            class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-xs font-medium border border-slate-700 text-slate-300 hover:bg-slate-800">
                        Save as draft
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

