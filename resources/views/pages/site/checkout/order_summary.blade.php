@extends('pages.site.layout')

@section('title', 'Order confirmed – ShopDemo')

@section('content')
    @php
        $contactEmail = config('mail.from.address');
    @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="text-center mb-10">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 ring-4 ring-emerald-50">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-slate-900">
                Thank you for your order
            </h1>
            <p class="mt-3 text-sm sm:text-base text-slate-600 max-w-lg mx-auto leading-relaxed">
                We have received your order and sent a confirmation to
                <span class="font-medium text-slate-800">{{ $order->customer_email }}</span>.
                You will get another email when your order ships.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-slate-50 to-white border-b border-slate-200 px-5 sm:px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Order number</p>
                    <p class="text-xl font-semibold text-slate-900 tabular-nums">#{{ $order->id }}</p>
                </div>
                <div class="sm:text-right text-sm text-slate-600">
                    <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Placed on</p>
                    <p class="font-medium text-slate-900">
                        {{ $order->created_at?->timezone(config('app.timezone'))->format('M j, Y') }}
                    </p>
                    <p class="text-slate-500">{{ $order->created_at?->timezone(config('app.timezone'))->format('g:i A') }}</p>
                </div>
            </div>

            <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-4">Order details</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-slate-500">Payment</dt>
                        <dd class="mt-0.5 font-medium text-slate-900">{{ $order->payment_method ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Status</dt>
                        <dd class="mt-0.5">
                            @php
                                $status = $order->status ?? 'Pending';
                                $statusBadgeClass = match ($status) {
                                    'Delivered' => 'text-emerald-900 bg-emerald-100 ring-emerald-600/15',
                                    'Shipped', 'Processing' => 'text-sky-900 bg-sky-100 ring-sky-600/15',
                                    'Pending' => 'text-amber-900 bg-amber-100 ring-amber-600/15',
                                    'Unpaid', 'Cancelled' => 'text-rose-900 bg-rose-100 ring-rose-600/15',
                                    default => 'text-slate-800 bg-slate-100 ring-slate-400/20',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusBadgeClass }}">
                                {{ $status }}
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500">Ship to</dt>
                        <dd class="mt-0.5 font-medium text-slate-900 leading-relaxed">
                            {{ $order->customer_first_name }} {{ $order->customer_last_name }}<br>
                            <span class="font-normal text-slate-700">
                                {{ $order->delivery_address }}<br>
                                {{ $order->city }}{{ $order->zip ? ', '.$order->zip : '' }}{{ $order->country ? ' · '.$order->country : '' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="px-5 sm:px-6 py-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-4">Items</h2>
                <ul class="divide-y divide-slate-100">
                    @forelse ($order->orderProducts as $line)
                        <li class="flex gap-4 py-4 first:pt-0">
                            <div class="h-14 w-14 flex-shrink-0 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden">
                                @if ($line->product && !empty($line->product->image))
                                    <img src="{{ asset('storage/'.ltrim($line->product->image, '/')) }}" alt=""
                                         class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-slate-900 truncate">
                                    {{ $line->product->name ?? 'Product #'.$line->product_id }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">Qty {{ $line->quantity }} × ${{ number_format((float) $line->price, 2) }}</p>
                            </div>
                            <p class="text-sm font-semibold text-slate-900 tabular-nums flex-shrink-0">
                                ${{ number_format((float) ($line->total ?? $line->price * $line->quantity), 2) }}
                            </p>
                        </li>
                    @empty
                        <li class="py-6 text-center text-sm text-slate-500">No line items on this order.</li>
                    @endforelse
                </ul>

                <div class="mt-5 pt-5 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-900">Total</span>
                    <span class="text-lg font-semibold text-slate-900 tabular-nums">
                        ${{ number_format((float) ($order->total_amount ?? 0), 2) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-5 py-4 text-sm text-slate-600 mb-8">
            <p class="font-medium text-slate-800 mb-1">Questions about your order?</p>
            <p>
                @if ($contactEmail)
                    Contact us at
                    <a href="mailto:{{ $contactEmail }}" class="text-slate-900 underline decoration-slate-300 underline-offset-2 hover:decoration-slate-600">{{ $contactEmail }}</a>.
                @else
                    Reach out through the contact options on our site.
                @endif
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:justify-center">
            <a href="{{ route('products.index') }}"
               class="inline-flex justify-center items-center rounded-lg px-5 py-2.5 text-sm font-medium text-white shadow-sm"
               style="background-color: var(--color-accent);">
                Continue shopping
            </a>
            <a href="{{ route('homepage') }}"
               class="inline-flex justify-center items-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                Back to home
            </a>
        </div>
    </div>
@endsection
