@extends('pages.admin.layout')

@section('title', 'Edit role – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Edit role
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Adjust the role label and which capabilities it grants.
            </p>
        </div>
        <a href="#"
           class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 hover:bg-slate-100">
            Back to roles
        </a>
    </div>

    <form class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] text-sm">
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Role details
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Display name
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Slug
                        </label>
                        <input type="text"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Description
                        </label>
                        <textarea rows="3"
                                  class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Permissions
                </h2>
                <p class="text-xs text-slate-600">
                    Demo checkboxes — wire to your authorization layer when ready.
                </p>
                <div class="space-y-2">
                    @foreach (['Manage products', 'Manage orders', 'Manage users', 'Manage roles', 'View reports'] as $perm)
                        <label class="flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 cursor-pointer hover:bg-slate-100">
                            <input type="checkbox"
                                   class="rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-500 focus:ring-offset-0">
                            <span class="text-slate-800">{{ $perm }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Actions
                </h2>
                <div class="space-y-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        Save role
                    </button>
                    <button type="button"
                            class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-xs font-medium border border-slate-300 text-slate-700 hover:bg-slate-100">
                        Duplicate role
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
