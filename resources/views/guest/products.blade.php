@extends('layouts.guest')
@section('title', 'Products')
@section('content')

<style>
    :root {
        --rb-dark: #6B1A00;
        --rb-primary: #8B2500;
        --rb-mid: #A83010;
        --rb-accent: #C44A20;
        --rb-light: #E8714A;
        --rb-gold: #C68A40;
        --rb-gold-light: #F5E8D0;
        --rb-warm-bg: #FDF6F3;
        --rb-warm-border: #F0D8CE;
        --rb-warm-border-mid: #DEB8A8;
    }

    .search-input {
        background: var(--rb-warm-bg);
        border: 1px solid var(--rb-warm-border-mid);
        color: #2A1208;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-input:focus {
        outline: none;
        border-color: var(--rb-accent);
        box-shadow: 0 0 0 3px rgba(196,74,32,0.12);
    }
    .search-input::placeholder { color: #B88A7A; }

    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
        border: 1px solid var(--rb-warm-border-mid);
        background: white;
        color: var(--rb-mid);
    }
    .filter-btn:hover {
        border-color: var(--rb-accent);
        color: var(--rb-accent);
        background: var(--rb-warm-bg);
    }

    .sort-active { color: var(--rb-primary); background: var(--rb-warm-bg); }
    .sort-item { display: block; padding: 8px 16px; font-size: 13px; color: #5C3020; transition: background 0.15s; text-decoration: none; }
    .sort-item:hover { background: var(--rb-warm-bg); }
    .sort-item.active { background: var(--rb-warm-bg); color: var(--rb-primary); font-weight: 700; }

    .product-card {
        border: 1px solid var(--rb-warm-border);
        border-radius: 12px;
        overflow: hidden;
        background: white;
        transition: transform 0.2s, box-shadow 0.2s;
        display: block;
        text-decoration: none;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139,37,0,0.12);
        border-color: var(--rb-warm-border-mid);
    }

    .discount-badge {
        background: linear-gradient(135deg, var(--rb-primary), var(--rb-accent));
    }
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-8">

    <!-- Desktop Header -->
    <div class="mb-6 hidden md:block">
        <h1 class="text-2xl font-bold" style=" color: var(--rb-dark);">
            Premium Pre-owned Devices
        </h1>
        <p class="mt-1 text-sm" style="color: var(--rb-light);">Certified quality with warranty included</p>
        <div style="width: 48px; height: 3px; background: linear-gradient(90deg, var(--rb-accent), var(--rb-gold)); border-radius: 2px; margin-top: 8px;"></div>
    </div>

    <!-- Mobile Search -->
    <div class="md:hidden mb-4 sticky top-0 z-10 bg-white pt-2 pb-2">
        <form method="GET" action="{{ route('products') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4" style="color: #B88A7A;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input type="text" name="search" placeholder="Search by brand, model..."
                    value="{{ request('search') }}"
                    class="search-input block w-full pl-10 pr-10 py-2 text-sm rounded-lg">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: var(--rb-accent);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Desktop Filter/Search Bar -->
    <div class="hidden md:block mb-6 sticky top-0 z-10 rounded-xl p-4"
        style="background: white; border: 1px solid var(--rb-warm-border); box-shadow: 0 2px 12px rgba(139,37,0,0.07);">
        <div class="flex items-center gap-4">
            <!-- Search -->
            <div class="relative flex-1">
                <form method="GET" action="{{ route('products') }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4" style="color: #B88A7A;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" placeholder="Search by brand, model..."
                            value="{{ request('search') }}"
                            class="search-input block w-full pl-10 pr-10 py-2 text-sm rounded-lg">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: var(--rb-accent);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button onclick="openFilterMenu()" class="filter-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters
                </button>
                <div class="relative">
                    <button onclick="toggleSortMenu()" class="filter-btn sort-active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                        </svg>
                        @switch(request('sort', 'newest'))
                            @case('popular') Most Popular @break
                            @case('price_asc') Price: Low → High @break
                            @case('price_desc') Price: High → Low @break
                            @default Newest Arrivals
                        @endswitch
                    </button>
                    <div id="sortMenu" class="hidden absolute right-0 mt-2 w-52 rounded-xl overflow-hidden z-20"
                        style="background: white; border: 1px solid var(--rb-warm-border); box-shadow: 0 8px 24px rgba(139,37,0,0.12);">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}"
                            class="sort-item {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}">Newest Arrivals</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}"
                            class="sort-item {{ request('sort') == 'popular' ? 'active' : '' }}">Most Popular</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}"
                            class="sort-item {{ request('sort') == 'price_asc' ? 'active' : '' }}">Price: Low → High</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}"
                            class="sort-item {{ request('sort') == 'price_desc' ? 'active' : '' }}">Price: High → Low</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5 pb-16 md:pb-0">
        @foreach($inventories as $inventory)
        <a href="{{ route('product.show', ['product' => $inventory->product->slug, 'inventory' => $inventory->inventory_id]) }}"
            class="product-card">
            <div class="relative">
                <img src="{{ asset('storage/' . $inventory->product->img_one) }}" alt="{{ $inventory->product->name }}"
                    class="w-full h-40 md:h-48 object-contain p-4" loading="lazy">
                @if($inventory->product->mrp > $inventory->sell_rate)
                <span class="discount-badge absolute top-2 left-2 text-white text-xs px-2 py-0.5 rounded-full font-semibold">
                    {{ round(($inventory->product->mrp - $inventory->sell_rate) / $inventory->product->mrp * 100) }}% OFF
                </span>
                @endif
            </div>
            <div class="p-3" style="border-top: 1px solid var(--rb-warm-border);">
                <h3 class="text-sm font-semibold truncate" style="color: #2A1208;">{{ $inventory->product->name }}</h3>
                <div class="flex items-baseline mt-1 gap-2">
                    <span class="text-sm font-bold" style="color: var(--rb-primary);">₹{{ number_format($inventory->sell_rate) }}</span>
                    @if($inventory->product->mrp > $inventory->sell_rate)
                    <span class="text-xs text-gray-400 line-through">₹{{ number_format($inventory->product->mrp) }}</span>
                    @endif
                </div>
                <div class="mt-1.5 text-xs font-semibold" style="color: #2E7D32;">✓ {{ ucfirst($inventory->health) }}</div>
                <div class="mt-0.5 text-xs text-gray-500 truncate">
                    @foreach($inventory->product->productAttributes as $attr)
                        {{ $attr->attribute->name }}: {{ $attr->attributeValue->value }}@if(!$loop->last), @endif
                    @endforeach
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $inventories->links() }}
    </div>
</div>

<script>
    function toggleSortMenu() {
        document.getElementById('sortMenu').classList.toggle('hidden');
    }
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#sortMenu') && !event.target.closest('button[onclick="toggleSortMenu()"]')) {
            document.getElementById('sortMenu').classList.add('hidden');
        }
    });
    const searchInput = document.querySelector('input[name="search"]');
    function debounce(func, wait) {
        let timeout;
        return function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, arguments), wait);
        };
    }
    if (searchInput) {
        searchInput.addEventListener('keyup', debounce(function() { this.form.submit(); }, 500));
    }
</script>
@endsection
