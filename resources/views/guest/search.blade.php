@extends('layouts.store')

@section('title', 'Search' . ($searchTerm ? " - $searchTerm" : ''))

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    .search-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 1000;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .product-list-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .product-list-item:hover {
        background-color: #f8f9fa;
        border-left-color: #0d6efd;
    }

    .price-original {
        text-decoration: line-through;
        color: #6c757d;
    }

    .price-discounted {
        color: #dc3545;
        font-weight: 600;
    }

    .discount-badge {
        background-color: #dc3545;
        color: white;
        font-size: 0.75rem;
    }

    .stock-warning {
        color: #fd7e14;
        font-size: 0.875rem;
    }

    .out-of-stock {
        color: #dc3545;
        font-weight: 600;
    }

    .rating-badge {
        background-color: #198754;
        color: white;
    }

    .condition-badge {
        background-color: #6f42c1;
        color: white;
    }

    .tag-badge {
        background-color: #0dcaf0;
        color: white;
    }
</style>
@endsection

@section('content')
<!-- Search Header -->
<div class="search-header py-3 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-auto">
                <button onclick="window.history.back()" class="btn btn-link p-0 text-dark">
                    <i class="fas fa-arrow-left fa-lg"></i>
                </button>
            </div>
            <div class="col">
                <form action="{{ route('search') }}" method="GET" class="d-flex">
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        placeholder="Search for products..."
                        value="{{ $searchTerm }}"
                        id="searchInput"
                        autocomplete="off">
                    <button class="btn btn-outline-secondary ms-2" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container">
    @if($searchTerm)
        <!-- Search Results Header -->
        <div class="mb-4">
            @if($resultsCount > 0)
                <h5 class="text-muted">Found {{ $resultsCount }} results for "{{ $searchTerm }}"</h5>
            @else
                <h5 class="text-muted">No results found for "{{ $searchTerm }}"</h5>
            @endif
        </div>
    @endif

    @if($products->isNotEmpty())
        <!-- Product List -->
        <div class="row">
            <div class="col-12">
                @foreach($products as $product)
                    @foreach($product->activeVariants as $variant)
                        @php
                            $discountPercentage = $variant->compare_at_price > 0
                                ? round(($variant->compare_at_price - $variant->price) / $variant->compare_at_price * 100)
                                : 0;
                        @endphp
                        <div class="product-list-item p-3 mb-3 border rounded">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge condition-badge">{{ ucfirst($product->product_condition) }}</span>
                                        @if($product->tags)
                                            <span class="badge tag-badge">{{ $product->tags }}</span>
                                        @endif
                                        @if($product->rating > 0)
                                            <span class="badge rating-badge">
                                                <i class="fas fa-star me-1"></i>{{ number_format($product->rating, 1) }}
                                            </span>
                                        @endif
                                    </div>
                                    <h6 class="mb-2">
                                        <a href="{{ route('product.show', ['product' => $product->slug, 'inventory' => $variant->inventory_id ?? $variant->variant_id ?? null]) }}"
                                           class="text-decoration-none text-dark">
                                            {{ $product->name }}
                                            @if($variant->attributes->isNotEmpty())
                                                ({{ $variant->attributes
                                                ->whereIn('attributeValue.attribute.name', ['ram', 'storage'])
                                                ->map(function($attr) {
                                                    return $attr->attributeValue->value;
                                                })->implode(', ') }})
                                            @endif
                                        </a>
                                    </h6>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="mb-2">
                                        <span class="price-discounted h5 me-2">₹{{ number_format($variant->price, 0) }}</span>
                                        @if($variant->compare_at_price > 0 && $variant->compare_at_price > $variant->price)
                                            <span class="price-original me-2">₹{{ number_format($variant->compare_at_price, 0) }}</span>
                                        @endif
                                    @if($discountPercentage > 0)
                                            <span class="badge discount-badge">{{ $discountPercentage }}% OFF</span>
                                    @endif
                                    </div>
                                    @if($variant->stock_quantity > 0 && $variant->stock_quantity <= 5)
                                        <div class="stock-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Only {{ $variant->stock_quantity }} left
                                        </div>
                                    @elseif($variant->stock_quantity === 0)
                                        <div class="out-of-stock">
                                            <i class="fas fa-times-circle me-1"></i>Out of Stock
                                        </div>
                                    @else
                                        <div class="text-success small">
                                            <i class="fas fa-check-circle me-1"></i>In Stock
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(['q' => $searchTerm])->links() }}
            </div>
        @endif
    @elseif($searchTerm)
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search fa-3x text-muted"></i>
            </div>
            <h4 class="mb-3">No products found</h4>
            <p class="text-muted mb-4">We couldn't find any products matching "{{ $searchTerm }}"</p>
            <a href="{{ route('products') }}" class="btn btn-primary">
                <i class="fas fa-grid-2 me-2"></i>Browse All Products
            </a>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search fa-3x text-muted"></i>
            </div>
            <h4 class="mb-3">Start searching</h4>
            <p class="text-muted">Search for products by name, brand, or features</p>
        </div>
    @endif
</div>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Submit form on search input change
        const searchInput = document.querySelector('#searchInput');

        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });

        // Focus search input on page load if empty
        if (searchInput.value === '') {
            searchInput.focus();
        }

        // Optional: Auto-submit after typing stops (debounced)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    });
</script>
@endsection
