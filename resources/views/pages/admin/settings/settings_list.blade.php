@extends('pages.admin.layout')

@section('title', 'Settings – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Settings
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Review your store configuration grouped by section.
            </p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
            <tr class="text-left">
                <th class="px-4 py-3">Label</th>
                <th class="px-4 py-3">Value</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Public</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @forelse ($settings as $group => $groupSettings)
                <tr class="bg-slate-50">
                    <td colspan="4" class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700">
                        {{ $group }}
                    </td>
                </tr>

                @foreach ($groupSettings as $setting)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700">
                            {{ $setting['label'] }}
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            {{ $setting['value'] }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                            {{ $setting['type'] }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $setting['is_public'] ? 'Yes' : 'No' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-2 text-xs">
                                <a href="{{ route('admin.setting.edit', $setting['id']) }}" class="text-sky-600 hover:text-sky-700">
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-3 text-center text-slate-600">
                        No settings found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
