@extends('layouts.guest')
@section('title', $inventory->product->name)
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

    .product-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .product-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(139,37,0,0.12) !important; }

    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .active\:scale-95:active { transform: scale(0.95); }

    .breadcrumb-link {
        color: #8A5A48;
        font-size: 12px;
        transition: color 0.15s;
        text-decoration: none;
    }
    .breadcrumb-link:hover { color: var(--rb-accent); }

    .badge-brand { background: var(--rb-warm-bg); color: var(--rb-mid); border: 1px solid var(--rb-warm-border-mid); }
    .badge-type  { background: #EBF5EB; color: #2E7D32; border: 1px solid #C5E8C5; }
    .badge-discount { background: linear-gradient(135deg, var(--rb-primary), var(--rb-accent)); color: white; border: none; }

    .rb-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .thumb-item {
        border: 1.5px solid var(--rb-warm-border);
        border-radius: 8px;
        padding: 6px;
        cursor: pointer;
        transition: border-color 0.15s;
        background: white;
    }
    .thumb-item:hover, .thumb-item.active { border-color: var(--rb-accent); }

    .tab-btn {
        padding: 12px 20px;
        font-size: 13px;
        font-weight: 600;
        border-bottom: 2px solid transparent;
        color: #8A5A48;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        background: none;
        border-left: none; border-right: none; border-top: none;
    }
    .tab-btn.active {
        border-bottom-color: var(--rb-accent);
        color: var(--rb-primary);
    }
    .tab-btn:hover:not(.active) { color: var(--rb-mid); }

    .add-to-cart-btn {
        background: linear-gradient(135deg, var(--rb-primary), var(--rb-mid));
        color: white;
        transition: opacity 0.2s, transform 0.1s;
    }
    .add-to-cart-btn:hover { opacity: 0.9; }
    .add-to-cart-btn:active { transform: scale(0.97); }

    .buy-now-btn {
        background: var(--rb-dark);
        color: white;
        transition: opacity 0.2s, transform 0.1s;
    }
    .buy-now-btn:hover { opacity: 0.85; }
    .buy-now-btn:active { transform: scale(0.97); }

    .spec-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; }
    .spec-item span:first-child { font-weight: 600; color: var(--rb-mid); }
    .spec-item span:last-child { color: #2A1208; }

    .discount-badge { background: linear-gradient(135deg, var(--rb-primary), var(--rb-accent)); }
</style>

<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-3 md:py-6">

    <!-- Breadcrumb -->
    <nav class="flex mb-4 overflow-x-auto py-2 hide-scrollbar">
        <ol class="inline-flex items-center whitespace-nowrap space-x-2">
            <li>
                <a href="{{ route('index') }}" class="breadcrumb-link inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Home
                </a>
            </li>
            @foreach([['route' => route('products'), 'label' => 'Products'], ['route' => route('products', ['category' => $inventory->product->category->slug]), 'label' => $inventory->product->category->name]] as $crumb)
            <li class="flex items-center">
                <svg class="w-3 h-3 mx-1" style="color: var(--rb-warm-border-mid);" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 9 4-4-4-4"/>
                </svg>
                <a href="{{ $crumb['route'] }}" class="breadcrumb-link ml-1">{{ $crumb['label'] }}</a>
            </li>
            @endforeach
            <li class="flex items-center">
                <svg class="w-3 h-3 mx-1" style="color: var(--rb-warm-border-mid);" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 9 4-4-4-4"/>
                </svg>
                <span class="ml-1 text-xs" style="color: var(--rb-light);">{{ $inventory->product->name }}</span>
            </li>
        </ol>
    </nav>

    <!-- Product Section -->
    <div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid var(--rb-warm-border); box-shadow: 0 2px 16px rgba(139,37,0,0.07);">
        <div class="flex flex-col lg:grid lg:grid-cols-2 gap-4 md:gap-6 p-3 md:p-6">

            <!-- Images -->
            <div class="w-full">
                <div class="rounded-xl mb-3 flex items-center justify-center p-4 h-64 sm:h-80 md:h-96"
                    style="background: var(--rb-warm-bg); border: 1px solid var(--rb-warm-border);">
                    <img id="mainImage" src="{{ asset('storage/' . $inventory->product->img_one) }}"
                        alt="{{ $inventory->product->name }}" class="h-full object-contain">
                </div>
                <div class="flex md:grid md:grid-cols-5 gap-2 overflow-x-auto hide-scrollbar pb-2">
                    @foreach([$inventory->product->img_one, $inventory->product->img_two, $inventory->product->img_three, $inventory->product->img_four, $inventory->product->img_five] as $image)
                        @if($image)
                        <div class="thumb-item flex-shrink-0" onclick="changeImage('{{ asset('storage/' . $image) }}', this)">
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $inventory->product->name }}" class="h-16 md:h-20 object-contain w-full">
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Details -->
            <div class="w-full">
                <!-- Badges -->
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($inventory->product->brand)
                        <span class="rb-badge badge-brand">{{ ucfirst(strtolower($inventory->product->brand->name)) }}</span>
                    @endif
                    @if(!is_null($inventory->product_type))
                        <span class="rb-badge badge-type">{{ ucfirst(strtolower($inventory->product_type)) }}</span>
                    @endif
                    @if($inventory->product->mrp > $inventory->sell_rate)
                        <span class="rb-badge badge-discount">
                            {{ round(($inventory->product->mrp - $inventory->sell_rate) / $inventory->product->mrp * 100) }}% OFF
                        </span>
                    @endif
                </div>

                <!-- Title -->
                <h1 class="text-xl sm:text-2xl font-bold mb-2" style=" color: var(--rb-dark);">
                    {{ $inventory->product->name }}
                    @if($inventory->product->productAttributes->isNotEmpty())
                        <span class="text-lg font-normal" style="color: var(--rb-light);">(
                        @foreach($inventory->product->productAttributes as $attr)
                            {{ $attr->attributeValue->value }}@if(!$loop->last) / @endif
                        @endforeach
                        )</span>
                    @endif
                </h1>

                <!-- Rating -->
                <div class="flex items-center mb-4 gap-3">
                    <div class="flex text-sm" style="color: var(--rb-gold);">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= 4 ? 'fas' : 'far' }} fa-star"></i>
                        @endfor
                    </div>
                    <span class="text-xs" style="color: var(--rb-light);">(124 reviews)</span>
                    <span style="color: var(--rb-warm-border-mid);">|</span>
                    <span class="text-xs font-semibold" style="color: #2E7D32;">✓ In Stock</span>
                </div>

                <!-- Price -->
                <div class="mb-4 p-4 rounded-xl" style="background: var(--rb-warm-bg); border: 1px solid var(--rb-warm-border);">
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl font-bold" style="color: var(--rb-primary);">₹{{ number_format($inventory->sell_rate) }}</span>
                        @if($inventory->product->mrp > $inventory->sell_rate)
                        <span class="text-base text-gray-400 line-through">₹{{ number_format($inventory->product->mrp) }}</span>
                        @endif
                    </div>
                    @if($inventory->product->mrp > $inventory->sell_rate)
                    <span class="text-sm font-semibold" style="color: #2E7D32;">
                        You save ₹{{ number_format($inventory->product->mrp - $inventory->sell_rate) }}
                    </span>
                    @endif
                </div>

                <!-- Quality Condition -->
                <div class="mb-4">
                    <h3 class="text-sm font-bold mb-1" style="color: var(--rb-dark);">
                        Quality Condition: <span style="color: #2E7D32;">{{ ucfirst($inventory->health) }}</span>
                    </h3>
                    <p class="text-sm leading-relaxed" style="color: #5C3020;">
                        @switch($inventory->health)
                            @case('sealed pack') Factory-sealed, completely untouched and in pristine condition—exactly as it left the manufacturer. @break
                            @case('super mint') Nearly indistinguishable from new. Handled with extreme care and shows no signs of wear. @break
                            @case('superb') Minimal to no cosmetic imperfections. Thoroughly inspected, cleaned, and tested for excellent functionality. @break
                            @case('good') Light signs of use. Fully tested and cleaned, delivering solid performance at great value. @break
                            @case('fair') Visible signs of use but fully functional. Tested for reliability—perfect for budget-conscious buyers. @break
                            @default Inspected and fully functional. Cosmetic condition may vary but guaranteed to perform reliably.
                        @endswitch
                    </p>
                </div>

                <!-- Specifications -->
                <div class="mb-5">
                    <h3 class="text-sm font-bold mb-2" style="color: var(--rb-dark);">Specifications</h3>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                        <!-- Column 1 -->
                        <div>
                            @foreach($specsColumn1 as $spec)
                                <div class="mb-1">
                                    <span class="font-semibold" style="color: var(--rb-mid);">{{ $spec['name'] }}:</span>
                                    <span style="color: #2A1208;"> {{ $spec['value'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Column 2 -->
                        <div>
                            @foreach($specsColumn2 as $spec)
                                <div class="mb-1">
                                    <span class="font-semibold" style="color: var(--rb-mid);">{{ $spec['name'] }}:</span>
                                    <span style="color: #2A1208;"> {{ $spec['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div style="border-top: 1px solid var(--rb-warm-border); padding-top: 16px;">
                    @auth
                        <form method="POST" action="{{ route('customer.cart.add') }}" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input type="hidden" name="inventory_id" value="{{ $inventory->inventory_id }}">
                            <input type="hidden" name="quantity" value="1">

                            <button type="submit" class="add-to-cart-btn flex-1 py-3 px-4 rounded-xl font-semibold text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Add to Cart
                            </button>
                            <a href="{{ route('customer.checkout', ['inventory_id' => $inventory->inventory_id]) }}" class="buy-now-btn flex-1 py-3 px-4 rounded-xl font-semibold text-sm text-center flex items-center justify-center">
                                Buy Now
                            </a>
                        </form>
                    @else
                        <div class="flex flex-col sm:flex-row gap-2">
                            <a href="{{ route('login') }}" class="add-to-cart-btn flex-1 py-3 px-4 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 text-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Add to Cart
                            </a>
                            <a href="{{ route('login') }}" class="buy-now-btn flex-1 py-3 px-4 rounded-xl font-semibold text-sm text-center flex items-center justify-center">
                                Buy Now
                            </a>
                        </div>
                    @endauth

                    <div class="mt-3 flex items-center gap-2 text-xs" style="color: #8A5A48;">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Free delivery within 5 KM radius of our store.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="mt-6 bg-white rounded-xl overflow-hidden" style="border: 1px solid var(--rb-warm-border); box-shadow: 0 2px 12px rgba(139,37,0,0.06);">
        <div class="overflow-x-auto hide-scrollbar" style="border-bottom: 1px solid var(--rb-warm-border);">
            <nav class="flex w-max min-w-full px-4">
                <button id="warrantyTab" class="tab-btn active" onclick="switchTab('warranty')">Warranty & Returns</button>
                <button id="descriptionTab" class="tab-btn" onclick="switchTab('description')">Description</button>
            </nav>
        </div>
        <div class="p-4 md:p-6">
            <div id="warrantyContent" class="tab-content active">
                <h4 class="text-sm font-bold mb-2" style="color: var(--rb-dark);">Warranty Information</h4>
                <p class="text-sm leading-relaxed mb-4" style="color: #5C3020;">
                    @if($inventory->warranty === 'out of warranty')
                        This product is out of warranty.
                    @else
                        This product is covered under our {{ $inventory->warranty }} warranty against manufacturing defects and functionality issues. The warranty does not cover physical or liquid damage.
                    @endif
                </p>
                <h4 class="text-sm font-bold mb-2" style="color: var(--rb-dark);">Return Policy</h4>
                <p class="text-sm leading-relaxed" style="color: #5C3020;">
                    If there is any problem with the product within 7 days of delivery. You may return this product for a full refund or exchange. The product must be in its original condition with all accessories included.
                </p>
            </div>
            <div id="descriptionContent" class="tab-content">
                {!! $inventory->product->description !!}
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-8">
        <div class="flex items-center gap-3 mb-5">
            <h2 class="text-lg font-bold" style="color: var(--rb-dark); ">You May Also Like</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5">
            @foreach($relatedProducts as $related)
            <a href="{{ route('product.show', ['product' => $related->product->slug, 'inventory' => $related->inventory_id]) }}"
                class="product-card" style="border: 1px solid var(--rb-warm-border); border-radius: 12px; overflow: hidden; background: white; text-decoration: none; display: block;">
                <div class="relative">
                    <img src="{{ asset('storage/' . $related->product->img_one) }}" alt="{{ $related->product->name }}"
                        class="w-full h-40 md:h-48 object-contain p-4">
                    @if($related->product->mrp > $related->sell_rate)
                    <span class="discount-badge absolute top-2 left-2 text-white text-xs px-2 py-0.5 rounded-full font-semibold">
                        {{ round(($related->product->mrp - $related->sell_rate) / $related->product->mrp * 100) }}% OFF
                    </span>
                    @endif
                </div>
                <div class="p-3" style="border-top: 1px solid var(--rb-warm-border);">
                    <h3 class="text-sm font-semibold truncate" style="color: #2A1208;">{{ $related->product->name }}</h3>
                    <div class="flex items-baseline mt-1 gap-2">
                        <span class="text-sm font-bold" style="color: var(--rb-primary);">₹{{ number_format($related->sell_rate) }}</span>
                        @if($related->product->mrp > $related->sell_rate)
                        <span class="text-xs text-gray-400 line-through">₹{{ number_format($related->product->mrp) }}</span>
                        @endif
                    </div>
                    <div class="mt-1.5 text-xs font-semibold" style="color: #2E7D32;">✓ {{ ucfirst($related->health) }}</div>
                    <div class="mt-0.5 text-xs text-gray-500 truncate">
                        @foreach($related->product->productAttributes as $attr)
                            {{ $attr->attribute->name }}: {{ $attr->attributeValue->value }}@if(!$loop->last), @endif
                        @endforeach
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
    function changeImage(src, el) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
        if (el) el.classList.add('active');
    }

    function switchTab(tab) {
        ['warranty', 'description'].forEach(t => {
            document.getElementById(t + 'Content').classList.remove('active');
            document.getElementById(t + 'Content').classList.add('hidden');
            document.getElementById(t + 'Tab').classList.remove('active');
        });
        document.getElementById(tab + 'Content').classList.add('active');
        document.getElementById(tab + 'Content').classList.remove('hidden');
        document.getElementById(tab + 'Tab').classList.add('active');
    }
</script>
@endsection
