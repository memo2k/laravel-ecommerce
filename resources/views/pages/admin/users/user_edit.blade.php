@extends('pages.admin.layout')

@section('title', ($user->id ? 'Edit' : 'Create') . ' user - ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                {{ $user->id ? 'Edit user' : 'Create user' }}
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Update profile details, role assignment, and account status.
            </p>
        </div>
        <a href="{{ route('admin.users') }}"
           class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 hover:bg-slate-100">
            Back to users
        </a>
    </div>

    <form method="POST" action="{{ route('admin.user.save') }}" class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] text-sm">
        @csrf
        <input type="hidden" name="id" value="{{ $user->id ?? '' }}">

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Profile
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Name
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Email
                        </label>
                        <input type="email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Security
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            New password
                        </label>
                        <input type="password" autocomplete="new-password"
                               name="password"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500"
                               placeholder="Leave blank to keep current">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Confirm password
                        </label>
                        <input type="password" autocomplete="new-password"
                               name="password_confirmation"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Access
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Role
                        </label>
                        <select
                            name="role"
                            class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role', optional($user->roles->first())->id) == $role->id)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Actions
                </h2>
                <div class="space-y-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        {{ $user->id ? 'Save user' : 'Create user' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
