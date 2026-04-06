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
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Price</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @foreach (range(1, 6) as $i)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-900">
                        Demo product {{ $i }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        Category {{ $i }}
                    </td>
                    <td class="px-4 py-3 text-slate-900">
                        ${{ 20 + $i }}.00
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium text-emerald-200 bg-emerald-500/20">
                            Active
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-900">
                        {{ 10 * $i }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2 text-xs">
                            <a href="#" class="text-sky-600 hover:text-sky-700">
                                Edit
                            </a>
                            <button type="button" class="text-rose-600 hover:text-rose-700">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

