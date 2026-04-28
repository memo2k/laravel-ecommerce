@extends('pages.admin.layout')

@section('title', 'Attributes – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Attributes
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Define reusable attributes and their options for your products.
            </p>
        </div>
        <a href="{{ route('admin.attribute.edit') }}"
           class="inline-flex items-center rounded-md px-4 py-2 text-xs font-medium text-white shadow-sm"
           style="background-color: var(--color-accent);">
            + Add attribute
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
            <tr class="text-left">
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Description</th>
                <th class="px-4 py-3">Options</th>
                <th class="px-4 py-3">Categories</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @forelse ($attributes as $attribute)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-900 font-medium">
                        {{ $attribute->name }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ \Illuminate\Support\Str::limit($attribute->description, 80) ?: '—' }}
                    </td>
                    <td class="px-4 py-3 text-slate-900">
                        {{ $attribute->attributeOptions->count() }}
                    </td>
                    <td class="px-4 py-3 text-slate-900">
                        {{ $attribute->productCategories->count() }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2 text-xs">
                            <a href="{{ route('admin.attribute.edit', $attribute->id) }}" class="text-sky-600 hover:text-sky-700">
                                Edit
                            </a>
                            <button type="button" class="text-rose-600 hover:text-rose-700 delete-attribute-button" data-id="{{ $attribute->id }}">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-slate-600">
                        No attributes found
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
            $('.delete-attribute-button').on('click', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this attribute?')) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        url: `{{ route('admin.attribute.delete') }}`,
                        type: 'DELETE',
                        data: {
                            attribute_id: id,
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error deleting attribute: ' + error);
                        }
                    });
                }
            });
        });
    </script>
@endsection
