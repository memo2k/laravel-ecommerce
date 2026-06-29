@extends('pages.admin.layout')

@section('title', 'Orders – ShopDemo Admin')

@section('content')
    @php
        use App\Constants\OrderStatusConstant;

        $statusBadgeClasses = [
            OrderStatusConstant::DELIVERED => 'text-emerald-900 bg-emerald-100 ring-1 ring-inset ring-emerald-600/15',
            OrderStatusConstant::SHIPPED => 'text-sky-900 bg-sky-100 ring-1 ring-inset ring-sky-600/15',
            OrderStatusConstant::PROCESSING => 'text-sky-900 bg-sky-100 ring-1 ring-inset ring-sky-600/15',
            OrderStatusConstant::PENDING => 'text-amber-900 bg-amber-100 ring-1 ring-inset ring-amber-600/15',
            OrderStatusConstant::UNPAID => 'text-rose-900 bg-rose-100 ring-1 ring-inset ring-rose-600/15',
            OrderStatusConstant::CANCELLED => 'text-rose-900 bg-rose-100 ring-1 ring-inset ring-rose-600/15',
        ];

        $selectClass = 'peer h-9 w-full sm:w-auto appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs font-medium text-slate-700 hover:border-slate-300 hover:bg-slate-50 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 transition-colors cursor-pointer';

        $hasFilters = $search || $status || $period || $payment;

        $sortUrl = function (string $column) use ($sort, $direction) {
            $nextDirection = ($sort === $column && $direction === 'desc') ? 'asc' : 'desc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null]);
        };

        $sortIndicator = function (string $column) use ($sort, $direction) {
            if ($sort !== $column) {
                return '↕';
            }
            return $direction === 'asc' ? '↑' : '↓';
        };
    @endphp

    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Orders</h1>
            <p class="text-sm text-slate-600 mt-1">Search, filter, and manage store orders.</p>
        </div>
    </header>

    <section class="grid gap-3 sm:grid-cols-3 mb-8" aria-label="Order statistics">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Total orders</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">{{ number_format($totalOrders) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Revenue</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">${{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50/80 p-4">
            <p class="text-[11px] font-medium uppercase tracking-wide text-amber-800">Needs attention</p>
            <p class="mt-2 text-2xl font-semibold text-amber-900 tabular-nums">{{ number_format($needsActionCount) }}</p>
            <p class="mt-1 text-[11px] text-amber-700">pending, processing, or unpaid</p>
        </div>
    </section>

    <section aria-labelledby="orders-list-heading">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between mb-4">
            <div>
                <h2 id="orders-list-heading" class="text-base font-semibold text-slate-900">All orders</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if ($orders->total() > 0)
                        Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}
                    @else
                        No matching orders
                    @endif
                </p>
            </div>
            @if ($hasFilters)
                <a href="{{ route('admin.orders') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">
                    Clear filters
                </a>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
            <form method="GET" action="{{ route('admin.orders') }}" class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">

                <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:flex-wrap">
                    <div class="flex flex-1 min-w-[12rem] lg:max-w-md gap-2">
                        <div class="relative flex-1 min-w-0">
                            <label class="sr-only" for="orders_search">Search orders</label>
                            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            <input type="search"
                                   name="search"
                                   id="orders_search"
                                   value="{{ $search }}"
                                   placeholder="Order #, customer, or email…"
                                   class="h-9 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-xs text-slate-900 placeholder:text-slate-400 hover:border-slate-300 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 transition-colors">
                        </div>
                        <button type="submit"
                                class="inline-flex h-9 shrink-0 items-center justify-center rounded-lg px-4 text-xs font-medium text-white shadow-sm hover:opacity-90 transition"
                                style="background-color: var(--color-accent);">
                            Search
                        </button>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 flex-wrap sm:items-center">
                        <div class="relative">
                            <label for="orders_status_filter" class="sr-only">Order status</label>
                            <select name="status" id="orders_status_filter" class="{{ $selectClass }}" onchange="this.form.submit()">
                                <option value="">All statuses</option>
                                @foreach (OrderStatusConstant::ORDER_STATUSES as $orderStatus)
                                    <option value="{{ $orderStatus }}" @selected($status === $orderStatus)>{{ $orderStatus }}</option>
                                @endforeach
                            </select>
                            <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 peer-focus:text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        <div class="relative">
                            <label for="orders_period_filter" class="sr-only">Date range</label>
                            <select name="period" id="orders_period_filter" class="{{ $selectClass }}" onchange="this.form.submit()">
                                <option value="">All time</option>
                                <option value="7" @selected($period === '7')>Last 7 days</option>
                                <option value="30" @selected($period === '30')>Last 30 days</option>
                                <option value="90" @selected($period === '90')>Last 90 days</option>
                            </select>
                            <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 peer-focus:text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        @if (count($paymentMethods) > 1)
                            <div class="relative">
                                <label for="orders_payment_filter" class="sr-only">Payment method</label>
                                <select name="payment" id="orders_payment_filter" class="{{ $selectClass }}" onchange="this.form.submit()">
                                    <option value="">All payments</option>
                                    @foreach ($paymentMethods as $method => $count)
                                        <option value="{{ strtolower($method) }}" @selected($payment === strtolower($method))>{{ $method }} ({{ $count }})</option>
                                    @endforeach
                                </select>
                                <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 peer-focus:text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
                        <tr class="text-left">
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3 hidden sm:table-cell">
                                <a href="{{ $sortUrl('date') }}" class="inline-flex items-center gap-1 hover:text-slate-900">
                                    Date <span class="text-[10px] {{ $sort === 'date' ? '' : 'opacity-40' }}">{{ $sortIndicator('date') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-3 text-right">
                                <a href="{{ $sortUrl('total') }}" class="inline-flex items-center gap-1 hover:text-slate-900 ml-auto">
                                    Total <span class="text-[10px] {{ $sort === 'total' ? '' : 'opacity-40' }}">{{ $sortIndicator('total') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($orders as $order)
                            @php
                                $customerName = trim($order->customer_first_name . ' ' . $order->customer_last_name);
                                $customerName = $customerName !== '' ? $customerName : 'Guest';
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('admin.order.view', $order->id) }}'">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs font-semibold text-slate-900">#{{ $order->id }}</span>
                                    <span class="block sm:hidden text-[11px] text-slate-500 mt-0.5">{{ $order->created_at->format('M j, Y') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-slate-900">{{ $customerName }}</span>
                                    @if ($order->customer_email)
                                        <span class="block text-[11px] text-slate-500 truncate max-w-[14rem]">{{ $order->customer_email }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600 hidden sm:table-cell">{{ $order->created_at->format('M j, Y') }}</td>
                                <td class="px-4 py-3 text-right tabular-nums font-medium text-slate-900">
                                    ${{ number_format((float) $order->total_amount, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $statusBadgeClasses[$order->status] ?? 'text-slate-800 bg-slate-100 ring-1 ring-inset ring-slate-400/20' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-600">
                                    @if ($hasFilters)
                                        No orders match your filters.
                                    @else
                                        No orders yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('pages.admin.components.pagination', ['paginator' => $orders])
        </div>
    </section>
@endsection
