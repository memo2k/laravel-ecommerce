@extends('pages.admin.layout')

@section('title', 'Products – ShopDemo Admin')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endsection

@section('content')
    @php
        use App\Constants\ProductStockConstant;

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

        $totalProducts = $products->count();

        $inventoryChartData = [
            'labels' => array_values(ProductStockConstant::PRODUCT_STOCK_STATE_LABELS),
            'counts' => [
                $inventoryCounts[ProductStockConstant::IN_STOCK],
                $inventoryCounts[ProductStockConstant::LOW_STOCK],
                $inventoryCounts[ProductStockConstant::OUT_OF_STOCK],
            ],
            'states' => ProductStockConstant::PRODUCT_STOCK_STATES,
            'colors' => array_values($stockChartColors),
        ];
    @endphp

    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Products</h1>
            <p class="text-sm text-slate-600 mt-1 max-w-lg">
                Manage your catalog, monitor inventory health, and filter products below.
            </p>
        </div>
        <a href="{{ route('admin.product.edit') }}"
           class="inline-flex shrink-0 items-center justify-center rounded-md px-4 py-2 text-xs font-medium text-white shadow-sm hover:opacity-90 transition"
           style="background-color: var(--color-accent);">
            + Add product
        </a>
    </header>

    {{-- Inventory overview --}}
    <section class="mb-8" aria-labelledby="inventory-overview-heading">
        <h2 id="inventory-overview-heading" class="text-base font-semibold text-slate-900 mb-1">Inventory overview</h2>
        <p class="text-xs text-slate-500 mb-4">Click a segment or summary card to filter the product list</p>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,16rem)_minmax(0,1fr)]">
            <div class="rounded-xl border border-slate-200 bg-white p-5 flex flex-col items-center">
                <div class="relative w-full max-w-[11rem] aspect-square">
                    <canvas id="products_inventory_chart" aria-label="Inventory status distribution"></canvas>
                </div>
                <p class="mt-3 text-center text-xs text-slate-500">
                    <span class="font-semibold text-slate-800" id="products_chart_total">{{ $totalProducts }}</span> products total
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                @foreach (ProductStockConstant::PRODUCT_STOCK_STATES as $state)
                    @php
                        $count = $inventoryCounts[$state];
                        $percent = $totalProducts > 0 ? round(($count / $totalProducts) * 100) : 0;
                    @endphp
                    <button type="button"
                            class="products-inventory-filter-btn rounded-xl border border-slate-200 bg-white p-4 text-left transition-all hover:border-slate-300 hover:shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1"
                            style="--tw-ring-color: var(--color-accent);"
                            data-stock-filter="{{ $state }}"
                            aria-pressed="false">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $stockChartColors[$state] }}"></span>
                            <span class="text-xs font-medium text-slate-600">{{ ProductStockConstant::PRODUCT_STOCK_STATE_LABELS[$state] }}</span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 tabular-nums">{{ $count }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $percent }}% of catalog</p>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="mt-3 flex flex-wrap gap-2 text-[11px] text-slate-500">
            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5">
                Low stock threshold: <strong class="text-slate-700">{{ $lowStockThreshold }}</strong> units
            </span>
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-800 px-2 py-0.5">{{ $activeCount }} active</span>
            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-700 px-2 py-0.5">{{ $inactiveCount }} inactive</span>
        </div>
    </section>

    {{-- Product list --}}
    <section aria-labelledby="products-list-heading">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between mb-4">
            <div>
                <h2 id="products-list-heading" class="text-base font-semibold text-slate-900">Product list</h2>
                <p class="text-xs text-slate-500 mt-0.5" id="products_result_count" aria-live="polite">
                    Showing {{ $totalProducts }} of {{ $totalProducts }} products
                </p>
            </div>
            <button type="button" id="products_reset_filters"
                    class="hidden text-xs font-medium text-sky-600 hover:text-sky-700 sm:ml-auto">
                Reset all filters
            </button>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
            {{-- Toolbar --}}
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:flex-wrap">
                    <div class="relative flex-1 min-w-[12rem] lg:max-w-xs">
                        <label class="sr-only" for="products_search">Search products</label>
                        <svg class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="search"
                               id="products_search"
                               placeholder="Search by name or SKU…"
                               class="w-full rounded-md border border-slate-300 bg-white pl-8 pr-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 flex-wrap">
                        <div>
                            <label for="products_stock_filter" class="sr-only">Inventory status</label>
                            <select id="products_stock_filter"
                                    class="w-full sm:w-auto rounded-md border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                <option value="">All inventory</option>
                                @foreach (ProductStockConstant::PRODUCT_STOCK_STATES as $state)
                                    <option value="{{ $state }}">{{ ProductStockConstant::PRODUCT_STOCK_STATE_LABELS[$state] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="products_status_filter" class="sr-only">Product status</label>
                            <select id="products_status_filter"
                                    class="w-full sm:w-auto rounded-md border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                <option value="">All statuses</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label for="products_category_filter" class="sr-only">Category</label>
                            <select id="products_category_filter"
                                    class="w-full sm:w-auto rounded-md border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                <option value="">All categories</option>
                                @foreach ($categories as $name => $count)
                                    <option value="{{ $name }}">{{ $name }} ({{ $count }})</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" id="products_clear_filters"
                                class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto" id="products_table_wrap">
                <table class="min-w-full" id="products_table">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
                    <tr class="text-left">
                        <th class="px-4 py-3">Image</th>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3 hidden md:table-cell">Category</th>
                        <th class="px-4 py-3">Price</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Inventory</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" id="products_table_body">
                    @forelse ($products as $product)
                        @php
                            $stockState = $resolveStockState($product);
                            $categoryName = $product->productCategory?->name ?? 'Uncategorized';
                            $catalogStatus = $product->is_active ? 'active' : 'inactive';
                        @endphp
                        <tr class="products-table-row hover:bg-slate-50 transition-colors"
                            data-name="{{ strtolower($product->name) }}"
                            data-sku="{{ strtolower($product->sku) }}"
                            data-category="{{ strtolower($categoryName) }}"
                            data-category-label="{{ $categoryName }}"
                            data-stock-state="{{ $stockState }}"
                            data-catalog-status="{{ $catalogStatus }}"
                            data-stock-qty="{{ (int) $product->stock }}">
                            <td class="px-4 py-3">
                                <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="block">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . ltrim($product->image, '/')) }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded">
                                    @else
                                        <div class="w-10 h-10 bg-slate-100 rounded flex items-center justify-center">
                                            <span class="text-[10px] text-slate-500">No img</span>
                                        </div>
                                    @endif
                                </a>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-900">
                                <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="hover:text-sky-600">
                                    {{ $product->sku }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="font-medium text-slate-900 hover:text-sky-600">
                                    {{ $product->name }}
                                </a>
                                <span class="block md:hidden text-[11px] text-slate-500 mt-0.5">{{ $categoryName }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 hidden md:table-cell">{{ $categoryName }}</td>
                            <td class="px-4 py-3 text-slate-900 tabular-nums">${{ $product->price }}</td>
                            <td class="px-4 py-3">
                                @if ($product->is_active)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium text-emerald-900 bg-emerald-100 ring-1 ring-inset ring-emerald-600/15">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium text-slate-700 bg-slate-100 ring-1 ring-inset ring-slate-400/20">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $stockBadgeClasses[$stockState] }}">
                                        {{ ProductStockConstant::PRODUCT_STOCK_STATE_LABELS[$stockState] }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 tabular-nums">
                                        {{ (int) $product->stock }} {{ (int) $product->stock === 1 ? 'unit' : 'units' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2 text-xs">
                                    <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}" class="text-sky-600 hover:text-sky-700 font-medium">
                                        Edit
                                    </a>
                                    <button type="button" class="text-rose-600 hover:text-rose-700 delete-product-button" data-id="{{ $product->id }}">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="products_no_data_row">
                            <td colspan="8" class="px-4 py-8 text-center text-slate-600">
                                No products in catalog yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div id="products_empty_filtered" class="hidden px-4 py-10 text-center border-t border-slate-100">
                <p class="text-sm font-medium text-slate-700">No products match your filters</p>
                <p class="text-xs text-slate-500 mt-1">Try adjusting search or filter options.</p>
                <button type="button" class="products-reset-empty mt-3 text-xs font-medium text-sky-600 hover:text-sky-700">
                    Reset filters
                </button>
            </div>
        </div>
    </section>

    <script type="application/json" id="products_inventory_chart_data">@json($inventoryChartData)</script>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var accentColor = getComputedStyle(document.body).getPropertyValue('--color-accent').trim() || '#3b82f6';
            var currentStockFilter = '';
            var inventoryChart = null;

            var chartPayload = JSON.parse($('#products_inventory_chart_data').text());

            function initInventoryChart() {
                var ctx = document.getElementById('products_inventory_chart');
                if (!ctx || typeof Chart === 'undefined') return;

                inventoryChart = new Chart(ctx, {
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
                            var state = chartPayload.states[elements[0].index];
                            setStockFilter(state === currentStockFilter ? '' : state);
                        }
                    }
                });
            }

            function hasActiveFilters() {
                return $('#products_search').val().trim() !== ''
                    || $('#products_stock_filter').val() !== ''
                    || $('#products_status_filter').val() !== ''
                    || $('#products_category_filter').val() !== '';
            }

            function updateResetVisibility() {
                var active = hasActiveFilters() || currentStockFilter !== '';
                $('#products_reset_filters').toggleClass('hidden', !active);
            }

            function setInventoryCardActive(state) {
                $('.products-inventory-filter-btn').each(function() {
                    var $btn = $(this);
                    var isActive = state && $btn.data('stock-filter') === state;
                    $btn.attr('aria-pressed', isActive ? 'true' : 'false');
                    $btn.toggleClass('ring-2 ring-offset-1 border-sky-300 bg-sky-50/50 shadow-sm', isActive)
                        .toggleClass('border-slate-200', !isActive);
                });
            }

            function setStockFilter(state) {
                currentStockFilter = state || '';
                $('#products_stock_filter').val(currentStockFilter);
                setInventoryCardActive(currentStockFilter);
                applyFilters();
            }

            function resetFilters() {
                currentStockFilter = '';
                $('#products_search').val('');
                $('#products_stock_filter').val('');
                $('#products_status_filter').val('');
                $('#products_category_filter').val('');
                setInventoryCardActive('');
                applyFilters();
            }

            function applyFilters() {
                var query = ($('#products_search').val() || '').toLowerCase().trim();
                var stockState = currentStockFilter || $('#products_stock_filter').val();
                var catalogStatus = $('#products_status_filter').val();
                var category = ($('#products_category_filter').val() || '').toLowerCase();
                var visible = 0;
                var total = $('.products-table-row').length;

                if (stockState && stockState !== currentStockFilter) {
                    currentStockFilter = stockState;
                    setInventoryCardActive(currentStockFilter);
                }

                $('.products-table-row').each(function() {
                    var $row = $(this);
                    var matchesSearch = !query
                        || $row.data('name').indexOf(query) !== -1
                        || $row.data('sku').indexOf(query) !== -1
                        || String($row.data('categoryLabel') || '').toLowerCase().indexOf(query) !== -1;
                    var matchesStock = !stockState || $row.data('stock-state') === stockState;
                    var matchesStatus = !catalogStatus || $row.data('catalog-status') === catalogStatus;
                    var matchesCategory = !category || $row.data('category') === category;

                    if (matchesSearch && matchesStock && matchesStatus && matchesCategory) {
                        $row.removeClass('hidden');
                        visible++;
                    } else {
                        $row.addClass('hidden');
                    }
                });

                $('#products_result_count').text('Showing ' + visible + ' of ' + total + ' products');

                var hasRows = total > 0;
                var hasResults = visible > 0;
                $('#products_empty_filtered').toggleClass('hidden', !hasRows || hasResults);
                $('#products_table_wrap').toggleClass('hidden', hasRows && !hasResults);

                updateResetVisibility();
            }

            initInventoryChart();

            $(document).on('click', '.products-inventory-filter-btn', function() {
                var state = $(this).data('stock-filter');
                setStockFilter(state === currentStockFilter ? '' : state);
            });

            $('#products_search').on('input', applyFilters);
            $('#products_stock_filter').on('change', function() {
                setStockFilter($(this).val());
            });
            $('#products_status_filter, #products_category_filter').on('change', applyFilters);

            $('#products_reset_filters, #products_clear_filters, .products-reset-empty').on('click', resetFilters);

            $('.delete-product-button').on('click', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this product?')) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        url: '{{ route('admin.product.delete') }}',
                        type: 'DELETE',
                        data: { product_id: id },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error deleting product: ' + error);
                        }
                    });
                }
            });

            applyFilters();
        });
    </script>
@endsection
