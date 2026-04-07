@extends('pages.admin.layout')

@section('title', 'Users – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Users
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Invite teammates and manage access to the admin.
            </p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
            <tr class="text-left">
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @foreach ($users as $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-900">
                        {{ $user->name }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ $user->email }}
                    </td>
                    <td class="px-4 py-3 text-slate-700">
                        {{ $user->roles->pluck('name')->implode(', ') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2 text-xs">
                            <a href="{{ route('admin.user.edit', $user->id) }}" class="text-sky-600 hover:text-sky-700">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
