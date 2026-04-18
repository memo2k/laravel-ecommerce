@extends('pages.admin.layout')

@section('title', 'Edit order – ShopDemo Admin')

@section('content')
    @php
        $isEdit = !empty($order?->id);
        $statuses = \App\Constants\OrderStatusConstant::ORDER_STATUSES;
    @endphp

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                {{ $isEdit ? 'Edit order' : 'Create order' }}
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Manage all order details from the orders table.
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            Please correct the highlighted fields and try again.
        </div>
    @endif

    <form action="{{ route('admin.order.save') }}" method="POST" class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] text-sm">
        @csrf
        <input type="hidden" name="id" value="{{ old('id', $order->id) }}">

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Order details
                </h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            ID
                        </label>
                        <input type="text" value="{{ $order->id ?? 'New order' }}" readonly
                               class="block w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Total amount
                        </label>
                        <input type="number" step="0.01" min="0" name="total_amount"
                               value="{{ old('total_amount', $order->total_amount) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Status
                        </label>
                        <select name="status"
                                class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Payment method
                        </label>
                        <select name="payment_method"
                                class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            @foreach (\App\Constants\PaymentMethodConstant::PAYMENT_METHODS as $key => $value)
                                <option value="{{ $key }}" @selected(old('payment_method', $order->payment_method) === $key)>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Delivery information
                </h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Delivery address
                        </label>
                        <input type="text" name="delivery_address"
                               value="{{ old('delivery_address', $order->delivery_address) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            City
                        </label>
                        <input type="text" name="city"
                               value="{{ old('city', $order->city) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            State
                        </label>
                        <input type="text" name="state"
                               value="{{ old('state', $order->state) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            ZIP
                        </label>
                        <input type="text" name="zip"
                               value="{{ old('zip', $order->zip) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Country
                        </label>
                        <input type="text" name="country"
                               value="{{ old('country', $order->country) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Customer information
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            First name
                        </label>
                        <input type="text" name="customer_first_name"
                               value="{{ old('customer_first_name', $order->customer_first_name) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Last name
                        </label>
                        <input type="text" name="customer_last_name"
                               value="{{ old('customer_last_name', $order->customer_last_name) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Phone
                        </label>
                        <input type="text" name="customer_phone"
                               value="{{ old('customer_phone', $order->customer_phone) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Email
                        </label>
                        <input type="email" name="customer_email"
                               value="{{ old('customer_email', $order->customer_email) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">Metadata</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Created at
                        </label>
                        <input type="text"
                               value="{{ $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '-' }}"
                               readonly
                               class="block w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Updated at
                        </label>
                        <input type="text"
                               value="{{ $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : '-' }}"
                               readonly
                               class="block w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                    </div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        Save order
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
