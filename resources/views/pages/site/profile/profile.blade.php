@extends('pages.site.layout')

@section('title', 'My Profile – ShopDemo')

@section('content')
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <x-flash-messages show-validation-summary />

        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">My Profile</h1>
            <p class="mt-1 text-sm text-slate-500">
                View your account details, recent orders, and update your personal information.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">
            <aside class="rounded-xl border border-slate-200 bg-white p-4 h-fit">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">Navigation</p>
                <nav class="space-y-1 text-sm" id="profile-tabs-nav">
                    <button type="button" data-tab-target="overview" class="profile-tab-button w-full text-left rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100">Overview</button>
                    <button type="button" data-tab-target="orders" class="profile-tab-button w-full text-left rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100">My Orders</button>
                    <button type="button" data-tab-target="edit-profile" class="profile-tab-button w-full text-left rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100">Account Settings</button>
                </nav>
            </aside>

            <div class="space-y-6">
                <section id="overview" data-tab-panel class="rounded-xl border border-slate-200 bg-white p-5">
                    <h2 class="text-base font-semibold text-slate-900 mb-4">Account Overview</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Full Name</p>
                            <p class="mt-1 text-sm font-medium text-slate-900">{{ $user->name ?? 'N/A' }}</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Email</p>
                            <p class="mt-1 text-sm font-medium text-slate-900">{{ $user->email ?? 'N/A' }}</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Member Since</p>
                            <p class="mt-1 text-sm font-medium text-slate-900">
                                {{ optional($user->created_at)->format('M d, Y') ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Total Orders</p>
                            <p class="mt-1 text-sm font-medium text-slate-900">{{ $orders->count() }}</p>
                        </div>
                    </div>
                </section>

                <section id="orders" data-tab-panel class="rounded-xl border border-slate-200 bg-white p-5 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-semibold text-slate-900">Order History</h2>
                        <span class="text-xs text-slate-500">{{ $orders->count() }} total</span>
                    </div>

                    @if ($orders->isEmpty())
                        <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-sm text-slate-600">
                            You have not placed any orders yet.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                                        <th class="py-3 pr-3">Order #</th>
                                        <th class="py-3 pr-3">Date</th>
                                        <th class="py-3 pr-3">Status</th>
                                        <th class="py-3 text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr class="border-b border-slate-100">
                                            <td class="py-3 pr-3 text-slate-800">#{{ $order->id }}</td>
                                            <td class="py-3 pr-3 text-slate-600">{{ optional($order->created_at)->format('M d, Y') ?? 'N/A' }}</td>
                                            <td class="py-3 pr-3">
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                                    {{ $order->status ?? 'Pending' }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-right font-medium text-slate-900">
                                                ${{ number_format((float) ($order->total_price ?? $order->total ?? 0), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <section id="edit-profile" data-tab-panel class="rounded-xl border border-slate-200 bg-white p-5 hidden">
                    <h2 class="text-base font-semibold text-slate-900 mb-4">Edit Profile</h2>
                    <p class="text-xs text-slate-500 mb-4">
                        Front-end form only for now. Hook this section to your update route when ready.
                    </p>

                    <form class="space-y-4" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="profile_name" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                    Full Name
                                </label>
                                <input
                                    id="profile_name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                                >
                                <x-validation-error field="name" />
                            </div>
                            <div>
                                <label for="profile_email" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                    Email
                                </label>
                                <input
                                    id="profile_email"
                                    type="email"
                                    value="{{ old('email', $user->email) }}"
                                    name="email"
                                    class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                                >
                                <x-validation-error field="email" />
                            </div>
                        </div>

                        <div>
                            <label for="profile_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                Phone
                            </label>
                            <input
                                id="profile_phone"
                                type="tel"
                                value="{{ old('phone') }}"
                                placeholder="+1 555 0100"
                                class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                            >
                        </div>

                        <div class="flex justify-start gap-2 pt-2">
                            <button type="submit" class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm" style="background-color: var(--color-accent);">
                                Save Changes
                            </button>
                        </div>
                    </form>

                    <form class="mt-6 pt-6 border-t border-slate-200 space-y-4" method="POST" action="{{ route('profile.update-address') }}">
                        @csrf
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Delivery Address</h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Used for shipping your orders. Hook this form to your address update route when ready.
                            </p>
                        </div>

                        <div>
                            <label for="delivery_address" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                Street Address
                            </label>
                            <input
                                id="delivery_address"
                                type="text"
                                name="address"
                                value="{{ old('address', optional($userAddress ?? null)->address) }}"
                                class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                            >
                            <x-validation-error field="address" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="delivery_city" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                    City
                                </label>
                                <input
                                    id="delivery_city"
                                    type="text"
                                    name="city"
                                    value="{{ old('city', optional($userAddress ?? null)->city) }}"
                                    class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                                >
                                <x-validation-error field="city" />
                            </div>
                            <div>
                                <label for="delivery_state" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                    State / Province
                                </label>
                                <input
                                    id="delivery_state"
                                    type="text"
                                    name="state"
                                    value="{{ old('state', optional($userAddress ?? null)->state) }}"
                                    class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                                >
                                <x-validation-error field="state" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="delivery_zip" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                    ZIP / Postal Code
                                </label>
                                <input
                                    id="delivery_zip"
                                    type="text"
                                    name="zip"
                                    value="{{ old('zip', optional($userAddress ?? null)->zip) }}"
                                    class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                                >
                                <x-validation-error field="zip" />
                            </div>
                            <div>
                                <label for="delivery_country" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                    Country
                                </label>
                                <input
                                    id="delivery_country"
                                    type="text"
                                    name="country"
                                    value="{{ old('country', optional($userAddress ?? null)->country) }}"
                                    class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                                >
                                <x-validation-error field="country" />
                            </div>
                        </div>

                        <div>
                            <label for="delivery_phone" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                Phone
                            </label>
                            <input
                                id="delivery_phone"
                                type="tel"
                                name="phone"
                                value="{{ old('phone', optional($userAddress ?? null)->phone) }}"
                                placeholder="+1 555 0100"
                                class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                            >
                            <x-validation-error field="phone" />
                        </div>

                        <div class="flex justify-start gap-2 pt-2">
                            <button type="submit" class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm" style="background-color: var(--color-accent);">
                                Save Address
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 border-t border-slate-200 pt-5">
                        <h3 class="text-sm font-semibold text-red-600">Delete Account</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            This action is irreversible. Your profile and order history access will be removed.
                        </p>
                        <form class="mt-3 space-y-3" method="POST" action="{{ route('profile.delete-account') }}">
                            @csrf
                            @method('DELETE')
                            <div>
                                <label for="delete_account_password" class="block text-xs font-medium uppercase tracking-wide text-slate-700 mb-1">
                                    Confirm Password
                                </label>
                                <input
                                    id="delete_account_password"
                                    type="password"
                                    name="password"
                                    autocomplete="current-password"
                                    required
                                    class="block w-full max-w-sm rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                                >
                                @error('password', 'userDeletion')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">
                                Delete Account
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = Array.from(document.querySelectorAll('.profile-tab-button'));
            const panels = Array.from(document.querySelectorAll('[data-tab-panel]'));

            if (!buttons.length || !panels.length) {
                return;
            }

            const setActiveTab = function (targetId) {
                panels.forEach(function (panel) {
                    panel.classList.toggle('hidden', panel.id !== targetId);
                });

                buttons.forEach(function (button) {
                    const isActive = button.dataset.tabTarget === targetId;
                    button.classList.toggle('bg-slate-100', isActive);
                    button.classList.toggle('text-slate-900', isActive);
                    button.classList.toggle('font-medium', isActive);
                });
            };

            const validPanelIds = panels.map(function (panel) {
                return panel.id;
            });

            let initialTab = 'overview';
            const hashTab = window.location.hash.replace('#', '');
            if (validPanelIds.includes(hashTab)) {
                initialTab = hashTab;
            }

            setActiveTab(initialTab);

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const targetId = button.dataset.tabTarget;
                    if (!validPanelIds.includes(targetId)) {
                        return;
                    }

                    setActiveTab(targetId);
                    history.replaceState(null, '', '#' + targetId);
                });
            });
        });
    </script>
@endsection
