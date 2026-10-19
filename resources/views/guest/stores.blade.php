@extends('layouts.guest')
@section('title', 'Stores')

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

    .store-card {
        border: 1px solid var(--rb-warm-border);
        border-radius: 14px;
        overflow: hidden;
        background: white;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    }
    .store-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(139,37,0,0.13);
        border-color: var(--rb-warm-border-mid);
    }

    .gst-badge {
        background: linear-gradient(135deg, var(--rb-primary), var(--rb-accent));
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }

    .rating-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--rb-gold-light);
        border: 1px solid var(--rb-gold);
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        color: var(--rb-dark);
    }

    .info-icon {
        color: var(--rb-accent);
        width: 14px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .directions-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        transition: opacity 0.2s, transform 0.1s;
        background: linear-gradient(135deg, var(--rb-primary), var(--rb-mid));
        color: white;
        text-decoration: none;
    }
    .directions-btn:hover { opacity: 0.88; }
    .directions-btn:active { transform: scale(0.97); }

    .call-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        border: 1.5px solid var(--rb-accent);
        color: var(--rb-accent);
        background: white;
        text-decoration: none;
    }
    .call-btn:hover {
        background: var(--rb-warm-bg);
        border-color: var(--rb-primary);
        color: var(--rb-primary);
    }
    .call-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }
</style>

    <!-- Page Header -->
    <section class="relative overflow-hidden text-white text-center py-9 px-4"
        style="background: linear-gradient(135deg, var(--rb-dark) 0%, var(--rb-primary) 55%, var(--rb-accent) 100%);">
        <div class="absolute -top-8 -left-8 w-32 h-32 rounded-full opacity-10" style="background: white;"></div>
        <div class="absolute -bottom-6 right-12 w-24 h-24 rounded-full opacity-10" style="background: white;"></div>
        <div class="relative z-10">
            <p class="text-xs font-bold tracking-widest mb-1" style="color: var(--rb-gold-light); letter-spacing: 3px;">FIND US NEARBY</p>
            <h1 class="text-2xl md:text-3xl font-bold" style="">Our Stores</h1>
            <p class="mt-2 text-sm" style="color: rgba(255,220,200,0.85);">Visit a verified Smart Tech store near you</p>
        </div>
        <div style="height: 2px; background: linear-gradient(90deg, var(--rb-gold), var(--rb-accent), var(--rb-gold)); margin-top: 20px;"></div>
    </section>

    <!-- Stores Grid -->
    <section class="px-4 py-8 desktop-container" style="background: var(--rb-warm-bg);">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($shops as $shop)
            <div class="store-card">
                <!-- Store Photo -->
                <div class="relative h-48 overflow-hidden" style="background: var(--rb-warm-bg);">
                    <img src="{{ $shop->shop_photo ? asset('storage/' . $shop->shop_photo) : 'https://placehold.co/600x400?text=Mobile+Store' }}"
                         alt="{{ $shop->shop_name }}"
                         class="w-full h-full object-cover">
                    <!-- Overlay gradient at bottom -->
                    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(107,26,0,0.35) 0%, transparent 50%);"></div>
                    <!-- GST Badge -->
                    <div class="absolute top-3 right-3">
                        <span class="gst-badge">✓ GST Verified</span>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Name + Rating -->
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-base font-bold leading-tight" style="color: var(--rb-dark); ">{{ $shop->shop_name }}</h3>
                        <div class="rating-pill ml-2 flex-shrink-0">
                            {{ $shop->rating }}
                            <i class="fas fa-star" style="color: var(--rb-gold); font-size: 10px;"></i>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div style="height: 1px; background: var(--rb-warm-border); margin-bottom: 12px;"></div>

                    <!-- Address -->
                    <div class="flex items-start gap-2 mb-2">
                        <i class="fas fa-map-marker-alt info-icon mt-0.5"></i>
                        <p class="text-xs leading-relaxed" style="color: #5C3020;">{{ $shop->address }}</p>
                    </div>

                    <!-- Phone + Hours -->
                    <div class="grid grid-cols-2 gap-2 mt-3 mb-4">
                        <div class="flex items-center gap-2 p-2 rounded-lg" style="background: var(--rb-warm-bg);">
                            <i class="fas fa-phone-alt info-icon" style="margin-top: 0;"></i>
                            <span class="text-xs font-medium truncate" style="color: #5C3020;">9733927304</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-lg" style="background: var(--rb-warm-bg);">
                            <i class="fas fa-clock info-icon" style="margin-top: 0;"></i>
                            <span class="text-xs font-medium truncate" style="color: #5C3020;">{{ $shop->hours }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <a href="https://maps.app.goo.gl/JEyRUWKz5xCWtsZj7"
                           target="_blank"
                           class="directions-btn">
                            <i class="fas fa-directions text-sm"></i> Directions
                        </a>
                        @if($shop->phone)
                        <a href="tel:919733927304" class="call-btn">
                            <i class="fas fa-phone-alt text-sm"></i> Call
                        </a>
                        @else
                        <button class="call-btn disabled">
                            <i class="fas fa-phone-alt text-sm"></i> Call
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Stores page loaded');
        });
    </script>
@endsection
