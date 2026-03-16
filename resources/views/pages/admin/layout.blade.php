@php
    // Reuse the same 3-color palette for admin, but you can override these per-view.
    $primaryColor = $primaryColor ?? '#0f172a';   // Darker primary for admin sidebar/header
    $secondaryColor = $secondaryColor ?? '#111827'; // Secondary background
    $accentColor = $accentColor ?? '#3b82f6';    // Accent for active links/buttons
@endphp

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ShopDemo Admin')</title>
</head>
<body
    class="min-h-screen flex bg-slate-900 text-slate-100"
    style="
        --color-primary: {{ $primaryColor }};
        --color-secondary: {{ $secondaryColor }};
        --color-accent: {{ $accentColor }};
    "
>
    <!-- Sidebar -->
    <aside
        class="hidden md:flex md:flex-col w-64 bg-slate-950 border-r border-slate-800"
        style="background-color: var(--color-primary);"
    >
        <div class="px-6 py-4 border-b border-slate-800">
            <a href="#" class="text-lg font-semibold tracking-tight">
                ShopDemo <span class="text-sm font-normal text-slate-300">Admin</span>
            </a>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 text-sm">
            <a href="#"
               class="block rounded-md px-3 py-2 font-medium hover:bg-slate-800 transition-colors">
                Dashboard
            </a>
            <div class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400 px-3">
                Catalog
            </div>
            <a href="#"
               class="block rounded-md px-3 py-2 font-medium bg-slate-800 text-white"
               style="background-color: var(--color-accent);">
                Products
            </a>

            <div class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400 px-3">
                Sales
            </div>
            <a href="#"
               class="block rounded-md px-3 py-2 font-medium hover:bg-slate-800 transition-colors">
                Orders
            </a>
            <a href="#"
               class="block rounded-md px-3 py-2 font-medium hover:bg-slate-800 transition-colors">
                Customers
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-slate-800 text-xs text-slate-400">
            &copy; {{ date('Y') }} ShopDemo
        </div>
    </aside>

    <!-- Mobile top nav -->
    <header class="md:hidden w-full border-b border-slate-800"
            style="background-color: var(--color-primary);">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between text-sm">
            <div class="font-semibold">
                ShopDemo <span class="text-slate-300">Admin</span>
            </div>
            <div class="flex gap-3 text-xs">
                <a href="#" class="hover:underline">Dashboard</a>
                <a href="#" class="hover:underline">Catalog</a>
                <a href="#" class="hover:underline">Orders</a>
                <a href="#" class="hover:underline">Customers</a>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <div class="flex-1 flex flex-col bg-slate-900">
        <main class="w-full max-w-6xl mx-auto px-4 py-6">
            @yield('content')
        </main>
    </div>
</body>
</html>

