@extends('layouts.guest')
@section('title', 'Explore')
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

    .section-title-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
        flex-shrink: 0;
    }

    .view-all-btn {
        display: inline-block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--rb-accent);
        border: 1px solid var(--rb-accent);
        padding: 4px 14px;
        border-radius: 20px;
        transition: all 0.2s;
        text-decoration: none;
    }
    .view-all-btn:hover {
        background: var(--rb-accent);
        color: white;
    }

    .rb-card {
        border: 1px solid var(--rb-warm-border);
        border-radius: 12px;
        transition: box-shadow 0.2s, border-color 0.2s, transform 0.2s;
        background: white;
    }
    .rb-card:hover {
        box-shadow: 0 4px 18px rgba(139,37,0,0.1);
        border-color: var(--rb-warm-border-mid);
        transform: translateY(-1px);
    }

    .spec-tag {
        display: inline-block;
        font-size: 11px;
        padding: 3px 9px;
        border-radius: 20px;
        background: var(--rb-warm-bg);
        color: var(--rb-mid);
        border: 1px solid var(--rb-warm-border);
        transition: all 0.15s;
        text-decoration: none;
        font-weight: 500;
    }
    .spec-tag:hover {
        background: var(--rb-accent);
        color: white;
        border-color: var(--rb-accent);
    }

    .spec-icon-bg {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
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

    .rb-section {
        background: white;
        margin-top: 8px;
        border-top: 3px solid var(--rb-warm-border);
        padding: 20px 16px;
    }
</style>

    <!-- Explore Header -->
    <section class="relative overflow-hidden text-white text-center py-10 px-4"
        style="background: linear-gradient(135deg, var(--rb-dark) 0%, var(--rb-primary) 55%, var(--rb-accent) 100%);">
        <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full opacity-10" style="background: white;"></div>
        <div class="absolute -bottom-8 right-10 w-28 h-28 rounded-full opacity-10" style="background: white;"></div>
        <div class="relative z-10">
            <p class="text-xs font-bold tracking-widest mb-1" style="color: var(--rb-gold-light); letter-spacing: 3px;">DISCOVER</p>
            <h1 class="text-2xl md:text-3xl font-bold" style="">Explore Our Collection</h1>
            <p class="mt-2 text-sm" style="color: rgba(255,220,200,0.85);">Browse by brands, categories or specifications</p>
            <a href="{{ route('products') }}"
                class="inline-block mt-5 px-7 py-2.5 rounded-full text-sm font-bold transition"
                style="background: white; color: var(--rb-primary);"
                onmouseover="this.style.background='var(--rb-gold-light)'"
                onmouseout="this.style.background='white'">
                View All Products →
            </a>
        </div>
    </section>

    <!-- Popular Brands -->
    <section class="rb-section desktop-container">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title-pill">Popular Brands</h2>
        </div>
        <div class="grid grid-cols-4 md:grid-cols-8 gap-3">
            @foreach($popularBrands as $brand)
            <a href="{{ route('products', ['brand' => $brand->slug]) }}"
                class="rb-card flex flex-col items-center p-2 cursor-pointer"
                style="background: var(--rb-warm-bg);">
                <img src="{{ asset('storage/' . $brand->logo_url) }}" alt="{{ $brand->name }}"
                    class="w-12 h-12 md:w-14 md:h-14 object-contain">
                <span class="text-xs mt-1.5 text-center font-medium" style="color: #5C3020;">{{ $brand->name }}</span>
            </a>
            @endforeach
        </div>
    </section>

    <!-- Popular Categories -->
    <section class="rb-section desktop-container">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title-pill">Popular Categories</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($mainCategories as $category)
            <a href="{{ route('products', ['category' => $category->slug]) }}"
                class="rb-card flex flex-col items-center p-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-3"
                    style="background: linear-gradient(135deg, var(--rb-warm-bg), var(--rb-gold-light)); border: 1px solid var(--rb-warm-border-mid);">
                    @if($category->icon_url)
                        <img src="{{ asset('storage/' . $category->icon_url) }}" alt="{{ $category->name }}" class="w-8 h-8 object-contain">
                    @else
                        <i class="fas fa-mobile-alt" style="color: var(--rb-accent); font-size: 20px;"></i>
                    @endif
                </div>
                <span class="text-sm font-semibold text-center" style="color: var(--rb-dark);">{{ $category->name }}</span>
                <span class="text-xs mt-1" style="color: var(--rb-light);">({{ $category->products_count }} products)</span>
            </a>
            @endforeach
        </div>

        <!-- Subcategories -->
        @foreach($mainCategories as $category)
            @if($category->children->count() > 0)
            <div class="mt-6">
                <h3 class="text-sm font-semibold mb-3 flex items-center gap-2" style="color: var(--rb-mid);">
                    <span style="width: 3px; height: 14px; background: var(--rb-accent); display: inline-block; border-radius: 2px;"></span>
                    {{ $category->name }} Subcategories
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($category->children as $subcategory)
                    <a href="{{ route('products', ['category' => $subcategory->slug]) }}"
                        class="rb-card flex items-center p-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center mr-3 flex-shrink-0"
                            style="background: var(--rb-warm-bg); border: 1px solid var(--rb-warm-border);">
                            @if($subcategory->icon_url)
                                <img src="{{ asset('storage/' . $subcategory->icon_url) }}" alt="{{ $subcategory->name }}" class="w-5 h-5 object-contain">
                            @else
                                <i class="fas fa-mobile text-sm" style="color: var(--rb-accent);"></i>
                            @endif
                        </div>
                        <div>
                            <span class="text-sm font-semibold block" style="color: var(--rb-dark);">{{ $subcategory->name }}</span>
                            <span class="text-xs" style="color: var(--rb-light);">{{ $subcategory->products_count }} products</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </section>

    <!-- Shop by Specifications -->
    <section class="rb-section desktop-container">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title-pill">Shop by Specifications</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- RAM -->
            <div class="rb-card p-4">
                <div class="flex items-center mb-3 gap-3">
                    <div class="spec-icon-bg" style="background: linear-gradient(135deg, #FDF0EB, #F5D5C5);">
                        <i class="fas fa-memory" style="color: var(--rb-accent);"></i>
                    </div>
                    <span class="text-sm font-semibold" style="color: var(--rb-dark);">RAM</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($ramOptions as $ram)
                    <a href="{{ route('products', ['attribute' => 'ram', 'value' => $ram]) }}" class="spec-tag">{{ $ram }}GB</a>
                    @endforeach
                </div>
            </div>

            <!-- Storage -->
            <div class="rb-card p-4">
                <div class="flex items-center mb-3 gap-3">
                    <div class="spec-icon-bg" style="background: linear-gradient(135deg, #F5EBF5, #E8D0E8);">
                        <i class="fas fa-database" style="color: #8B3090;"></i>
                    </div>
                    <span class="text-sm font-semibold" style="color: var(--rb-dark);">Storage</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($storageOptions as $storage)
                    <a href="{{ route('products', ['attribute' => 'storage', 'value' => $storage]) }}" class="spec-tag">{{ $storage }}GB</a>
                    @endforeach
                </div>
            </div>

            <!-- Condition -->
            <div class="rb-card p-4">
                <div class="flex items-center mb-3 gap-3">
                    <div class="spec-icon-bg" style="background: linear-gradient(135deg, #EBF5EB, #C5E8C5);">
                        <i class="fas fa-certificate" style="color: #2E7D32;"></i>
                    </div>
                    <span class="text-sm font-semibold" style="color: var(--rb-dark);">Condition</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($conditionOptions as $condition)
                    <a href="{{ route('products', ['health' => $condition['value']]) }}" class="spec-tag">{{ $condition['label'] }}</a>
                    @endforeach
                </div>
            </div>

            <!-- Warranty -->
            <div class="rb-card p-4">
                <div class="flex items-center mb-3 gap-3">
                    <div class="spec-icon-bg" style="background: linear-gradient(135deg, var(--rb-gold-light), #EDD8A0);">
                        <i class="fas fa-shield-alt" style="color: var(--rb-gold);"></i>
                    </div>
                    <span class="text-sm font-semibold" style="color: var(--rb-dark);">Warranty</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($warrantyOptions as $warranty)
                    <a href="{{ route('products', ['warranty' => $warranty]) }}" class="spec-tag">{{ $warranty }} mo</a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Shop by Color -->
    <section class="rb-section desktop-container">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title-pill">Shop by Color</h2>
        </div>
        <div class="grid grid-cols-6 md:grid-cols-10 gap-3">
            @foreach($colors as $color)
            <a href="{{ route('products', ['color' => $color['value']]) }}" class="flex flex-col items-center gap-1 group">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-full border-2 cursor-pointer transition group-hover:scale-110 group-hover:shadow-md"
                    style="background-color: {{ $color['hex'] }}; border-color: var(--rb-warm-border-mid);"></div>
                <span class="text-xs text-center" style="color: var(--rb-mid);">{{ $color['name'] }}</span>
            </a>
            @endforeach
        </div>
    </section>

    <!-- Shop by Price -->
    <section class="rb-section desktop-container">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title-pill">Shop by Price</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($priceRanges as $range)
            <a href="{{ route('products', ['min_price' => $range['min'], 'max_price' => $range['max']]) }}"
                class="rb-card p-4 flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3"
                    style="background: linear-gradient(135deg, var(--rb-warm-bg), var(--rb-gold-light)); border: 1px solid var(--rb-warm-border-mid);">
                    <i class="fas fa-rupee-sign" style="color: var(--rb-accent);"></i>
                </div>
                <span class="text-sm font-semibold" style="color: var(--rb-dark);">
                    @if($range['min'] === null)
                        Under ₹{{ number_format($range['max']) }}
                    @elseif($range['max'] === null)
                        Above ₹{{ number_format($range['min']) }}
                    @else
                        ₹{{ number_format($range['min']) }} – ₹{{ number_format($range['max']) }}
                    @endif
                </span>
                <span class="text-xs mt-1" style="color: var(--rb-light);">{{ $range['label'] }}</span>
            </a>
            @endforeach
        </div>
    </section>

    <!-- Featured Products -->
    <section class="rb-section desktop-container">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title-pill">Featured Products</h2>
            <a href="{{ route('products') }}" class="view-all-btn">View All</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($featuredProducts as $inventory)
            <a href="{{ route('product.show', ['product' => $inventory->product->slug, 'inventory' => $inventory->inventory_id]) }}"
                class="product-card rb-card overflow-hidden relative">
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
