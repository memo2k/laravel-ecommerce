@extends('pages.admin.layout')

@section('title', 'Categories – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Categories
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Organize products into clear groups for your storefront.
            </p>
        </div>
        <a href="{{ route('admin.product-category.edit') }}"
           class="inline-flex items-center rounded-md px-4 py-2 text-xs font-medium text-white shadow-sm"
           style="background-color: var(--color-accent);">
            + Add category
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
            <tr class="text-left">
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Slug</th>
                <th class="px-4 py-3">Products</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @forelse ($productCategories as $productCategory)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-900">
                        {{ $productCategory->name }}
                    </td>
                    <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                        {{ $productCategory->slug }}
                    </td>
                    <td class="px-4 py-3 text-slate-900">
                        {{ $productCategory->products->count() }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2 text-xs">
                            <a href="{{ route('admin.product-category.edit', $productCategory->id) }}" class="text-sky-600 hover:text-sky-700">
                                Edit
                            </a>
                            <button type="button" id="delete-category" data-id="{{ $productCategory->id }}" class="text-rose-600 hover:text-rose-700">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-slate-600">
                        No product categories found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#delete-category').click(function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this product category?')) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        url: `{{ route('admin.product-category.delete') }}`,
                        type: 'DELETE',
                        data: {
                            product_category_id: id,
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
