<header class="border-b border-stone-200/90 bg-stone-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between text-sm text-slate-800">
            <!-- Brand -->
            <a href="{{ route('homepage') ?? '#' }}" class="font-semibold tracking-tight text-slate-900">
                ShopDemo
            </a>

            <!-- Main nav -->
            <nav class="hidden md:flex items-center gap-6 text-slate-700">
                <a href="{{ route('homepage') ?? '#' }}" class="hover:text-slate-900 hover:underline">
                    Home
                </a>
                <a href="{{ route('products.index') ?? '#' }}" class="hover:text-slate-900 hover:underline">
                    Products
                </a>
            </nav>

            <!-- Auth + cart -->
            <div class="flex items-center gap-4 text-slate-700">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') ?? '#' }}" target="_blank" class="hidden sm:inline hover:text-slate-900 hover:underline">
                            Dashboard
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hidden sm:inline hover:text-slate-900 hover:underline">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') ?? '#' }}" class="hidden sm:inline hover:text-slate-900 hover:underline">
                        Login
                    </a>
                    
                    <a href="{{ route('register') ?? '#' }}" class="hidden sm:inline hover:text-slate-900 hover:underline">
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

