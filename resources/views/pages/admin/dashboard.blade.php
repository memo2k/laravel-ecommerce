@extends('pages.admin.layout')

@section('title', 'Dashboard – ShopDemo Admin')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endsection

@section('content')
    @php
        use App\Constants\OrderStatusConstant;
        use App\Constants\ProductStockConstant;

        $inventoryCounts = $inventoryCounts ?? [
            ProductStockConstant::IN_STOCK => count($inStockProducts),
            ProductStockConstant::LOW_STOCK => count($lowStockProducts),
            ProductStockConstant::OUT_OF_STOCK => count($outOfStockProducts),
        ];

        $statusBadgeClasses = [
            OrderStatusConstant::DELIVERED => 'text-emerald-900 bg-emerald-100 ring-1 ring-inset ring-emerald-600/15',
            OrderStatusConstant::SHIPPED => 'text-sky-900 bg-sky-100 ring-1 ring-inset ring-sky-600/15',
            OrderStatusConstant::PROCESSING => 'text-sky-900 bg-sky-100 ring-1 ring-inset ring-sky-600/15',
            OrderStatusConstant::PENDING => 'text-amber-900 bg-amber-100 ring-1 ring-inset ring-amber-600/15',
            OrderStatusConstant::UNPAID => 'text-rose-900 bg-rose-100 ring-1 ring-inset ring-rose-600/15',
            OrderStatusConstant::CANCELLED => 'text-rose-900 bg-rose-100 ring-1 ring-inset ring-rose-600/15',
        ];

        $statusChartColors = [
            OrderStatusConstant::UNPAID => '#f43f5e',
            OrderStatusConstant::PENDING => '#f59e0b',
            OrderStatusConstant::PROCESSING => '#0ea5e9',
            OrderStatusConstant::SHIPPED => '#38bdf8',
            OrderStatusConstant::DELIVERED => '#10b981',
            OrderStatusConstant::CANCELLED => '#fb7185',
        ];

        $stockBadgeClasses = [
            ProductStockConstant::IN_STOCK => 'text-emerald-900 bg-emerald-100 ring-1 ring-inset ring-emerald-600/15',
            ProductStockConstant::LOW_STOCK => 'text-amber-900 bg-amber-100 ring-1 ring-inset ring-amber-600/15',
            ProductStockConstant::OUT_OF_STOCK => 'text-rose-900 bg-rose-100 ring-1 ring-inset ring-rose-600/15',
        ];

        $stockChartColors = [
            ProductStockConstant::IN_STOCK => '#10b981',
            ProductStockConstant::LOW_STOCK => '#f59e0b',
            ProductStockConstant::OUT_OF_STOCK => '#f43f5e',
        ];

        $resolveStockState = function ($stock) use ($lowStockThreshold) {
            $stock = (int) $stock;
            if ($stock <= 0) return ProductStockConstant::OUT_OF_STOCK;
            if ($stock <= $lowStockThreshold) return ProductStockConstant::LOW_STOCK;
            return ProductStockConstant::IN_STOCK;
        };

        $totalInventoryItems = array_sum($inventoryCounts);
        $needsAttentionCount = ($inventoryCounts[ProductStockConstant::LOW_STOCK] ?? 0)
            + ($inventoryCounts[ProductStockConstant::OUT_OF_STOCK] ?? 0);

        $totalOrdersInStatuses = array_sum($orderStatusCounts);
        $needsActionOrders = ($orderStatusCounts[OrderStatusConstant::UNPAID] ?? 0)
            + ($orderStatusCounts[OrderStatusConstant::PENDING] ?? 0)
            + ($orderStatusCounts[OrderStatusConstant::PROCESSING] ?? 0);

        $revenueChartPayload = [
            'labels' => $revenueTrend['labels'] ?? [],
            'revenue' => $revenueTrend['revenue'] ?? [],
            'orders' => $revenueTrend['orders'] ?? [],
        ];

        $orderStatusChartPayload = [
            'labels' => OrderStatusConstant::ORDER_STATUSES,
            'counts' => array_map(fn ($s) => $orderStatusCounts[$s] ?? 0, OrderStatusConstant::ORDER_STATUSES),
            'colors' => array_map(fn ($s) => $statusChartColors[$s], OrderStatusConstant::ORDER_STATUSES),
        ];

        $inventoryChartPayload = [
            'labels' => array_values(ProductStockConstant::PRODUCT_STOCK_STATE_LABELS),
            'counts' => [
                $inventoryCounts[ProductStockConstant::IN_STOCK] ?? 0,
                $inventoryCounts[ProductStockConstant::LOW_STOCK] ?? 0,
                $inventoryCounts[ProductStockConstant::OUT_OF_STOCK] ?? 0,
            ],
            'colors' => array_values($stockChartColors),
        ];

        $periodLabels = [
            '7'  => 'last 7 days',
            '30' => 'last 30 days',
            '90' => 'last 90 days',
        ];
        $periodLabel = $periodLabels[$period] ?? 'last 30 days';

        $formatDelta = function ($value) {
            $value = (float) $value;
            $sign = $value > 0 ? '+' : ($value < 0 ? '' : '');
            return $sign . number_format($value, 1) . '%';
        };

        $deltaClass = function ($value) {
            if ($value > 0) return 'text-emerald-700 bg-emerald-50 ring-emerald-600/15';
            if ($value < 0) return 'text-rose-700 bg-rose-50 ring-rose-600/15';
            return 'text-slate-600 bg-slate-100 ring-slate-400/20';
        };
    @endphp

    {{-- Header --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Dashboard</h1>
            <p class="text-sm text-slate-600 mt-1 max-w-lg">
                Snapshot of store performance for the <span id="dashboard_period_label" class="font-medium text-slate-700">{{ $periodLabel }}</span>.
            </p>
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

    {{-- KPI cards --}}
    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-8" aria-label="Key performance indicators">
        {{-- Revenue --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Revenue</p>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-emerald-50 text-emerald-600">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">${{ number_format($totalRevenue, 2) }}</p>
            <div class="mt-2 flex items-center gap-2 text-[11px]">
                <span class="inline-flex items-center rounded-full px-1.5 py-0.5 ring-1 ring-inset font-medium {{ $deltaClass($revenueDelta) }}">
                    {{ $formatDelta($revenueDelta) }}
                </span>
                <span class="text-slate-500">vs previous period</span>
            </div>
        </div>

        {{-- Orders --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Orders</p>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-sky-50 text-sky-600">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">{{ number_format($totalOrders) }}</p>
            <div class="mt-2 flex items-center gap-2 text-[11px]">
                <span class="inline-flex items-center rounded-full px-1.5 py-0.5 ring-1 ring-inset font-medium {{ $deltaClass($ordersDelta) }}">
                    {{ $formatDelta($ordersDelta) }}
                </span>
                <span class="text-slate-500">vs previous period</span>
            </div>
        </div>

        {{-- AOV --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Avg. order value</p>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-violet-50 text-violet-600">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">${{ number_format($avgOrderValue, 2) }}</p>
            <div class="mt-2 flex items-center gap-2 text-[11px]">
                <span class="inline-flex items-center rounded-full px-1.5 py-0.5 ring-1 ring-inset font-medium {{ $deltaClass($avgOrderValueDelta) }}">
                    {{ $formatDelta($avgOrderValueDelta) }}
                </span>
                <span class="text-slate-500">vs previous period</span>
            </div>
        </div>

        {{-- Customers --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">New customers</p>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-amber-50 text-amber-600">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">{{ number_format($totalCustomers) }}</p>
            <div class="mt-2 flex items-center gap-2 text-[11px]">
                <span class="inline-flex items-center rounded-full px-1.5 py-0.5 ring-1 ring-inset font-medium {{ $deltaClass($customersDelta) }}">
                    {{ $formatDelta($customersDelta) }}
                </span>
                <span class="text-slate-500">vs previous period</span>
            </div>
        </div>
    </section>

    {{-- Revenue trend + Order status --}}
    <section class="grid gap-4 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] mb-8">
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Revenue trend</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daily revenue and order volume over the <span class="dashboard-period-inline">{{ $periodLabel }}</span></p>
                </div>
                <div class="flex items-center gap-3 text-[11px] text-slate-600">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full" style="background-color: #0ea5e9;"></span>
                        Revenue
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full" style="background-color: #94a3b8;"></span>
                        Orders
                    </span>
                </div>
            </div>
            <div class="relative h-64 sm:h-72">
                <canvas id="dashboard_revenue_chart" aria-label="Revenue trend chart"></canvas>
                @if (empty($revenueChartPayload['labels']))
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
                        <p class="text-sm font-medium text-slate-600">No revenue data yet</p>
                        <p class="text-xs text-slate-500 mt-1">Once orders come in, the trend will appear here.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Order status</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Breakdown across all orders</p>
                </div>
                @if ($needsActionOrders > 0)
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium text-amber-900 bg-amber-100 ring-1 ring-inset ring-amber-600/15">
                        {{ $needsActionOrders }} need action
                    </span>
                @endif
            </div>

            <div class="flex flex-col items-center">
                <div class="relative w-full max-w-[10rem] aspect-square">
                    <canvas id="dashboard_orders_chart" aria-label="Order status distribution"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <p class="text-lg font-semibold text-slate-900 tabular-nums">{{ number_format($totalOrdersInStatuses) }}</p>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">Total</p>
                    </div>
                </div>
            </div>

            <ul class="mt-4 space-y-1.5">
                @foreach (OrderStatusConstant::ORDER_STATUSES as $status)
                    @php
                        $count = $orderStatusCounts[$status] ?? 0;
                        $pct = $totalOrdersInStatuses > 0 ? round(($count / $totalOrdersInStatuses) * 100) : 0;
                    @endphp
                    <li class="flex items-center gap-2 text-xs">
                        <span class="h-2 w-2 rounded-full shrink-0" style="background-color: {{ $statusChartColors[$status] }}"></span>
                        <span class="text-slate-700 flex-1 truncate">{{ $status }}</span>
                        <span class="font-medium text-slate-900 tabular-nums">{{ $count }}</span>
                        <span class="text-slate-400 tabular-nums w-9 text-right">{{ $pct }}%</span>
                    </li>
                @endforeach
            </ul>

            <a href="{{ route('admin.orders') }}" class="mt-4 inline-flex items-center text-xs font-medium text-sky-600 hover:text-sky-700">
                Manage orders
                <svg class="ml-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </section>

    {{-- Inventory health --}}
    <section class="mb-8" aria-labelledby="dashboard-inventory-heading">
        <div class="flex items-end justify-between mb-3">
            <div>
                <h2 id="dashboard-inventory-heading" class="text-base font-semibold text-slate-900">Inventory health</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Items at or below
                    <strong class="text-slate-700">{{ $lowStockThreshold }}</strong>
                    units are flagged as low stock.
                </p>
            </div>
            <a href="{{ route('admin.products') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">
                View all products →
            </a>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,16rem)_minmax(0,1fr)]">
            <div class="rounded-xl border border-slate-200 bg-white p-5 flex flex-col items-center">
                <div class="relative w-full max-w-[11rem] aspect-square">
                    <canvas id="dashboard_inventory_chart" aria-label="Inventory health distribution"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <p class="text-lg font-semibold text-slate-900 tabular-nums">{{ number_format($totalProducts) }}</p>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">Products</p>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap justify-center gap-1.5 text-[11px] text-slate-500">
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-800 px-2 py-0.5">{{ count($activeProducts) }} active</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-700 px-2 py-0.5">{{ max($totalProducts - count($activeProducts), 0) }} inactive</span>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                @foreach (ProductStockConstant::PRODUCT_STOCK_STATES as $state)
                    @php
                        $count = $inventoryCounts[$state] ?? 0;
                        $percent = $totalInventoryItems > 0 ? round(($count / $totalInventoryItems) * 100) : 0;
                        $label = ProductStockConstant::PRODUCT_STOCK_STATE_LABELS[$state];
                    @endphp
                    <a href="{{ route('admin.products') }}"
                       class="rounded-xl border border-slate-200 bg-white p-4 transition-all hover:border-slate-300 hover:shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $stockChartColors[$state] }}"></span>
                            <span class="text-xs font-medium text-slate-600">{{ $label }}</span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 tabular-nums">{{ number_format($count) }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $percent }}% of catalog</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Low stock + Top products --}}
    <section class="grid gap-4 lg:grid-cols-2 mb-8">
        {{-- Needs attention --}}
        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-start justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.732 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                            </svg>
                        </span>
                        Needs attention
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $needsAttentionCount }} {{ $needsAttentionCount === 1 ? 'product' : 'products' }} low or out of stock
                    </p>
                </div>
                <a href="{{ route('admin.products') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700 shrink-0">
                    See all →
                </a>
            </div>

            @if ($lowStockProducts->isEmpty())
                <div class="px-5 py-10 text-center">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 mb-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                    </span>
                    <p class="text-sm font-medium text-slate-700">All products are well stocked</p>
                    <p class="text-xs text-slate-500 mt-1">No items below the threshold of {{ $lowStockThreshold }} units.</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($lowStockProducts as $product)
                        @php
                            $stockState = $resolveStockState($product->stock);
                            $stockQty = (int) $product->stock;
                            $categoryName = $product->productCategory?->name ?? 'Uncategorized';
                        @endphp
                        <li class="px-5 py-3 flex items-center gap-3 hover:bg-slate-50 transition-colors">
                            @if (!empty($product->image))
                                <img src="{{ asset('storage/' . ltrim($product->image, '/')) }}" alt="{{ $product->name }}"
                                     class="h-10 w-10 rounded object-cover shrink-0">
                            @else
                                <div class="h-10 w-10 rounded bg-slate-100 flex items-center justify-center shrink-0">
                                    <span class="text-[10px] text-slate-500">No img</span>
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}"
                                   class="block text-sm font-medium text-slate-900 hover:text-sky-600 truncate">
                                    {{ $product->name }}
                                </a>
                                <p class="text-[11px] text-slate-500 truncate">
                                    <span class="font-mono">{{ $product->sku ?? '—' }}</span>
                                    <span class="mx-1 text-slate-300">·</span>
                                    {{ $categoryName }}
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $stockBadgeClasses[$stockState] }}">
                                    {{ ProductStockConstant::PRODUCT_STOCK_STATE_LABELS[$stockState] }}
                                </span>
                                <p class="text-[11px] text-slate-500 mt-0.5 tabular-nums">
                                    {{ $stockQty }} {{ $stockQty === 1 ? 'unit' : 'units' }} left
                                </p>
                            </div>

                            <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}"
                               class="ml-1 text-xs font-medium text-sky-600 hover:text-sky-700 shrink-0">
                                Restock
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Top selling --}}
        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-start justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-sky-100 text-sky-700">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 11.48Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z"/>
                            </svg>
                        </span>
                        Top selling products
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Best performers for the <span class="dashboard-period-inline">{{ $periodLabel }}</span></p>
                </div>
                <a href="{{ route('admin.products') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700 shrink-0">
                    See all →
                </a>
            </div>

            @if ($topProducts->isEmpty())
                <div class="px-5 py-10 text-center">
                    <p class="text-sm font-medium text-slate-700">No sales data yet</p>
                    <p class="text-xs text-slate-500 mt-1">Top sellers will appear here once orders are placed.</p>
                </div>
            @else
                @php
                    $maxRevenue = max($topProducts->max('revenue') ?? 0, 1);
                @endphp
                <ul class="divide-y divide-slate-100">
                    @foreach ($topProducts as $i => $product)
                        @php
                            $revenue = (float) ($product->revenue ?? 0);
                            $units = (int) ($product->units_sold ?? 0);
                            $pct = $maxRevenue > 0 ? round(($revenue / $maxRevenue) * 100) : 0;
                        @endphp
                        <li class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-[11px] font-semibold text-slate-600 tabular-nums shrink-0">
                                    {{ $i + 1 }}
                                </span>

                                @if (!empty($product->image))
                                    <img src="{{ asset('storage/' . ltrim($product->image, '/')) }}" alt="{{ $product->name }}"
                                         class="h-9 w-9 rounded object-cover shrink-0">
                                @else
                                    <div class="h-9 w-9 rounded bg-slate-100 flex items-center justify-center shrink-0">
                                        <span class="text-[10px] text-slate-500">No img</span>
                                    </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    @if (!empty($product->id))
                                        <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}"
                                           class="block text-sm font-medium text-slate-900 hover:text-sky-600 truncate">
                                            {{ $product->name }}
                                        </a>
                                    @else
                                        <span class="block text-sm font-medium text-slate-900 truncate">{{ $product->name }}</span>
                                    @endif
                                    <p class="text-[11px] text-slate-500 tabular-nums">{{ $units }} {{ $units === 1 ? 'unit' : 'units' }} sold</p>
                                </div>

                                <p class="text-sm font-semibold text-slate-900 tabular-nums shrink-0">
                                    ${{ number_format($revenue, 2) }}
                                </p>
                            </div>
                            <div class="mt-2 ml-9 h-1 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full" style="width: {{ $pct }}%; background-color: var(--color-accent);"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    {{-- Recent orders --}}
    <section class="mb-8" aria-labelledby="dashboard-recent-orders-heading">
        <div class="flex items-end justify-between mb-3">
            <div>
                <h2 id="dashboard-recent-orders-heading" class="text-base font-semibold text-slate-900">Recent orders</h2>
                <p class="text-xs text-slate-500 mt-0.5">Latest activity from your store</p>
            </div>
            <a href="{{ route('admin.orders') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">
                View all orders →
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
                        <tr class="text-left">
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3 hidden sm:table-cell">Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($recentOrders as $order)
                            @php
                                $status = $order->status;
                                $customerName = trim(($order->customer_first_name ?? '') . ' ' . ($order->customer_last_name ?? ''));
                                $customerName = $customerName !== '' ? $customerName : 'Guest';
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('admin.order.view', $order->id) }}'">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs font-semibold text-slate-900">#{{ $order->id }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-slate-900">{{ $customerName }}</span>
                                    @if (!empty($order->customer_email))
                                        <span class="block text-[11px] text-slate-500 truncate max-w-[14rem]">{{ $order->customer_email }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600 hidden sm:table-cell">
                                    {{ optional($order->created_at)->format('M j, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $statusBadgeClasses[$status] ?? 'text-slate-800 bg-slate-100 ring-1 ring-inset ring-slate-400/20' }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums font-medium text-slate-900">
                                    ${{ number_format((float) $order->total_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-600">
                                    No orders yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Chart data payloads --}}
    <script type="application/json" id="dashboard_revenue_chart_data">@json($revenueChartPayload)</script>
    <script type="application/json" id="dashboard_orders_chart_data">@json($orderStatusChartPayload)</script>
    <script type="application/json" id="dashboard_inventory_chart_data">@json($inventoryChartPayload)</script>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var revenuePayload = JSON.parse($('#dashboard_revenue_chart_data').text());
            var ordersPayload = JSON.parse($('#dashboard_orders_chart_data').text());
            var inventoryPayload = JSON.parse($('#dashboard_inventory_chart_data').text());

            function initRevenueChart() {
                var ctx = document.getElementById('dashboard_revenue_chart');
                if (!ctx || typeof Chart === 'undefined') return;
                if (!revenuePayload.labels || !revenuePayload.labels.length) return;

                var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 260);
                gradient.addColorStop(0, 'rgba(14, 165, 233, 0.25)');
                gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');

                new Chart(ctx, {
                    data: {
                        labels: revenuePayload.labels,
                        datasets: [
                            {
                                type: 'line',
                                label: 'Revenue',
                                data: revenuePayload.revenue,
                                borderColor: '#0ea5e9',
                                backgroundColor: gradient,
                                borderWidth: 2,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointHoverBackgroundColor: '#0ea5e9',
                                tension: 0.35,
                                fill: true,
                                yAxisID: 'y',
                                order: 1
                            },
                            {
                                type: 'bar',
                                label: 'Orders',
                                data: revenuePayload.orders,
                                backgroundColor: 'rgba(148, 163, 184, 0.35)',
                                borderRadius: 4,
                                barPercentage: 0.55,
                                categoryPercentage: 0.75,
                                yAxisID: 'y1',
                                order: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 10,
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataset.label === 'Revenue') {
                                            return ' Revenue: $' + Number(context.parsed.y || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                        return ' Orders: ' + Number(context.parsed.y || 0);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#64748b', font: { size: 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 }
                            },
                            y: {
                                position: 'left',
                                beginAtZero: true,
                                grid: { color: 'rgba(148, 163, 184, 0.15)' },
                                ticks: {
                                    color: '#64748b',
                                    font: { size: 10 },
                                    callback: function(value) {
                                        if (value >= 1000) return '$' + (value / 1000).toFixed(1) + 'k';
                                        return '$' + value;
                                    }
                                }
                            },
                            y1: {
                                position: 'right',
                                beginAtZero: true,
                                grid: { display: false },
                                ticks: { color: '#94a3b8', font: { size: 10 }, precision: 0 }
                            }
                        }
                    }
                });
            }

            function initDoughnut(canvasId, payload) {
                var ctx = document.getElementById(canvasId);
                if (!ctx || typeof Chart === 'undefined') return;

                var counts = payload.counts || [];
                var hasData = counts.reduce(function(s, v) { return s + Number(v || 0); }, 0) > 0;

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: payload.labels || [],
                        datasets: [{
                            data: hasData ? counts : counts.map(function() { return 1; }),
                            backgroundColor: hasData ? payload.colors : payload.colors.map(function() { return '#e2e8f0'; }),
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: hasData ? 6 : 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                enabled: hasData,
                                backgroundColor: '#0f172a',
                                padding: 10,
                                callbacks: {
                                    label: function(context) {
                                        var total = (payload.counts || []).reduce(function(a, b) { return a + Number(b || 0); }, 0);
                                        var value = Number((payload.counts || [])[context.dataIndex] || 0);
                                        var pct = total ? Math.round((value / total) * 100) : 0;
                                        return ' ' + context.label + ': ' + value + ' (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            initRevenueChart();
            initDoughnut('dashboard_orders_chart', ordersPayload);
            initDoughnut('dashboard_inventory_chart', inventoryPayload);

            $('#dashboard_period').on('change', function() {
                var url = new URL(window.location.href);
                url.searchParams.set('period', $(this).val());
                window.location.href = url.toString();
            });
        });
    </script>
@endsection
