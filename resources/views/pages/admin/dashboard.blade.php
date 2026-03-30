@extends('pages.admin.layout')

@section('title', 'Dashboard – ShopDemo Admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold tracking-tight text-white">Dashboard</h1>
        <p class="text-xs text-slate-400 mt-1">Welcome back. Here's an overview of your store.</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        @php
            $stats = [
                ['label' => 'Total Revenue',  'value' => '$12,480', 'change' => '+12%', 'up' => true],
                ['label' => 'Orders',          'value' => '156',     'change' => '+8%',  'up' => true],
                ['label' => 'Products',        'value' => '64',      'change' => '+3',   'up' => true],
                ['label' => 'Customers',       'value' => '1,024',   'change' => '+18%', 'up' => true],
            ];
        @endphp

        @foreach ($stats as $stat)
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ $stat['label'] }}</p>
                <p class="mt-2 text-2xl font-semibold text-white">{{ $stat['value'] }}</p>
                <p class="mt-1 text-xs {{ $stat['up'] ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $stat['change'] }} from last month
                </p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
        {{-- Recent orders --}}
        <div>
            <h2 class="text-sm font-semibold text-slate-100 mb-3">Recent orders</h2>
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 overflow-hidden text-sm">
                <table class="min-w-full">
                    <thead class="bg-slate-900 border-b border-slate-800 text-xs font-medium uppercase tracking-wide text-slate-400">
                    <tr class="text-left">
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                    @php
                        $orders = [
                            ['id' => '#1012', 'customer' => 'Alice Johnson',  'status' => 'Completed', 'total' => '$89.00'],
                            ['id' => '#1011', 'customer' => 'Bob Smith',      'status' => 'Processing','total' => '$142.50'],
                            ['id' => '#1010', 'customer' => 'Carol White',    'status' => 'Completed', 'total' => '$36.00'],
                            ['id' => '#1009', 'customer' => 'Dan Brown',      'status' => 'Pending',   'total' => '$215.00'],
                            ['id' => '#1008', 'customer' => 'Eve Davis',      'status' => 'Completed', 'total' => '$64.00'],
                        ];
                        $statusColors = [
                            'Completed'  => 'text-emerald-200 bg-emerald-500/20',
                            'Processing' => 'text-sky-200 bg-sky-500/20',
                            'Pending'    => 'text-amber-200 bg-amber-500/20',
                        ];
                    @endphp

                    @foreach ($orders as $order)
                        <tr class="hover:bg-slate-800/60 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-100">{{ $order['id'] }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $order['customer'] }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $statusColors[$order['status']] }}">
                                    {{ $order['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-slate-100">{{ $order['total'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sidebar widgets --}}
        <div class="space-y-6">
            <div>
                <h2 class="text-sm font-semibold text-slate-100 mb-3">Quick actions</h2>
                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 space-y-3">
                    <a href="#"
                       class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-slate-800 transition-colors"
                       style="background-color: var(--color-accent);">
                        <span class="text-base leading-none">+</span>
                        Add new product
                    </a>
                    <a href="#"
                       class="flex items-center gap-3 rounded-md border border-slate-700 px-3 py-2 text-sm font-medium text-slate-300 hover:bg-slate-800 transition-colors">
                        <span class="text-base leading-none">&#8599;</span>
                        View storefront
                    </a>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-slate-100 mb-3">Low stock alerts</h2>
                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 space-y-3 text-sm">
                    @php
                        $lowStock = [
                            ['name' => 'Wireless Headphones', 'stock' => 2],
                            ['name' => 'USB-C Hub',           'stock' => 4],
                            ['name' => 'Desk Lamp',           'stock' => 1],
                        ];
                    @endphp

                    @foreach ($lowStock as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-300">{{ $item['name'] }}</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium text-rose-200 bg-rose-500/20">
                                {{ $item['stock'] }} left
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
