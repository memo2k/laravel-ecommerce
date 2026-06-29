@extends('pages.admin.layout')

@section('title', 'Dashboard – ShopDemo Admin')

@section('content')
    @php
        use App\Constants\OrderStatusConstant;
        use App\Constants\ProductStockConstant;

        $statusBadgeClasses = [
            OrderStatusConstant::DELIVERED => 'text-emerald-900 bg-emerald-100 ring-1 ring-inset ring-emerald-600/15',
            OrderStatusConstant::SHIPPED => 'text-sky-900 bg-sky-100 ring-1 ring-inset ring-sky-600/15',
            OrderStatusConstant::PROCESSING => 'text-sky-900 bg-sky-100 ring-1 ring-inset ring-sky-600/15',
            OrderStatusConstant::PENDING => 'text-amber-900 bg-amber-100 ring-1 ring-inset ring-amber-600/15',
            OrderStatusConstant::UNPAID => 'text-rose-900 bg-rose-100 ring-1 ring-inset ring-rose-600/15',
            OrderStatusConstant::CANCELLED => 'text-rose-900 bg-rose-100 ring-1 ring-inset ring-rose-600/15',
        ];

        $periodLabels = [
            '7'  => 'last 7 days',
            '30' => 'last 30 days',
            '90' => 'last 90 days',
        ];
        $periodLabel = $periodLabels[$period] ?? 'last 30 days';

        $lowStockCount = $lowStockProducts->count();
    @endphp

    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Dashboard</h1>
            <p class="text-sm text-slate-600 mt-1">Overview for the {{ $periodLabel }}.</p>
        </div>
        <div class="flex items-center gap-2">
            <label for="dashboard_period" class="sr-only">Time range</label>
            <select id="dashboard_period"
                    class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <option value="7"  @selected($period === '7')>Last 7 days</option>
                <option value="30" @selected($period === '30')>Last 30 days</option>
                <option value="90" @selected($period === '90')>Last 90 days</option>
            </select>
            <a href="{{ route('admin.orders') }}"
               class="inline-flex shrink-0 items-center justify-center rounded-md px-4 py-2 text-xs font-medium text-white shadow-sm hover:opacity-90 transition"
               style="background-color: var(--color-accent);">
                View orders
            </a>
        </div>
    </header>

    <section class="grid gap-3 sm:grid-cols-3 mb-8" aria-label="Key metrics">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Revenue</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">${{ number_format($totalRevenue, 2) }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Orders</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">{{ number_format($totalOrders) }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Low stock</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">{{ number_format($lowStockCount) }}</p>
            <p class="mt-1 text-[11px] text-slate-500">at or below {{ $lowStockThreshold }} units</p>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-2 mb-8">
        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-900">Recent orders</h2>
                <a href="{{ route('admin.orders') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">View all</a>
            </div>

            <div class="overflow-x-auto text-sm">
                <table class="min-w-full">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
                        <tr class="text-left">
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($recentOrders->take(5) as $order)
                            <tr class="hover:bg-slate-50 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('admin.order.view', $order->id) }}'">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs font-semibold text-slate-900">#{{ $order->id }}</span>
                                    <span class="block text-[11px] text-slate-500">{{ optional($order->created_at)->format('M j, Y') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $statusBadgeClasses[$order->status] ?? 'text-slate-800 bg-slate-100 ring-1 ring-inset ring-slate-400/20' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums font-medium text-slate-900">
                                    ${{ number_format((float) $order->total_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-slate-600">No orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-900">Low stock</h2>
                <a href="{{ route('admin.products') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">View products</a>
            </div>

            @if ($lowStockProducts->isEmpty())
                <div class="px-5 py-10 text-center">
                    <p class="text-sm font-medium text-slate-700">All products are well stocked</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($lowStockProducts->take(5) as $product)
                        @php
                            $stockQty = (int) $product->stock;
                            $stockState = $stockQty <= 0
                                ? ProductStockConstant::OUT_OF_STOCK
                                : ProductStockConstant::LOW_STOCK;
                        @endphp
                        <li class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
                            <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}"
                               class="text-sm font-medium text-slate-900 hover:text-sky-600 truncate">
                                {{ $product->name }}
                            </a>
                            <span class="shrink-0 text-[11px] font-medium text-slate-600 tabular-nums">
                                {{ $stockQty }} left
                                @if ($stockState === ProductStockConstant::OUT_OF_STOCK)
                                    <span class="text-rose-600">· Out</span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#dashboard_period').on('change', function() {
                var url = new URL(window.location.href);
                url.searchParams.set('period', $(this).val());
                window.location.href = url.toString();
            });
        });
    </script>
@endsection
