@extends('pages.admin.layout')

@section('title', 'Order details – ShopDemo Admin')

@section('content')
    @php
        $status = $order->status ?? 'Unknown';
        $badgeClass = match ($status) {
            'Delivered' => 'text-emerald-900 bg-emerald-100 ring-1 ring-inset ring-emerald-600/15',
            'Shipped', 'Processing' => 'text-sky-900 bg-sky-100 ring-1 ring-inset ring-sky-600/15',
            'Pending' => 'text-amber-900 bg-amber-100 ring-1 ring-inset ring-amber-600/15',
            'Unpaid', 'Cancelled' => 'text-rose-900 bg-rose-100 ring-1 ring-inset ring-rose-600/15',
            default => 'text-slate-800 bg-slate-100 ring-1 ring-inset ring-slate-400/20',
        };
    @endphp

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Order #{{ $order->id }}
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Full order information and associated order items.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.order.edit', $order->id) }}"
               class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-white shadow-sm"
               style="background-color: var(--color-accent);">
                Edit order
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] text-sm">
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">Order details</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Order ID</p>
                        <p class="text-slate-900">#{{ $order->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Customer</p>
                        <p class="text-slate-900">{{ $order->customer_first_name }} {{ $order->customer_last_name }} ({{ $order->customer_email }})</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Total amount</p>
                        <p class="text-slate-900">${{ $order->total_amount ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Shipping amount</p>
                        <p class="text-slate-900">${{ $order->shipping_amount ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $badgeClass }}">
                            {{ $status }}
                        </span>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Payment method</p>
                        <p class="text-slate-900">{{ \App\Constants\PaymentMethodConstant::PAYMENT_METHODS[$order->payment_method] ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">Delivery information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Delivery address</p>
                        <p class="text-slate-900">{{ $order->delivery_address ?? '-' }}</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">City</p>
                            <p class="text-slate-900">{{ $order->city ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">State</p>
                            <p class="text-slate-900">{{ $order->state ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">ZIP</p>
                            <p class="text-slate-900">{{ $order->zip ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Country</p>
                            <p class="text-slate-900">{{ $order->country ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">Order items</h2>
                <div class="rounded-lg border border-slate-200 overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
                        <tr class="text-left">
                            <th class="px-4 py-3">Product ID</th>
                            <th class="px-4 py-3">Quantity</th>
                            <th class="px-4 py-3">Price</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                        @forelse ($order->orderProducts ?? [] as $item)
                            <tr>
                                <td class="px-4 py-3 text-slate-900">{{ $item->product_id }}</td>
                                <td class="px-4 py-3 text-slate-900">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-slate-900">
                                    ${{ $item->price ?? 0 }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-center text-slate-600">
                                    No order items found
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">Customer information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">First name</p>
                        <p class="text-slate-900">{{ $order->customer_first_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Last name</p>
                        <p class="text-slate-900">{{ $order->customer_last_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Email</p>
                        <p class="text-slate-900">{{ $order->customer_email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Phone</p>
                        <p class="text-slate-900">{{ $order->customer_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Customer notes</p>
                        <p class="text-slate-900 whitespace-pre-wrap">{{ $order->customer_notes ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">Timestamps</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Created at</p>
                        <p class="text-slate-900">
                            {{ $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Updated at</p>
                        <p class="text-slate-900">
                            {{ $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
