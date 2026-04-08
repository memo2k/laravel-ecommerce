@extends('pages.admin.layout')

@section('title', 'Orders – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Orders
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Review and manage customer orders for your demo storefront.
            </p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
            <tr class="text-left">
                <th class="px-4 py-3">Order</th>
                <th class="px-4 py-3">Customer</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Status</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @forelse ($orders as $order)
                <tr
                    class="hover:bg-slate-50 transition-colors cursor-pointer"
                    onclick="window.location='{{ route('admin.order.view', $order->id) }}'"
                    onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location='{{ route('admin.order.view', $order->id) }}'; }"
                    tabindex="0"
                    role="link"
                    aria-label="Edit order #{{ $order->id }}"
                >
                    <td class="px-4 py-3 text-slate-900 font-mono text-xs">
                        #{{ $order->id }}
                    </td>
                    <td class="px-4 py-3 text-slate-900">
                        {{ $order->customer_first_name }} {{ $order->customer_last_name }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ $order->created_at->format('M j, Y') }}
                    </td>
                    <td class="px-4 py-3 text-slate-900">
                        ${{ $order->total_amount }}.00
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
                            $status = $order->status;
                            $badgeClass = match ($status) {
                                'Delivered' => 'text-emerald-200 bg-emerald-500/20',
                                'Shipped', 'Processing' => 'text-sky-200 bg-sky-500/20',
                                'Pending' => 'text-amber-200 bg-amber-500/20',
                                default => 'text-slate-700 bg-slate-100',
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $badgeClass }}">
                            {{ $status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-slate-600">
                        No orders found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
