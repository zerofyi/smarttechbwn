@php
    // Cart badge count: guest session cart + logged-in DB cart.
    // Never throws: CartService / cart routes may not exist yet (Lane C),
    // so everything below is guarded and falls back to 0.
    $cartCount = 0;
    try {
        $guestCart = session('guest_cart', []);
        if (is_array($guestCart)) {
            $cartCount += array_sum(array_map('intval', $guestCart));
        }
        if (auth()->check()
            && class_exists(\App\Models\UserCart::class)
            && \Illuminate\Support\Facades\Schema::hasTable('user_carts')) {
            $dbCart = auth()->user()->cart;
            if ($dbCart) {
                $cartCount += (int) $dbCart->items()->sum('qty');
            }
        }
    } catch (\Throwable $e) {
        $cartCount = 0;
    }
@endphp
<header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    style="background: linear-gradient(135deg, #6B1A00 0%, #8B2500 60%, #A83010 100%); box-shadow: 0 2px 16px rgba(107,26,0,0.35);">
    <div class="desktop-container px-4 py-3 flex items-center justify-between">

        <!-- Logo / Back -->
        <div class="flex items-center">
            @if(request()->is('/'))
                <div class="flex flex-col leading-none">
                    <span class="text-xl font-bold tracking-widest text-white" style="letter-spacing: 3px;">SMART TECH</span>
                    <span class="text-xs tracking-widest" style="color: rgba(255,220,200,0.75); letter-spacing: 2px; font-size: 9px;">MOBILE STORE</span>
                </div>
            @else
                <div class="flex items-center">
                    <a href="javascript:history.back()"
                        class="mr-3 md:hidden flex items-center justify-center w-8 h-8 rounded-full hover:bg-white/10 transition"
                        style="color: rgba(255,220,200,0.9);">
                        <i class="fas fa-arrow-left text-base"></i>
                    </a>
                    <a href="{{ route('index') }}" class="flex flex-col leading-none">
                        <span class="text-xl font-bold tracking-widest text-white" style=" letter-spacing: 3px;">SMART TECH</span>
                        <span class="text-xs" style="color: rgba(255,220,200,0.75); letter-spacing: 2px; font-size: 9px;">MOBILE STORE</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Search Bar (Desktop) -->
        <div class="hidden md:block w-2/5 mx-6">
            <form method="GET" action="{{ route('products') }}">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4" style="color: #B88A7A;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" placeholder="Search brand, model..."
                        value="{{ request('search') }}"
                        class="block w-full pl-10 pr-10 py-2 text-sm rounded-lg border-0 outline-none transition"
                        style="background: rgba(255,255,255,0.12); color: white; border: 1px solid rgba(255,200,180,0.25);"
                        onfocus="this.style.background='rgba(255,255,255,0.2)'; this.style.borderColor='rgba(255,200,180,0.6)';"
                        onblur="this.style.background='rgba(255,255,255,0.12)'; this.style.borderColor='rgba(255,200,180,0.25)';">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 transition" style="color: rgba(255,220,200,0.8);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Icons -->
        <div class="flex items-center space-x-2">
            @guest
                <!-- Login -->
                <a href="{{ route('login') }}"
                    class="hidden md:flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold transition hover:bg-white/10"
                    style="color: rgba(255,220,200,0.9); border: 1px solid rgba(255,200,180,0.3);">
                    <i class="far fa-user text-sm"></i>
                    <span>Login</span>
                </a>
                <!-- Mobile login icon -->
                <a href="{{ route('login') }}"
                    class="md:hidden flex items-center justify-center w-9 h-9 rounded-full hover:bg-white/10 transition"
                    style="color: rgba(255,220,200,0.9);">
                    <i class="far fa-user text-base"></i>
                </a>
            @endguest
            @auth
                <!-- My Account -->
                <a href="{{ route('account.dashboard') }}"
                    class="hidden md:flex items-center gap-1.5 rounded-full text-sm font-semibold transition hover:bg-white/10"
                    style="color: rgba(255,220,200,0.9); border: 1px solid rgba(255,200,180,0.3);">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold"
                        style="background: rgba(255,255,255,0.2); color: white;">{{ auth()->user()->initials() }}</span>
                    <span class="py-1.5 pr-4">My Account</span>
                </a>
                <!-- Mobile avatar icon -->
                <a href="{{ route('account.dashboard') }}"
                    class="md:hidden flex items-center justify-center w-9 h-9 rounded-full transition hover:bg-white/10"
                    style="background: rgba(255,255,255,0.2); color: white;">
                    <span class="text-xs font-bold">{{ auth()->user()->initials() }}</span>
                </a>
                <!-- Orders -->
                <a href="{{ route('account.orders.index') }}"
                    class="hidden md:flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold transition hover:bg-white/10"
                    style="color: rgba(255,220,200,0.9); border: 1px solid rgba(255,200,180,0.3);">
                    <i class="fas fa-box-open text-sm"></i>
                    <span>Orders</span>
                </a>
                <!-- Logout -->
                {{-- <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-center w-9 h-9 md:w-auto md:px-4 md:py-1.5 rounded-full text-sm font-semibold transition hover:bg-white/10"
                        style="color: rgba(255,220,200,0.9); border: 1px solid rgba(255,200,180,0.3);"
                        title="Logout">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                        <span class="hidden md:inline ml-1.5">Logout</span>
                    </button>
                </form> --}}
            @endauth
            <!-- Cart -->
            <a href="{{ route('cart.index') }}"
                class="relative flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold transition"
                style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,200,180,0.3);"
                onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                <i class="fas fa-shopping-cart text-sm"></i>
                <span class="hidden md:inline">Cart</span>
                @if($cartCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[11px] font-bold"
                        style="background: #C68A40; color: white;">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- Bottom accent line -->
    <div style="height: 2px; background: linear-gradient(90deg, #C68A40 0%, #E8714A 50%, #C68A40 100%);"></div>
</header>
