@extends('pages.site.layout')

@section('title', 'Products – ShopDemo')

@section('content')
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-slate-900">
                        Products
                    </h1>
                    <p class="text-sm text-slate-600 mt-1">
                        Discover simple, clean product cards with practical filters.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
            <aside class="rounded-xl border border-slate-200 bg-white p-5 h-fit">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Filters</h2>
                    <a href="{{ route('products.index') ?? '#' }}"
                       class="text-xs font-medium text-slate-500 hover:text-slate-700">
                        Reset
                    </a>
                </div>

                <form method="GET" action="{{ route('products.index') ?? '#' }}" class="space-y-6">
                    <div>
                        <label for="q" class="block text-xs font-medium text-slate-700 uppercase tracking-wide mb-2">
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
                        <p class="text-xs font-medium text-slate-700 uppercase tracking-wide mb-2">Category</p>
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
                        <p class="text-xs font-medium text-slate-700 uppercase tracking-wide mb-2">Price range</p>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                   placeholder="Min"
                                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                   placeholder="Max"
                                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                    </div>

                    @if ($attributeOptions)
                        @foreach ($attributeOptions as $attributeName => $attributeOptions)
                            <div>
                                <p class="text-xs font-medium text-slate-700 uppercase tracking-wide mb-2">{{ $attributeName }}</p>
                                <div class="space-y-2">
                                    @foreach ($attributeOptions as $attributeOption)
                                        <label class="flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="attribute_options[]" value="{{ $attributeOption->id }}" class="h-4 w-4 border-slate-300 text-slate-700 focus:ring-slate-300">
                                            <span>{{ $attributeOption->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <button type="submit"
                            class="w-full inline-flex justify-center items-center rounded-md px-4 py-2.5 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        Apply filters
                    </button>
                </form>
            </aside>

            <div class="space-y-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-sm text-slate-600">
                            Showing <span class="font-semibold text-slate-900">{{ $products->count() }}</span> products
                        </p>

                        <form method="GET" action="{{ route('products.index') ?? '#' }}" class="flex items-center gap-2">
                            <input type="hidden" name="q" value="{{ $search }}">
                            <input type="hidden" name="category" value="{{ $selectedCategory }}">
                            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                            <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            <label for="sort" class="text-xs font-medium text-slate-700 uppercase tracking-wide">
                                Sort
                            </label>
                            <select id="sort" name="sort"
                                    onchange="this.form.submit()"
                                    class="rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                                <option value="newest" @selected($sortBy === 'newest')>Newest</option>
                                <option value="price_asc" @selected($sortBy === 'price_asc')>Price: Low to high</option>
                                <option value="price_desc" @selected($sortBy === 'price_desc')>Price: High to low</option>
                                <option value="name_asc" @selected($sortBy === 'name_asc')>Name: A-Z</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($products as $product)
                        <article class="rounded-xl border border-slate-200 bg-white p-4 flex flex-col gap-3">
                            <a href="{{ route('product.show', $product->slug) }}"
                               class="block aspect-[4/3] rounded-lg bg-slate-100 border border-slate-200">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . ltrim($product->image, '/')) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                        <span class="text-xs text-slate-500">No image</span>
                                    </div>
                                @endif
                            </a>

                            <div>
                                <p class="text-xs font-medium text-slate-500 mb-1">
                                    {{ $product->productCategory?->name ?? 'Category' }}
                                </p>
                                <h3 class="text-sm font-semibold text-slate-900">
                                    <a href="{{ route('product.show', $product->slug) }}"
                                       class="hover:underline">
                                        {{ $product->name ?? 'Product name' }}
                                    </a>
                                </h3>
                            </div>

                            <div class="mt-auto flex items-center justify-between gap-3">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-base font-semibold text-slate-900">
                                        ${{ number_format($product->price ?? 0, 2) }}
                                    </span>
                                    @if (!empty($product->old_price))
                                        <span class="text-xs text-slate-400 line-through">
                                            ${{ number_format($product->old_price, 2) }}
                                        </span>
                                    @endif
                                </div>

                                <button type="button"
                                        data-product-id="{{ $product->id }}"
                                        class="rounded-full px-3 py-1.5 text-xs font-medium text-white add-to-cart"
                                        style="background-color: var(--color-accent);">
                                    Add
                                </button>
                            </div>
                        </article>
                    @empty
                        <div class="sm:col-span-2 xl:col-span-3 rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">
                            <p class="text-sm font-medium text-slate-800 mb-1">No products found</p>
                            <p class="text-xs text-slate-500">Try adjusting your filters or search terms.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection