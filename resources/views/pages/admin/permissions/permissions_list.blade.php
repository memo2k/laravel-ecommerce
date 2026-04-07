@extends('pages.admin.layout')

@section('title', 'Permissions – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Permissions
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Manage granular access rules that can be assigned to roles.
            </p>
        </div>
        <a href="{{ route('admin.permission.edit') }}"
           class="inline-flex items-center rounded-md px-4 py-2 text-xs font-medium text-white shadow-sm"
           style="background-color: var(--color-accent);">
            + Add permission
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
            <tr class="text-left">
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Description</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @forelse ($permissions as $permission)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-900">
                        {{ $permission->name }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ $permission->description ?: '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2 text-xs">
                            <a href="{{ route('admin.permission.edit', $permission->id) }}" class="text-sky-600 hover:text-sky-700">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-3 text-center text-slate-600">
                        No permissions found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
