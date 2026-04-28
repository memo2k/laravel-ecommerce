@extends('pages.admin.layout')

@section('title', 'Edit product – ShopDemo Admin')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Edit product
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Update product details, pricing, and availability.
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.product.save') }}" class="space-y-6 text-sm" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="id" value="{{ $product->id ?? '' }}">
        
        <!-- Main column -->
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Basic information
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            SKU
                        </label>
                        <input type="text"
                               name="sku"
                               value="{{ $product->sku ?? old('sku') }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                               @error('sku')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                               @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Name
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ $product->name ?? old('name') }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                               @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                               @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Slug
                        </label>
                        <input type="text"
                               name="slug"
                               value="{{ $product->slug ?? old('slug') }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <p class="text-xs text-slate-600 mt-1">
                            Automatically generated from the name if not provided.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Description
                        </label>
                        <textarea rows="4"
                                  name="description"
                                  class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">{{ $product->description ?? old('description') }}</textarea>
                                  @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                               @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Image
                        </label>
                        <input type="file"
                               name="image"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                               @error('image')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                               @enderror
                    </div>
                    @if ($product->image)
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('storage/' . ltrim($product->image, '/')) }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Catalog -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Catalog
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Category
                        </label>
                        <select
                            name="category"
                            class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="">- Select a category -</option>
                            @foreach ($productCategories as $productCategory)
                                <option value="{{ $productCategory->id }}" @selected($product->product_category_id ?? old('category') == $productCategory->id)>{{ $productCategory->name }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="is_active" class="block text-xs font-medium text-slate-600 uppercase tracking-wide">
                            Active
                        </label>
                        <input type="checkbox"
                                name="is_active"
                                id="is_active"
                                @checked($product->is_active ?? old('is_active'))
                                class="rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-500 focus:ring-offset-0">
                    </div>
                </div>
            </div>
        </div>

        <!-- Attributes -->
        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
            <header class="flex items-start justify-between gap-4 px-5 py-4 border-b border-slate-200">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">
                        Attributes
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Assign attribute options to describe this product.
                    </p>
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                    {{ count($attributeOptions ?? []) }} attached
                </span>
            </header>
        
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] md:items-end">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Attribute
                        </label>
                        <select
                            id="attribute-select2"
                            class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="">- Select attribute -</option>
                        </select>
                        <span id="attribute-select2-error" class="text-red-500 text-xs mt-1"></span>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Option
                        </label>
                        <select id="attribute-option-select2" disabled
                            class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="">- Select option -</option>
                        </select>
                        <span id="attribute-option-select2-error" class="text-red-500 text-xs mt-1"></span>
                    </div>
                    <button type="button"
                            id="add-attribute-option-button"
                            class="inline-flex items-center justify-center gap-1.5 rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 4a.75.75 0 01.75.75v4.5h4.5a.75.75 0 010 1.5h-4.5v4.5a.75.75 0 01-1.5 0v-4.5h-4.5a.75.75 0 010-1.5h4.5v-4.5A.75.75 0 0110 4z"/>
                        </svg>
                        Add
                    </button>
                </div>
            </div>
        
            <div class="px-5 py-4" id="product-edit-attributes">
                @include('pages.admin.products._product_edit_attributes', ['attributeOptions' => $attributeOptions])
            </div>
        </div>        

        <!-- Pricing & inventory -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-900">
                Pricing & inventory
            </h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                        Price
                    </label>
                    <input type="number" step="0.01"
                           name="price"
                           value="{{ $product->price ?? old('price') }}"
                           class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                           @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                           @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                        Stock
                    </label>
                    <input type="number"
                           name="stock"
                           value="{{ $product->stock ?? old('stock') }}"
                           class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                           @error('stock')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                           @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-900">
                Actions
            </h2>
            <div class="space-y-3">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                        style="background-color: var(--color-accent);">
                    Save product
                </button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        function initSelect2() {
            $('#attribute-option-select2').select2({
                placeholder: 'Search for an option',
                ajax: {
                    url: '{{ route('admin.select') }}',
                    data: function(params) {
                        return {
                            name: 'attribute-option',
                            attribute_id: $('#attribute-select2').val(),
                            term: params.term,
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return { id: item.id, text: item.name };
                            })
                        };
                    }
                }
            });
    
            $('#attribute-select2').select2({
                placeholder: 'Search for an attribute',
                ajax: {
                    url: '{{ route('admin.select', ['name' => 'attribute']) }}',
                    data: function(params) {
                        return {
                            name: 'attribute',
                            term: params.term,
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return { id: item.id, text: item.name };
                            })
                        };
                    }
                }
            });
        }
        
        $(document).ready(function() {
            initSelect2();

            $('#attribute-select2').on('change', function() {
                $('#attribute-select2-error').text('');
                $('#attribute-option-select2-error').text('');
                $('#attribute-option-select2').prop('disabled', false);
                $('#attribute-option-select2').val('').trigger('change');
            });

            $('#add-attribute-option-button').on('click', function() {
                $.ajax({
                    url: '{{ route('admin.product.add-attribute-option') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: '{{ $product->id }}',
                        attribute_id: $('#attribute-select2').val(),
                        attribute_option_id: $('#attribute-option-select2').val(),
                    },
                    success: function(response) {
                        if (response.success === true) {
                            $('#product-edit-attributes').html(response.html);
                            $('#attribute-select2').val('').trigger('change');
                            $('#attribute-option-select2').prop('disabled', true).select2('destroy').val('').trigger('change');
                            initSelect2();
                        } else {
                            if (response.errors.attribute_id) {
                                $('#attribute-select2-error').text(response.errors.attribute_id[0]);
                            }
                            if (response.errors.attribute_option_id) {
                                $('#attribute-option-select2-error').text(response.errors.attribute_option_id[0]);
                            }
                        }
                    },
                });
            });
        });

        $(document).on('click', '.remove-attribute-option-button', function() {
            var attributeOptionId = $(this).data('attribute-option-id');
            $.ajax({
                url: '{{ route('admin.product.remove-attribute-option') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: '{{ $product->id }}',
                    attribute_option_id: attributeOptionId,
                },
                success: function(response) {
                    $('#product-edit-attributes').html(response.html);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        });
    </script>
@endsection