@extends('account.layout')

@section('account')
<div class="space-y-6">
    <h2 class="text-xl font-bold" style="color: #6B1A00;">My Wishlist</h2>

    @if(($items ?? collect())->isEmpty())
        <div class="bg-white rounded-xl border shadow-sm p-8 text-center">
            <i class="fas fa-heart text-3xl text-gray-300 mb-2"></i>
            <p class="text-sm text-gray-500">Your wishlist is empty. Save products you like to find them here.</p>
            <a href="{{ route('products') }}" class="inline-block mt-3 px-5 py-2 bg-red-800 text-white text-sm font-semibold rounded-lg hover:bg-red-900">Browse Products</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $inventory)
                @php $product = $inventory->product ?? null; @endphp
                <div class="bg-white rounded-xl border shadow-sm overflow-hidden flex flex-col">
                    @if($product)
                        <a href="{{ route('product.show', ['product' => $product->slug, 'inventory' => $inventory->inventory_id]) }}" class="block p-4 pb-0">
                            @if($product->img_one ?? null)
                                <img src="{{ asset('storage/' . $product->img_one) }}" alt="{{ $product->name }}" class="w-full h-40 object-contain">
                            @else
                                <div class="w-full h-40 bg-gray-100 flex items-center justify-center rounded-lg">
                                    <i class="fas fa-image text-gray-300 text-3xl"></i>
                                </div>
                            @endif
                        </a>
                        <div class="p-4 flex-1 flex flex-col">
                            <a href="{{ route('product.show', ['product' => $product->slug, 'inventory' => $inventory->inventory_id]) }}" class="font-semibold text-sm text-gray-800 hover:text-red-800 line-clamp-2">{{ $product->name }}</a>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="font-bold text-red-800">&#8377;{{ number_format($inventory->sell_rate ?? 0) }}</span>
                                @if(($product->mrp ?? 0) > ($inventory->sell_rate ?? 0))
                                    <span class="text-xs text-gray-400 line-through">&#8377;{{ number_format($product->mrp) }}</span>
                                @endif
                            </div>
                            @if(($inventory->stock ?? 0) <= 0)
                                <p class="text-xs text-red-600 font-semibold mt-1">Out of stock</p>
                            @endif
                        </div>
                    @else
                        <div class="p-4 flex-1">
                            <p class="text-sm text-gray-500">This product is no longer available.</p>
                        </div>
                    @endif
                    <div class="p-4 pt-0 flex gap-2">
                        @if($product && ($inventory->stock ?? 0) > 0)
                            <form method="POST" action="{{ route('cart.add') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="inventory_id" value="{{ $inventory->inventory_id }}">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="w-full px-3 py-2 bg-red-800 text-white text-sm font-bold rounded-lg hover:bg-red-900">Add to Cart</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('account.wishlist.destroy', $inventory->inventory_id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 border border-gray-300 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50" title="Remove from wishlist">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
