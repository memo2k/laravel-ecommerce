@extends('pages.admin.layout')

@section('title', 'Products – ShopDemo Admin')

@section('content')
    @php
        use App\Constants\ProductStockConstant;

        $stockBadgeClasses = [
            ProductStockConstant::IN_STOCK => 'text-emerald-900 bg-emerald-100 ring-1 ring-inset ring-emerald-600/15',
            ProductStockConstant::LOW_STOCK => 'text-amber-900 bg-amber-100 ring-1 ring-inset ring-amber-600/15',
            ProductStockConstant::OUT_OF_STOCK => 'text-rose-900 bg-rose-100 ring-1 ring-inset ring-rose-600/15',
        ];

        $selectClass = 'peer h-9 w-full sm:w-auto appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs font-medium text-slate-700 hover:border-slate-300 hover:bg-slate-50 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 transition-colors cursor-pointer';

        $hasFilters = $search || $stock || $status || $category;

        $sortUrl = function (string $column) use ($sort, $direction) {
            $nextDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
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
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Products</h1>
            <p class="text-sm text-slate-600 mt-1">Search, filter, and manage your catalog.</p>
        </div>
        <a href="{{ route('admin.product.edit') }}"
           class="inline-flex shrink-0 items-center justify-center rounded-md px-4 py-2 text-xs font-medium text-white shadow-sm hover:opacity-90 transition"
           style="background-color: var(--color-accent);">
            Add product
        </a>
    </header>

    <section class="grid gap-3 sm:grid-cols-3 mb-8" aria-label="Product statistics">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Total products</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">{{ number_format($totalProducts) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Active</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 tabular-nums">{{ number_format($activeCount) }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50/80 p-4">
            <p class="text-[11px] font-medium uppercase tracking-wide text-amber-800">Low stock</p>
            <p class="mt-2 text-2xl font-semibold text-amber-900 tabular-nums">{{ number_format($lowStockCount) }}</p>
            <p class="mt-1 text-[11px] text-amber-700">at or below {{ $lowStockThreshold }} units</p>
        </div>
    </section>

    <section aria-labelledby="products-list-heading">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between mb-4">
            <div>
                <h2 id="products-list-heading" class="text-base font-semibold text-slate-900">All products</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if ($products->total() > 0)
                        Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}
                    @else
                        No matching products
                    @endif
                </p>
            </div>
            @if ($hasFilters)
                <a href="{{ route('admin.products') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">
                    Clear filters
                </a>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
            <form method="GET" action="{{ route('admin.products') }}" class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">

                <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:flex-wrap">
                    <div class="flex flex-1 min-w-[12rem] lg:max-w-md gap-2">
                        <div class="relative flex-1 min-w-0">
                            <label class="sr-only" for="products_search">Search products</label>
                            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            <input type="search"
                                   name="search"
                                   id="products_search"
                                   value="{{ $search }}"
                                   placeholder="Name or SKU…"
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
                            <label for="products_stock_filter" class="sr-only">Inventory status</label>
                            <select name="stock" id="products_stock_filter" class="{{ $selectClass }}" onchange="this.form.submit()">
                                <option value="">All inventory</option>
                                @foreach (ProductStockConstant::PRODUCT_STOCK_STATES as $state)
                                    <option value="{{ $state }}" @selected($stock === $state)>{{ ProductStockConstant::PRODUCT_STOCK_STATE_LABELS[$state] }}</option>
                                @endforeach
                            </select>
                            <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 peer-focus:text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        <div class="relative">
                            <label for="products_status_filter" class="sr-only">Product status</label>
                            <select name="status" id="products_status_filter" class="{{ $selectClass }}" onchange="this.form.submit()">
                                <option value="">All statuses</option>
                                <option value="active" @selected($status === 'active')>Active</option>
                                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                            </select>
                            <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 peer-focus:text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        @if (count($categories) > 0)
                            <div class="relative">
                                <label for="products_category_filter" class="sr-only">Category</label>
                                <select name="category" id="products_category_filter" class="{{ $selectClass }}" onchange="this.form.submit()">
                                    <option value="">All categories</option>
                                    @foreach ($categories as $name => $count)
                                        <option value="{{ $name }}" @selected($category === $name)>{{ $name }} ({{ $count }})</option>
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
                            <th class="px-4 py-3">
                                <a href="{{ $sortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-slate-900">
                                    Product <span class="text-[10px] {{ $sort === 'name' ? '' : 'opacity-40' }}">{{ $sortIndicator('name') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-3 hidden md:table-cell">Category</th>
                            <th class="px-4 py-3 text-right">
                                <a href="{{ $sortUrl('price') }}" class="inline-flex items-center gap-1 hover:text-slate-900 ml-auto">
                                    Price <span class="text-[10px] {{ $sort === 'price' ? '' : 'opacity-40' }}">{{ $sortIndicator('price') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">
                                <a href="{{ $sortUrl('stock') }}" class="inline-flex items-center gap-1 hover:text-slate-900">
                                    Stock <span class="text-[10px] {{ $sort === 'stock' ? '' : 'opacity-40' }}">{{ $sortIndicator('stock') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($products as $product)
                            @php
                                $stockState = $resolveStockState($product);
                                $categoryName = $product->productCategory?->name ?? 'Uncategorized';
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}"
                                       class="font-medium text-slate-900 hover:text-sky-600">
                                        {{ $product->name }}
                                    </a>
                                    <span class="block font-mono text-[11px] text-slate-500 mt-0.5">{{ $product->sku }}</span>
                                    <span class="block md:hidden text-[11px] text-slate-500">{{ $categoryName }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-600 hidden md:table-cell">{{ $categoryName }}</td>
                                <td class="px-4 py-3 text-right tabular-nums font-medium text-slate-900">${{ number_format((float) $product->price, 2) }}</td>
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
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $stockBadgeClasses[$stockState] }}">
                                        {{ ProductStockConstant::PRODUCT_STOCK_STATE_LABELS[$stockState] }}
                                    </span>
                                    <span class="block text-[11px] text-slate-500 tabular-nums mt-0.5">{{ $product->stock }} units</span>
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
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-600">
                                    @if ($hasFilters)
                                        No products match your filters.
                                    @else
                                        No products yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('pages.admin.components.pagination', ['paginator' => $products])
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
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
        });
    </script>
@endsection
