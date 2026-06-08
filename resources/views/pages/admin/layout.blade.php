@php
    // Reuse the same 3-color palette for admin, but you can override these per-view.
    $primaryColor = $primaryColor ?? '#ffffff';   // Sidebar/header surface
    $secondaryColor = $secondaryColor ?? '#f8fafc'; // Page background
    $accentColor = $accentColor ?? '#3b82f6';    // Accent for active links/buttons
@endphp

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ShopDemo Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @yield('head')
</head>
<body
    class="min-h-screen flex flex-col text-slate-900"
    style="
        --color-primary: {{ $primaryColor }};
        --color-secondary: {{ $secondaryColor }};
        --color-accent: {{ $accentColor }};
        background-color: var(--color-secondary);
    "
>
    @include('pages.components.demo_notice')

    <div class="flex flex-1 min-h-0">
    <!-- Sidebar -->
    <aside
        class="hidden md:flex md:flex-col w-64 bg-white border-r border-slate-200"
        style="background-color: var(--color-primary);"
    >
        <div class="px-6 py-4 border-b border-slate-200">
            <a href="#" class="text-lg font-semibold tracking-tight">
                ShopDemo <span class="text-sm font-normal text-slate-500">Admin</span>
            </a>
        </div>

        @php $currentRoute = Route::currentRouteName(); @endphp

        <nav class="flex-1 px-4 py-4 space-y-1 text-sm">
            <a href="{{ route('admin.dashboard') }}"
               class="block rounded-md px-3 py-2 font-medium transition-colors {{ $currentRoute === 'admin.dashboard' ? 'text-white' : 'text-slate-700 hover:bg-slate-100' }}"
               @if($currentRoute === 'admin.dashboard') style="background-color: var(--color-accent);" @endif>
                Dashboard
            </a>

            <a href="{{ route('admin.orders') }}"
               class="block rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                Orders
            </a>

            <a href="{{ route('admin.products') }}"
               class="block rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                Products
            </a>

            <a href="{{ route('admin.product-categories') }}"
               class="block rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                Categories
            </a>

            <a href="{{ route('admin.attributes') }}"
               class="block rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                Attributes
            </a>

            <a href="{{ route('admin.users') }}"
               class="block rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                Users
            </a>

            <a href="{{ route('admin.roles') }}"
               class="block rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                Roles
            </a>

            <a href="{{ route('admin.permissions') }}"
               class="block rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                Permissions
            </a>

            <a href="{{ route('admin.settings') }}"
               class="block rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                Settings
            </a>

            {{-- <a href="{{ route('admin.logs') }}"
               class="block rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                Logs
            </a> --}}
        </nav>

        <div class="px-4 py-4 border-t border-slate-200 space-y-3">
            <a href="{{ route('products.index') }}" target="_blank"
               class="flex items-center gap-2 text-xs text-slate-500 hover:text-slate-700 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                </svg>
                View site
            </a>
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} ShopDemo</p>
        </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-w-0" style="background-color: var(--color-secondary);">
        <!-- Mobile top nav -->
        <header class="md:hidden w-full border-b border-slate-200"
                style="background-color: var(--color-primary);">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between text-sm">
                <div class="font-semibold">
                    ShopDemo <span class="text-slate-500">Admin</span>
                </div>
                <div class="flex gap-3 text-xs">
                    <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a>
                    <a href="{{ route('admin.dashboard') }}" class="hover:underline">Products</a>
                    <a href="#" class="hover:underline">Orders</a>
                    <a href="#" class="hover:underline">Customers</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('products.index') }}" target="_blank" class="hover:underline text-slate-500">Site ↗</a>
                </div>
            </div>
        </header>

        <main class="w-full max-w-6xl mx-auto px-4 py-6">
            @yield('content')
        </main>
    </div>
    </div>

    @yield('scripts')
</body>
</html>

