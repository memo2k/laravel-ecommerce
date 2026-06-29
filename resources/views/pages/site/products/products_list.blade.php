@extends('pages.site.layout')

@section('title', 'Products – ShopDemo')

@section('content')
    <section id="products_filters" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Mobile filter backdrop --}}
        <div
            id="products_filters_backdrop"
            data-close-filters
            class="fixed inset-0 z-40 hidden bg-slate-900/40 lg:hidden"
            aria-hidden="true"
        ></div>

        <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
            <aside
                id="products_filters_drawer"
                class="fixed inset-y-0 left-0 z-50 flex w-full max-w-sm -translate-x-full flex-col overflow-y-auto rounded-none border-r border-slate-200 bg-white p-5 shadow-xl transition-transform duration-200 ease-out lg:static lg:z-auto lg:block lg:h-fit lg:w-auto lg:max-w-none lg:translate-x-0 lg:overflow-visible lg:rounded-xl lg:border lg:shadow-none"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-900">Filters</h2>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('products.index') ?? '#' }}"
                           class="text-xs font-medium text-slate-500 hover:text-slate-700">
                            Reset
                        </a>
                        <button
                            type="button"
                            data-close-filters
                            class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-700 lg:hidden"
                            aria-label="Close filters"
                        >
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <form method="GET" action="{{ route('products.index') ?? '#' }}" class="space-y-6">
                    <div>
                        <label for="q" class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-700">
                            Search
                        </label>
                        <input
                            id="q"
                            name="q"
                            type="text"
                            value="{{ $search }}"
                            placeholder="Search product..."
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                        >
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-700">Category</p>
                        <div class="space-y-2">
                            @foreach ($categories as $category)
                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                    <input
                                        type="radio"
                                        name="category"
                                        value="{{ $category->id }}"
                                        @checked((string) $selectedCategory === (string) $category->id)
                                        class="h-4 w-4 border-slate-300 text-slate-700 focus:ring-slate-300"
                                    >
                                    <span>{{ $category->name }}</span>
                                </label>
                            @endforeach

                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input
                                    type="radio"
                                    name="category"
                                    value=""
                                    @checked(empty($selectedCategory))
                                    class="h-4 w-4 border-slate-300 text-slate-700 focus:ring-slate-300"
                                >
                                <span>All categories</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-700">Price range</p>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                   placeholder="Min"
                                   class="w-full min-w-0 rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                   placeholder="Max"
                                   class="w-full min-w-0 rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                    </div>

                    @if ($attributeOptions)
                        @foreach ($attributeOptions as $attributeName => $attributeOptions)
                            <div>
                                <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-700">{{ $attributeName }}</p>
                                <div class="space-y-2">
                                    @foreach ($attributeOptions as $attributeOption)
                                        <label class="flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="attribute_options[]" value="{{ $attributeOption->id }}" @checked(!empty($selectedAttributeOptions) && in_array($attributeOption->id, $selectedAttributeOptions)) class="h-4 w-4 border-slate-300 text-slate-700 focus:ring-slate-300">
                                            <span>{{ $attributeOption->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-md px-4 py-2.5 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        Apply filters
                    </button>
                </form>
            </aside>

            <div class="min-w-0 space-y-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm text-slate-600">
                                Showing <span class="font-semibold text-slate-900">{{ $products->count() }}</span> products
                            </p>

                            <button
                                type="button"
                                data-open-filters
                                class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 lg:hidden"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                </svg>
                                Filters
                            </button>
                        </div>

                        <form method="GET" action="{{ route('products.index') ?? '#' }}" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                            <input type="hidden" name="q" value="{{ $search }}">
                            <input type="hidden" name="category" value="{{ $selectedCategory }}">
                            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                            <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            <label for="sort" class="text-xs font-medium uppercase tracking-wide text-slate-700">
                                Sort
                            </label>
                            <select id="sort" name="sort"
                                    onchange="this.form.submit()"
                                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300 sm:w-auto">
                                <option value="newest" @selected($sortBy === 'newest')>Newest</option>
                                <option value="price_asc" @selected($sortBy === 'price_asc')>Price: Low to high</option>
                                <option value="price_desc" @selected($sortBy === 'price_desc')>Price: High to low</option>
                                <option value="name_asc" @selected($sortBy === 'name_asc')>Name: A-Z</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-3">
                    @forelse ($products as $product)
                        @php
                            $stock = $product->stock > $lowStockThreshold 
                                ? \App\Constants\ProductStockConstant::IN_STOCK 
                                : ($product->stock > 0 ? \App\Constants\ProductStockConstant::LOW_STOCK : \App\Constants\ProductStockConstant::OUT_OF_STOCK);

                            $stockBadgeClass = match ($stock) {
                                \App\Constants\ProductStockConstant::IN_STOCK => 'text-black bg-emerald-200',
                                \App\Constants\ProductStockConstant::LOW_STOCK => 'text-black bg-amber-200',
                                \App\Constants\ProductStockConstant::OUT_OF_STOCK => 'text-black bg-red-200',
                                default => 'text-black bg-slate-200',
                            };
                        @endphp
                        <article class="flex flex-col gap-2 rounded-xl border border-slate-200 bg-white p-3 sm:gap-3 sm:p-4">
                            <a href="{{ route('product.show', $product->slug) }}"
                               class="block aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-100 sm:aspect-[4/3]">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . ltrim($product->image, '/')) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-slate-100">
                                        <span class="text-[10px] text-slate-500 sm:text-xs">No image</span>
                                    </div>
                                @endif
                            </a>

                            <div class="min-w-0">
                                <p class="mb-0.5 truncate text-[10px] font-medium text-slate-500 sm:mb-1 sm:text-xs">
                                    {{ $product->productCategory?->name ?? 'Category' }}
                                </p>
                                <h3 class="line-clamp-2 text-xs font-semibold text-slate-900 sm:text-sm">
                                    <a href="{{ route('product.show', $product->slug) }}"
                                       class="hover:underline">
                                        {{ $product->name ?? 'Product name' }}
                                    </a>
                                </h3>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium sm:px-2.5 sm:text-[11px] {{ $stockBadgeClass }}">
                                    {{ \App\Constants\ProductStockConstant::PRODUCT_STOCK_STATE_LABELS[$stock] }}
                                </span>
                            </div>

                            <div class="mt-auto flex items-end justify-between gap-2 sm:items-center sm:gap-3">
                                <div class="min-w-0">
                                    @if ($product->discount_price > 0)
                                        <div class="flex flex-col sm:flex-row sm:items-baseline sm:gap-2">
                                            <span class="text-sm font-semibold text-red-500 sm:text-base">
                                                ${{ number_format($product->discount_price, 2) }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 line-through sm:text-xs">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-sm font-semibold text-slate-900 sm:text-base">
                                            ${{ number_format($product->price, 2) }}
                                        </span>
                                    @endif
                                </div>

                                @if ($stock !== \App\Constants\ProductStockConstant::OUT_OF_STOCK)
                                    <button type="button"
                                            data-product-id="{{ $product->id }}"
                                            class="add-to-cart shrink-0 rounded-full px-2.5 py-1.5 text-[10px] font-medium text-white sm:px-3 sm:text-xs"
                                            style="background-color: var(--color-accent);">
                                        Add
                                    </button>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="col-span-2 rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center xl:col-span-3">
                            <p class="mb-1 text-sm font-medium text-slate-800">No products found</p>
                            <p class="text-xs text-slate-500">Try adjusting your filters or search terms.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(function () {
            var $section = $('#products_filters');
            var $drawer = $('#products_filters_drawer');
            var $backdrop = $('#products_filters_backdrop');

            function isMobileFilters() {
                return window.matchMedia('(max-width: 1023px)').matches;
            }

            function openFilters() {
                if (!isMobileFilters()) {
                    return;
                }

                $drawer.removeClass('-translate-x-full');
                $backdrop.removeClass('hidden');
                $('body').addClass('overflow-hidden');
            }

            function closeFilters() {
                $drawer.addClass('-translate-x-full');
                $backdrop.addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }

            $section.on('click', '[data-open-filters]', openFilters);
            $section.on('click', '[data-close-filters]', closeFilters);

            $(document).on('keydown.productsFilters', function (event) {
                if (event.key === 'Escape') {
                    closeFilters();
                }
            });

            $(window).on('resize.productsFilters', function () {
                if (!isMobileFilters()) {
                    closeFilters();
                }
            });
        });
    </script>
@endpush
