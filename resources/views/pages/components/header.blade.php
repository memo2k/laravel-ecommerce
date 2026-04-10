<header
    class="border-b border-slate-200"
    style="background-color: var(--color-primary);"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between text-sm text-white">
            <!-- Brand -->
            <a href="{{ route('homepage') ?? '#' }}" class="font-semibold tracking-tight">
                ShopDemo
            </a>

            <!-- Main nav -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="{{ route('homepage') ?? '#' }}" class="hover:underline">
                    Home
                </a>
                <a href="{{ route('products.index') ?? '#' }}" class="hover:underline">
                    Products
                </a>
            </nav>

            <!-- Auth + cart -->
            <div class="flex items-center gap-4">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') ?? '#' }}" class="hidden sm:inline hover:underline text-white">
                            Dashboard
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hidden sm:inline hover:underline">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') ?? '#' }}" class="hidden sm:inline hover:underline">
                        Login
                    </a>
                    
                    <a href="{{ route('register') ?? '#' }}" class="hidden sm:inline hover:underline">
                        Register
                    </a>
                @endauth
                <div class="relative group" id="cart_preview">
                    @include('pages.components.cart_preview')
                </div>
            </div>
        </div>
    </div>
</header>

