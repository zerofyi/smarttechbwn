@extends('layouts.store')
@section('title', 'Home')

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
        }

        .section-title-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            font-size: 1rem;
            color: var(--rb-dark);
        }
        .section-title-pill::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 18px;
            background: linear-gradient(180deg, var(--rb-accent), var(--rb-gold));
            border-radius: 2px;
        }

        .view-all-link {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--rb-accent);
            border: 1px solid var(--rb-accent);
            padding: 3px 12px;
            border-radius: 20px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .view-all-link:hover {
            background: var(--rb-accent);
            color: white;
        }

        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139,37,0,0.12) !important;
        }

        .discount-badge {
            background: linear-gradient(135deg, var(--rb-primary), var(--rb-accent));
        }

        .brand-card:hover {
            box-shadow: 0 4px 16px rgba(139,37,0,0.12);
            border-color: var(--rb-warm-border) !important;
        }

        .flash-timer {
            background: linear-gradient(135deg, var(--rb-primary), var(--rb-accent));
        }

        .search-input:focus {
            border-color: var(--rb-accent) !important;
            box-shadow: 0 0 0 2px rgba(196,74,32,0.2) !important;
        }
    </style>

    <!-- Search Bar (Mobile) -->
    <section class="px-4 py-3 bg-white shadow-sm md:hidden" style="border-bottom: 2px solid var(--rb-warm-border);">
        <form method="GET" action="{{ route('products') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4" style="color: #B88A7A;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input type="text" name="search" placeholder="Search by brand, model..."
                    value="{{ request('search') }}"
                    class="search-input block w-full pl-10 pr-10 py-2 text-sm border rounded-lg outline-none transition"
                    style="border-color: var(--rb-warm-border); background: var(--rb-warm-bg);">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: var(--rb-accent);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </section>

    <!-- Main Banner Slider -->
    <section class="relative mt-2 overflow-hidden desktop-container" style="border-radius: 0;">
        <div class="flex banner-container scroll-container overflow-x-auto snap-x snap-mandatory">
            @foreach($mainBanners as $banner)
            <div class="banner-slide flex-shrink-0 w-full snap-start">
                <a href="{{ $banner->target_url }}" class="block">
                    <img src="{{ asset('storage/' . $banner->image_url) }}"
                        alt="{{ $banner->title }}"
                        class="w-full object-cover mx-auto">
                </a>
            </div>
            @endforeach
        </div>
        @if($mainBanners->count() > 1)
        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
            @foreach($mainBanners as $index => $banner)
            <button class="banner-dot w-2 h-2 rounded-full {{ $index === 0 ? 'opacity-100' : 'opacity-40' }} transition"
                style="background: white;"></button>
            @endforeach
        </div>
        @endif
    </section>

    <!-- Popular Brands -->
    <section class="px-4 py-5 bg-white mt-2 desktop-container" style="border-top: 3px solid var(--rb-warm-border);">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title-pill">Popular Brands</h2>
        </div>
        <div class="grid grid-cols-4 md:grid-cols-8 gap-3">
            @foreach($brands as $brand)
            <a href="{{ route('products', ['brand' => $brand->slug]) }}"
                class="brand-card flex flex-col items-center p-2 rounded-xl border transition cursor-pointer"
                style="border-color: var(--rb-warm-border); background: var(--rb-warm-bg);">
                <img src="{{ asset('storage/' . $brand->logo_url) }}" alt="{{ $brand->name }}"
                    class="w-full md:w-14 md:h-14 object-contain">
                <span class="text-xs mt-1.5 text-center font-medium" style="color: #5C3020;">{{ $brand->name }}</span>
            </a>
            @endforeach
        </div>
    </section>

    <!-- Top Products -->
    <section class="px-4 py-5 bg-white mt-2 desktop-container" style="border-top: 3px solid var(--rb-warm-border);">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title-pill">Top Products</h2>
            <a href="{{ route('products') }}" class="view-all-link">View All</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($topProducts as $inventory)
            <a href="{{ route('product.show', ['product' => $inventory->product->slug, 'inventory' => $inventory->inventory_id]) }}"
                class="product-card border rounded-xl overflow-hidden bg-white relative"
                style="border-color: var(--rb-warm-border);">
                <div class="relative">
                    <img src="{{ asset('storage/' . $inventory->product->img_one) }}" alt="{{ $inventory->product->name }}"
                        class="w-full h-40 md:h-48 object-contain p-4">
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
                    <div class="flex items-center mt-1">
                        <div class="flex text-xs" style="color: var(--rb-gold);">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-xs text-gray-400 ml-1">(124)</span>
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
    </section>

    <!-- Offer Banner -->
    <section class="px-4 py-5 mt-2 desktop-container">
        <div class="relative overflow-hidden rounded-2xl p-6 text-white"
            style="background: linear-gradient(135deg, var(--rb-dark) 0%, var(--rb-primary) 50%, var(--rb-accent) 100%);">
            <!-- Decorative circles -->
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full opacity-10" style="background: white;"></div>
            <div class="absolute -bottom-6 right-16 w-20 h-20 rounded-full opacity-10" style="background: white;"></div>
            <div class="relative z-10">
                <p class="text-xs font-bold tracking-widest mb-1" style="color: var(--rb-gold-light); letter-spacing: 3px;">LIMITED TIME</p>
                <h3 class="text-2xl md:text-3xl font-bold" style="">Festive Season Sale!</h3>
                <p class="text-sm mt-1" style="color: rgba(255,220,200,0.85);">Up to 40% off on all accessories</p>
                <button class="mt-4 px-6 py-2 rounded-full text-sm font-bold transition"
                    style="background: white; color: var(--rb-primary);"
                    onmouseover="this.style.background='var(--rb-gold-light)'"
                    onmouseout="this.style.background='white'">
                    Shop Now →
                </button>
            </div>
        </div>
    </section>

    <!-- Flash Sale -->
    <section class="px-4 py-5 bg-white mt-2 desktop-container" style="border-top: 3px solid var(--rb-warm-border);">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-3">
                <h2 class="section-title-pill">Flash Sale</h2>
                <span class="flash-timer text-white text-xs px-3 py-1 rounded-full font-bold tracking-wide">
                    ⏱ 02:15:30
                </span>
            </div>
            <a href="#" class="view-all-link">View All</a>
        </div>
        <div class="flex overflow-x-auto pb-2 space-x-3 scroll-container">
            @foreach($flashSaleProducts as $inventory)
            <a href="{{ route('product.show', ['product' => $inventory->product->slug, 'inventory' => $inventory->inventory_id]) }}"
                class="product-card flex-shrink-0 w-40 md:w-52 border rounded-xl overflow-hidden bg-white relative"
                style="border-color: var(--rb-warm-border);">
                <div class="relative">
                    <img src="{{ asset('storage/' . $inventory->product->img_one) }}" alt="{{ $inventory->product->name }}"
                        class="w-full h-32 md:h-40 object-contain p-3">
                    @if($inventory->product->mrp > $inventory->sell_rate)
                    <span class="discount-badge absolute top-2 left-2 text-white text-xs px-2 py-0.5 rounded-full font-semibold">
                        {{ round(($inventory->product->mrp - $inventory->sell_rate) / $inventory->product->mrp * 100) }}% OFF
                    </span>
                    @endif
                </div>
                <div class="p-3" style="border-top: 1px solid var(--rb-warm-border);">
                    <h3 class="text-sm font-semibold truncate" style="color: #2A1208;">{{ $inventory->product->name }}</h3>
                    <div class="flex items-baseline mt-1 gap-1.5">
                        <span class="text-sm font-bold" style="color: var(--rb-primary);">₹{{ number_format($inventory->sell_rate) }}</span>
                        @if($inventory->product->mrp > $inventory->sell_rate)
                        <span class="text-xs text-gray-400 line-through">₹{{ number_format($inventory->product->mrp) }}</span>
                        @endif
                    </div>
                    <div class="mt-1 text-xs font-semibold" style="color: #2E7D32;">✓ {{ ucfirst($inventory->health) }}</div>
                    <div class="mt-0.5 text-xs text-gray-500">{{ $inventory->color }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </section>

    <!-- Promo Banner -->
    @if($promoBanner)
    <section class="px-4 py-5 mt-2 desktop-container">
        <a href="{{ $promoBanner->target_url }}" class="block rounded-xl overflow-hidden"
            style="box-shadow: 0 4px 20px rgba(139,37,0,0.15);">
            <img src="{{ asset('storage/' . $promoBanner->image_url) }}"
                alt="{{ $promoBanner->title }}"
                class="w-full">
        </a>
    </section>
    @endif

    <!-- Best Sellers -->
    <section class="px-4 py-5 bg-white mt-2 desktop-container" style="border-top: 3px solid var(--rb-warm-border);">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title-pill">Best Sellers</h2>
            <a href="{{ route('products') }}" class="view-all-link">View All</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($bestSellers as $inventory)
            <a href="{{ route('product.show', ['product' => $inventory->product->slug, 'inventory' => $inventory->inventory_id]) }}"
                class="product-card border rounded-xl overflow-hidden bg-white relative"
                style="border-color: var(--rb-warm-border);">
                <div class="relative">
                    <img src="{{ asset('storage/' . $inventory->product->img_one) }}" alt="{{ $inventory->product->name }}"
                        class="w-full h-40 md:h-48 object-contain p-4">
                </div>
                <div class="p-3" style="border-top: 1px solid var(--rb-warm-border);">
                    <h3 class="text-sm font-semibold truncate" style="color: #2A1208;">{{ $inventory->product->name }}</h3>
                    <div class="flex items-baseline mt-1">
                        <span class="text-sm font-bold" style="color: var(--rb-primary);">₹{{ number_format($inventory->sell_rate) }}</span>
                    </div>
                    <div class="flex items-center mt-1">
                        <div class="flex text-xs" style="color: var(--rb-gold);">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-xs text-gray-400 ml-1">(215)</span>
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
    </section>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bannerContainer = document.querySelector('.banner-container');
            const banners = document.querySelectorAll('.banner-slide');
            const dots = document.querySelectorAll('.banner-dot');
            let currentIndex = 0;

            if (banners.length > 1) {
                setInterval(() => {
                    currentIndex = (currentIndex + 1) % banners.length;
                    updateBanner(currentIndex);
                }, 5000);
            }

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => updateBanner(index));
            });

            function updateBanner(index) {
                currentIndex = index;
                bannerContainer.scrollTo({ left: banners[currentIndex].offsetLeft, behavior: 'smooth' });
                dots.forEach((dot, i) => {
                    dot.style.opacity = i === index ? '1' : '0.4';
                });
            }
        });
    </script>
@endsection
