@extends('pages.admin.layout')

@section('title', 'Edit order – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Edit order
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Update fulfillment status, shipping details, and internal notes.
            </p>
        </div>
        <a href="#"
           class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 hover:bg-slate-100">
            Back to orders
        </a>
    </div>

    <form class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] text-sm">
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Order summary
                </h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Order number
                        </label>
                        <input type="text" value="#1001" readonly
                               class="block w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Placed on
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Customer
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Shipping
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Shipping address
                        </label>
                        <textarea rows="3"
                                  class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Tracking number
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Status
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Fulfillment
                        </label>
                        <select
                            class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option>Pending</option>
                            <option>Processing</option>
                            <option>Shipped</option>
                            <option>Delivered</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Internal notes
                        </label>
                        <textarea rows="4"
                                  class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500"
                                  placeholder="Visible to staff only."></textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Actions
                </h2>
                <div class="space-y-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        Save order
                    </button>
                    <button type="button"
                            class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-xs font-medium border border-slate-300 text-slate-700 hover:bg-slate-100">
                        Email customer
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
