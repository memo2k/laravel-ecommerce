@extends('pages.admin.layout')

@section('title', 'Edit category – ShopDemo Admin')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Edit category
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Update category details and visibility.
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.product-category.save') }}" class="space-y-6 text-sm">
        @csrf

        <input type="hidden" name="id" value="{{ $productCategory->id ?? '' }}">

        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-900">
                Basic information
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                        Name
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ $productCategory->name ?? old('name') }}"
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
                           value="{{ $productCategory->slug ?? old('slug') }}"
                           class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                           @error('slug')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                           @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                        Description
                    </label>
                    <textarea rows="4"
                              name="description"
                              class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">{{ $productCategory->description ?? old('description') }}</textarea>
                </div>
            </div>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white overflow-hidden">
            <header class="flex items-start justify-between gap-4 px-5 py-4 border-b border-slate-200">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">
                        Attributes
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Link attributes that are relevant to products in this category.
                    </p>
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                    {{ count($attributes ?? []) }} linked
                </span>
            </header>

            <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
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
                    <button type="button" 
                            id="add-attribute-button"
                            class="inline-flex items-center justify-center gap-1.5 rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 4a.75.75 0 01.75.75v4.5h4.5a.75.75 0 010 1.5h-4.5v4.5a.75.75 0 01-1.5 0v-4.5h-4.5a.75.75 0 010-1.5h4.5v-4.5A.75.75 0 0110 4z"/>
                        </svg>
                        Add
                    </button>
                </div>
            </div>

            <div class="px-5 py-4" id="product-category-edit-attributes">
                @include('pages.admin.product_categories._product_category_edit_attributes', ['attributes' => $attributes])
            </div>
        </section>

        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-900">
                Actions
            </h2>
            <button type="submit"
                    class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                    style="background-color: var(--color-accent);">
                Save category
            </button>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        function initSelect2() {
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

            $('#add-attribute-button').on('click', function() {
                $.ajax({
                    url: '{{ route('admin.product-category.add-attribute') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_category_id: '{{ $productCategory->id }}',
                        attribute_id: $('#attribute-select2').val(),
                    },
                    success: function(response) {
                        $('#product-category-edit-attributes').html(response.html);
                        $('#attribute-select2').val('').trigger('change');
                        initSelect2();
                    },
                    error: function(xhr, status, error) {
                        $('#attribute-select2-error').text('Please select an attribute');
                    }
                });
            });
        });

        $(document).on('click', '.remove-attribute-button', function() {
            var attributeId = $(this).data('attribute-id');
            $.ajax({
                url: '{{ route('admin.product-category.remove-attribute') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_category_id: '{{ $productCategory->id }}',
                    attribute_id: attributeId,
                },
                success: function(response) {
                    $('#product-category-edit-attributes').html(response.html);
                }
            });
        });
    </script>
@endsection