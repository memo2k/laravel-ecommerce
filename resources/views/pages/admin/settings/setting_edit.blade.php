@extends('pages.admin.layout')

@section('title', 'Edit setting – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Edit setting
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Update setting value while keeping metadata locked.
            </p>
        </div>
    </div>

    @if ($setting)
        <form method="POST" action="{{ route('admin.setting.save') }}" class="space-y-6 text-sm">
            @csrf
            <input type="hidden" name="id" value="{{ $setting->id }}">

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Setting details
                </h2>

                <div>
                    <p class="text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">Key</p>
                    <p class="text-sm text-slate-900 font-mono">{{ $setting->key }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">Group</p>
                    <p class="text-sm text-slate-900">{{ $setting->group }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">Type</p>
                    <p class="text-sm text-slate-900 font-mono">{{ $setting->type }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">Public</p>
                    <p class="text-sm text-slate-900">{{ $setting->is_public ? 'Yes' : 'No' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">Description</p>
                    <p class="text-sm text-slate-700">{{ $setting->description ?: '—' }}</p>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Value
                </h2>
                <div>
                    <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                        Setting value
                    </label>
                    <input type="text"
                           name="value"
                           value="{{ old('value', $setting->value) }}"
                           class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    @error('value')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Actions
                </h2>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                        style="background-color: var(--color-accent);">
                    Save setting
                </button>
            </div>
        </form>
    @else
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-sm text-slate-600">
            Setting not found.
        </div>
    @endif
@endsection
