@extends('pages.admin.layout')

@section('title', 'Edit category – ShopDemo Admin')

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

    <form method="POST" action="{{ route('admin.product-category.save') }}" class="lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] text-sm">
        @csrf

        <input type="hidden" name="id" value="{{ $productCategory->id ?? '' }}">

        <div class="space-y-6">
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

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        Save category
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
