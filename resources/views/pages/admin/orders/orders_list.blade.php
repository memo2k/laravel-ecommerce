@extends('pages.admin.layout')

@section('title', 'Orders – ShopDemo Admin')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endsection

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

        $statusChartColors = [
            OrderStatusConstant::UNPAID => '#f43f5e',
            OrderStatusConstant::PENDING => '#f59e0b',
            OrderStatusConstant::PROCESSING => '#0ea5e9',
            OrderStatusConstant::SHIPPED => '#38bdf8',
            OrderStatusConstant::DELIVERED => '#10b981',
            OrderStatusConstant::CANCELLED => '#fb7185',
        ];

        $ordersChartData = [
            'labels' => OrderStatusConstant::ORDER_STATUSES,
            'counts' => array_map(fn ($status) => $statusCounts[$status] ?? 0, OrderStatusConstant::ORDER_STATUSES),
            'statuses' => OrderStatusConstant::ORDER_STATUSES,
            'colors' => array_map(fn ($status) => $statusChartColors[$status], OrderStatusConstant::ORDER_STATUSES),
        ];
    @endphp

    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Orders</h1>
            <p class="text-sm text-slate-600 mt-1 max-w-lg">
                Review fulfillment status, filter orders, and open any row for full details.
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}#section-orders"
           class="inline-flex shrink-0 items-center text-xs font-medium text-sky-600 hover:text-sky-700">
            View on dashboard
            <svg class="ml-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
    </header>

    {{-- Summary KPIs --}}
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Total orders</p>
            <p class="mt-1 text-xl font-semibold text-slate-900 tabular-nums">{{ number_format($totalOrders) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Revenue</p>
            <p class="mt-1 text-xl font-semibold text-slate-900 tabular-nums">${{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Avg. order value</p>
            <p class="mt-1 text-xl font-semibold text-slate-900 tabular-nums">${{ number_format($avgOrderValue, 2) }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50/80 px-4 py-3">
            <p class="text-[11px] font-medium uppercase tracking-wide text-amber-800">Needs attention</p>
            <p class="mt-1 text-xl font-semibold text-amber-900 tabular-nums">{{ $needsActionCount }}</p>
            <p class="text-[10px] text-amber-700 mt-0.5">Pending, processing, or unpaid</p>
        </div>
    </div>

    {{-- Status overview --}}
    <section class="mb-8" aria-labelledby="orders-overview-heading">
        <h2 id="orders-overview-heading" class="text-base font-semibold text-slate-900 mb-1">Order status overview</h2>
        <p class="text-xs text-slate-500 mb-4">Click a chart segment or status card to filter the list below</p>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,16rem)_minmax(0,1fr)]">
            <div class="rounded-xl border border-slate-200 bg-white p-5 flex flex-col items-center">
                <div class="relative w-full max-w-[11rem] aspect-square">
                    <canvas id="orders_status_chart" aria-label="Order status distribution"></canvas>
                </div>
                <p class="mt-3 text-center text-xs text-slate-500">
                    <span class="font-semibold text-slate-800">{{ $totalOrders }}</span> orders total
                </p>
            </div>

            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (OrderStatusConstant::ORDER_STATUSES as $status)
                    @php
                        $count = $statusCounts[$status] ?? 0;
                        $percent = $totalOrders > 0 ? round(($count / $totalOrders) * 100) : 0;
                    @endphp
                    <button type="button"
                            class="orders-status-filter-btn rounded-xl border border-slate-200 bg-white p-3 text-left transition-all hover:border-slate-300 hover:shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1"
                            style="--tw-ring-color: var(--color-accent);"
                            data-status-filter="{{ $status }}"
                            aria-pressed="false">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="h-2 w-2 rounded-full shrink-0" style="background-color: {{ $statusChartColors[$status] }}"></span>
                            <span class="text-xs font-medium text-slate-700 truncate">{{ $status }}</span>
                        </div>
                        <p class="text-lg font-semibold text-slate-900 tabular-nums">{{ $count }}</p>
                        <p class="text-[10px] text-slate-500">{{ $percent }}%</p>
                    </button>
                @endforeach
            </div>
        </div>

        @if ($totalOrders > 0)
            <div class="mt-4 flex h-2.5 w-full overflow-hidden rounded-full bg-slate-100" role="presentation">
                @foreach (OrderStatusConstant::ORDER_STATUSES as $status)
                    @if (($statusCounts[$status] ?? 0) > 0)
                        <div class="h-full"
                             style="width: {{ round((($statusCounts[$status] ?? 0) / $totalOrders) * 100, 1) }}%; background-color: {{ $statusChartColors[$status] }}"
                             title="{{ $status }}: {{ $statusCounts[$status] }}"></div>
                    @endif
                @endforeach
            </div>
        @endif
    </section>

    {{-- Orders table --}}
    <section aria-labelledby="orders-list-heading">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between mb-4">
            <div>
                <h2 id="orders-list-heading" class="text-base font-semibold text-slate-900">All orders</h2>
                <p class="text-xs text-slate-500 mt-0.5" id="orders_result_count" aria-live="polite">
                    Showing {{ $totalOrders }} of {{ $totalOrders }} orders
                </p>
            </div>
            <button type="button" id="orders_reset_filters"
                    class="hidden text-xs font-medium text-sky-600 hover:text-sky-700">
                Reset all filters
            </button>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:flex-wrap">
                    <div class="relative flex-1 min-w-[12rem] lg:max-w-xs">
                        <label class="sr-only" for="orders_search">Search orders</label>
                        <svg class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="search"
                               id="orders_search"
                               placeholder="Search order #, customer, or email…"
                               class="w-full rounded-md border border-slate-300 bg-white pl-8 pr-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 flex-wrap">
                        <div>
                            <label for="orders_status_filter" class="sr-only">Order status</label>
                            <select id="orders_status_filter"
                                    class="w-full sm:w-auto rounded-md border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                <option value="">All statuses</option>
                                @foreach (OrderStatusConstant::ORDER_STATUSES as $status)
                                    <option value="{{ $status }}">{{ $status }} ({{ $statusCounts[$status] ?? 0 }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="orders_period_filter" class="sr-only">Date range</label>
                            <select id="orders_period_filter"
                                    class="w-full sm:w-auto rounded-md border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                <option value="">All time</option>
                                <option value="7">Last 7 days</option>
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                        </div>

                        @if (count($paymentMethods) > 1)
                            <div>
                                <label for="orders_payment_filter" class="sr-only">Payment method</label>
                                <select id="orders_payment_filter"
                                        class="w-full sm:w-auto rounded-md border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                    <option value="">All payments</option>
                                    @foreach ($paymentMethods as $method => $count)
                                        <option value="{{ strtolower($method) }}">{{ $method }} ({{ $count }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <button type="button" id="orders_clear_filters"
                                class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto" id="orders_table_wrap">
                <table class="min-w-full" id="orders_table">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
                    <tr class="text-left">
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3 hidden sm:table-cell">
                            <button type="button" class="orders-sort-btn inline-flex items-center gap-1 hover:text-slate-900" data-sort="date">
                                Date <span class="orders-sort-indicator text-[10px]" data-sort-col="date">↓</span>
                            </button>
                        </th>
                        <th class="px-4 py-3 text-right">
                            <button type="button" class="orders-sort-btn inline-flex items-center gap-1 hover:text-slate-900 ml-auto" data-sort="total">
                                Total <span class="orders-sort-indicator text-[10px] opacity-40" data-sort-col="total">↕</span>
                            </button>
                        </th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 hidden lg:table-cell">Payment</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" id="orders_table_body">
                    @forelse ($orders as $order)
                        @php
                            $status = $order->status;
                            $customerName = trim($order->customer_first_name . ' ' . $order->customer_last_name);
                            $paymentMethod = $order->payment_method ?: 'Unknown';
                        @endphp
                        <tr class="orders-table-row group hover:bg-slate-50 transition-colors cursor-pointer"
                            data-order-id="{{ $order->id }}"
                            data-customer="{{ strtolower($customerName) }}"
                            data-email="{{ strtolower($order->customer_email ?? '') }}"
                            data-status="{{ $status }}"
                            data-payment="{{ strtolower($paymentMethod) }}"
                            data-total="{{ (float) $order->total_amount }}"
                            data-date="{{ $order->created_at->format('Y-m-d') }}"
                            data-href="{{ route('admin.order.view', $order->id) }}"
                            tabindex="0"
                            role="link"
                            aria-label="View order #{{ $order->id }}">
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
                            <td class="px-4 py-3 text-right tabular-nums">
                                <span class="font-medium text-slate-900">${{ number_format((float) $order->total_amount, 2) }}</span>
                                <span class="block text-[10px] text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity">View →</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $statusBadgeClasses[$status] ?? 'text-slate-800 bg-slate-100 ring-1 ring-inset ring-slate-400/20' }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 hidden lg:table-cell text-xs">{{ $paymentMethod }}</td>
                        </tr>
                    @empty
                        <tr id="orders_no_data_row">
                            <td colspan="6" class="px-4 py-8 text-center text-slate-600">
                                No orders yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div id="orders_empty_filtered" class="hidden px-4 py-10 text-center border-t border-slate-100">
                <p class="text-sm font-medium text-slate-700">No orders match your filters</p>
                <p class="text-xs text-slate-500 mt-1">Try a different search or filter combination.</p>
                <button type="button" class="orders-reset-empty mt-3 text-xs font-medium text-sky-600 hover:text-sky-700">
                    Reset filters
                </button>
            </div>
        </div>
    </section>

    <script type="application/json" id="orders_status_chart_data">@json($ordersChartData)</script>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var currentStatusFilter = '';
            var sortColumn = 'date';
            var sortDirection = 'desc';
            var statusChart = null;

            var chartPayload = JSON.parse($('#orders_status_chart_data').text());

            function initStatusChart() {
                var ctx = document.getElementById('orders_status_chart');
                if (!ctx || typeof Chart === 'undefined') return;

                statusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: chartPayload.labels,
                        datasets: [{
                            data: chartPayload.counts,
                            backgroundColor: chartPayload.colors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '62%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 10,
                                callbacks: {
                                    label: function(context) {
                                        var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                        var value = context.parsed;
                                        var pct = total ? Math.round((value / total) * 100) : 0;
                                        return context.label + ': ' + value + ' (' + pct + '%)';
                                    }
                                }
                            }
                        },
                        onClick: function(evt, elements) {
                            if (!elements.length) return;
                            var status = chartPayload.statuses[elements[0].index];
                            setStatusFilter(status === currentStatusFilter ? '' : status);
                        }
                    }
                });
            }

            function setStatusCardActive(status) {
                $('.orders-status-filter-btn').each(function() {
                    var $btn = $(this);
                    var isActive = status && $btn.data('status-filter') === status;
                    $btn.attr('aria-pressed', isActive ? 'true' : 'false');
                    $btn.toggleClass('ring-2 ring-offset-1 border-sky-300 bg-sky-50/50 shadow-sm', isActive)
                        .toggleClass('border-slate-200', !isActive);
                });
            }

            function hasActiveFilters() {
                return $('#orders_search').val().trim() !== ''
                    || $('#orders_status_filter').val() !== ''
                    || $('#orders_period_filter').val() !== ''
                    || ($('#orders_payment_filter').length && $('#orders_payment_filter').val() !== '');
            }

            function updateResetVisibility() {
                $('#orders_reset_filters').toggleClass('hidden', !hasActiveFilters() && currentStatusFilter === '');
            }

            function orderWithinPeriod(dateStr, days) {
                if (!days) return true;
                var orderDate = new Date(dateStr + 'T00:00:00');
                var cutoff = new Date();
                cutoff.setHours(0, 0, 0, 0);
                cutoff.setDate(cutoff.getDate() - parseInt(days, 10));
                return orderDate >= cutoff;
            }

            function setStatusFilter(status) {
                currentStatusFilter = status || '';
                $('#orders_status_filter').val(currentStatusFilter);
                setStatusCardActive(currentStatusFilter);
                applyFilters();
            }

            function resetFilters() {
                currentStatusFilter = '';
                $('#orders_search').val('');
                $('#orders_status_filter').val('');
                $('#orders_period_filter').val('');
                $('#orders_payment_filter').val('');
                setStatusCardActive('');
                applyFilters();
            }

            function applyFilters() {
                var query = ($('#orders_search').val() || '').toLowerCase().trim();
                var status = currentStatusFilter || $('#orders_status_filter').val();
                var periodDays = $('#orders_period_filter').val();
                var payment = $('#orders_payment_filter').val();
                var visible = 0;
                var total = $('.orders-table-row').length;

                if (status && status !== currentStatusFilter) {
                    currentStatusFilter = status;
                    setStatusCardActive(currentStatusFilter);
                }

                $('.orders-table-row').each(function() {
                    var $row = $(this);
                    var orderId = String($row.data('order-id'));
                    var matchesSearch = !query
                        || orderId.indexOf(query.replace('#', '')) !== -1
                        || $row.data('customer').indexOf(query) !== -1
                        || String($row.data('email')).indexOf(query) !== -1;
                    var matchesStatus = !status || $row.data('status') === status;
                    var matchesPeriod = orderWithinPeriod($row.data('date'), periodDays);
                    var matchesPayment = !payment || $row.data('payment') === payment;

                    if (matchesSearch && matchesStatus && matchesPeriod && matchesPayment) {
                        $row.removeClass('hidden');
                        visible++;
                    } else {
                        $row.addClass('hidden');
                    }
                });

                $('#orders_result_count').text('Showing ' + visible + ' of ' + total + ' orders');

                var hasRows = total > 0;
                var hasResults = visible > 0;
                $('#orders_empty_filtered').toggleClass('hidden', !hasRows || hasResults);
                $('#orders_table_wrap').toggleClass('hidden', hasRows && !hasResults);

                updateResetVisibility();
            }

            function sortOrders(column) {
                if (sortColumn === column) {
                    sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    sortColumn = column;
                    sortDirection = column === 'date' ? 'desc' : 'asc';
                }

                $('.orders-sort-indicator').text('↕').addClass('opacity-40');
                $('.orders-sort-indicator[data-sort-col="' + column + '"]')
                    .removeClass('opacity-40')
                    .text(sortDirection === 'asc' ? '↑' : '↓');

                var $tbody = $('#orders_table_body');
                var rows = $tbody.find('.orders-table-row').get();

                rows.sort(function(a, b) {
                    var aVal = column === 'date' ? $(a).data('date') : parseFloat($(a).data('total'));
                    var bVal = column === 'date' ? $(b).data('date') : parseFloat($(b).data('total'));
                    if (aVal < bVal) return sortDirection === 'asc' ? -1 : 1;
                    if (aVal > bVal) return sortDirection === 'asc' ? 1 : -1;
                    return 0;
                });

                $.each(rows, function(_, row) {
                    $tbody.append(row);
                });
            }

            initStatusChart();

            $(document).on('click', '.orders-status-filter-btn', function() {
                var status = $(this).data('status-filter');
                setStatusFilter(status === currentStatusFilter ? '' : status);
            });

            $('#orders_search').on('input', applyFilters);
            $('#orders_status_filter').on('change', function() {
                setStatusFilter($(this).val());
            });
            $('#orders_period_filter, #orders_payment_filter').on('change', applyFilters);

            $('#orders_reset_filters, #orders_clear_filters, .orders-reset-empty').on('click', resetFilters);

            $(document).on('click', '.orders-sort-btn', function(e) {
                e.stopPropagation();
                sortOrders($(this).data('sort'));
            });

            $(document).on('click', '.orders-table-row', function() {
                window.location = $(this).data('href');
            });

            $(document).on('keydown', '.orders-table-row', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    window.location = $(this).data('href');
                }
            });

            applyFilters();
        });
    </script>
@endsection
