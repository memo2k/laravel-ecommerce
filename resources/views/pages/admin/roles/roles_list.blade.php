@extends('pages.admin.layout')

@section('title', 'Roles – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Roles
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Define who can access which areas of the admin.
            </p>
        </div>
        <a href="#"
           class="inline-flex items-center rounded-md px-4 py-2 text-xs font-medium text-white shadow-sm"
           style="background-color: var(--color-accent);">
            + Add role
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
            <tr class="text-left">
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Active</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @foreach ($roles as $role)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-900">
                        {{ $role->name }}
                    </td>
                    <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                        {{ $role->is_active ? 'Yes' : 'No' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2 text-xs">
                            <a href="{{ route('admin.role.edit', $role->id) }}" class="text-sky-600 hover:text-sky-700">
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
