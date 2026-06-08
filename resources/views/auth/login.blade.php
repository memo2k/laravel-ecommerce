<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm dark:border-amber-800 dark:bg-amber-900/20">
        <p class="mb-2 font-medium text-amber-900 dark:text-amber-200">Demo admin credentials</p>
        <div class="space-y-2 text-amber-800 dark:text-amber-300">
            <div class="flex items-center justify-between gap-2">
                <span>Email: <code id="demo-email" class="rounded bg-amber-100 px-1.5 py-0.5 dark:bg-amber-900/40">admin@site.com</code></span>
                <button type="button" data-copy-target="demo-email" class="shrink-0 rounded border border-amber-300 px-2 py-0.5 text-xs hover:bg-amber-100 dark:border-amber-700 dark:hover:bg-amber-900/40">
                    Copy
                </button>
            </div>
            <div class="flex items-center justify-between gap-2">
                <span>Password: <code id="demo-password" class="rounded bg-amber-100 px-1.5 py-0.5 dark:bg-amber-900/40">Pl42@sa!</code></span>
                <button type="button" data-copy-target="demo-password" class="shrink-0 rounded border border-amber-300 px-2 py-0.5 text-xs hover:bg-amber-100 dark:border-amber-700 dark:hover:bg-amber-900/40">
                    Copy
                </button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.querySelectorAll('[data-copy-target]').forEach((button) => {
            button.addEventListener('click', async () => {
                const text = document.getElementById(button.dataset.copyTarget)?.textContent?.trim();

                if (!text) {
                    return;
                }

                await navigator.clipboard.writeText(text);
                const originalLabel = button.textContent;
                button.textContent = 'Copied!';
                setTimeout(() => {
                    button.textContent = originalLabel;
                }, 1500);
            });
        });
    </script>
</x-guest-layout>
