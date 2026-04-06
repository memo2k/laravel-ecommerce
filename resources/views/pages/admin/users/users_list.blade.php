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
        <a href="#"
           class="inline-flex items-center rounded-md px-4 py-2 text-xs font-medium text-white shadow-sm"
           style="background-color: var(--color-accent);">
            + Invite user
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden text-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-600">
            <tr class="text-left">
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @foreach (range(1, 6) as $i)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-900">
                        Demo user {{ $i }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        user{{ $i }}@example.com
                    </td>
                    <td class="px-4 py-3 text-slate-700">
                        {{ ['Administrator', 'Manager', 'Support', 'Viewer', 'Manager', 'Viewer'][($i - 1) % 4] }}
                    </td>
                    <td class="px-4 py-3">
                        @if ($i % 5 === 0)
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium text-slate-700 bg-slate-100">
                                Invited
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium text-emerald-200 bg-emerald-500/20">
                                Active
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2 text-xs">
                            <a href="#" class="text-sky-600 hover:text-sky-700">
                                Edit
                            </a>
                            <button type="button" class="text-rose-600 hover:text-rose-700">
                                Remove
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
