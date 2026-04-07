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
    </div>

    <form method="POST" action="{{ route('admin.role.save') }}" class="lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] text-sm">
        @csrf
        <input type="hidden" name="id" value="{{ $role->id ?? '' }}">
        
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Role details
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                            Name
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ $role->name ?? old('name') }}"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                               @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                               @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="is_active" class="block text-xs font-medium text-slate-600 uppercase tracking-wide">
                            Active
                        </label>
                        <input type="checkbox"
                               name="is_active"
                               id="is_active"
                               @checked($role->is_active)
                               class="rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-500 focus:ring-offset-0">
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-900">
                    Permissions
                </h2>
                <div class="space-y-2">
                    <div class="max-h-[500px] overflow-y-auto">
                        @foreach ($permissions as $permission)
                            <label class="flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 cursor-pointer hover:bg-slate-100">
                                <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $permission->id }}"
                                   @checked($role->permissions->contains('id', $permission->id))
                                   class="rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-500 focus:ring-offset-0">
                                <span class="text-slate-800">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                        style="background-color: var(--color-accent);">
                    Save role
                </button>
            </div>
        </div>
    </form>
@endsection
