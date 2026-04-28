@extends('pages.admin.layout')

@section('title', 'Products – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Products
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Manage the catalog for your demo storefront.
            </p>
        </div>
        <a href="{{ route('admin.product.edit') }}"
           class="inline-flex items-center rounded-md px-4 py-2 text-xs font-medium text-white shadow-sm"
           style="background-color: var(--color-accent);">
            + Add product
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
            <tr class="text-left">
                <th class="px-4 py-3">Image</th>
                <th class="px-4 py-3">SKU</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Price</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($products as $product)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('product.show', $product->slug) }}" target="_blank">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . ltrim($product->image, '/')) }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover">
                                @else
                                    <div class="w-10 h-10 bg-slate-100 flex items-center justify-center">
                                        <span class="text-xs text-slate-500">No image</span>
                                    </div>
                                @endif
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-900 font-mono text-xs">
                            <a href="{{ route('product.show', $product->slug) }}" target="_blank">
                                {{ $product->sku }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-900">
                            <a href="{{ route('product.show', $product->slug) }}" target="_blank">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $product->productCategory->name }}
                        </td>
                        <td class="px-4 py-3 text-slate-900">
                            ${{ $product->price }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($product->is_active)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium text-black bg-emerald-200">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium text-black bg-red-200">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-900">
                            {{ $product->stock }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-2 text-xs">
                                <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}" class="text-sky-600 hover:text-sky-700">
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
                        <td colspan="6" class="px-4 py-3 text-center text-slate-600">
                            No products found
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
            $('.delete-product-button').on('click', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this product?')) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        url: `{{ route('admin.product.delete') }}`,
                        type: 'DELETE',
                        data: {
                            product_id: id,
                        },
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