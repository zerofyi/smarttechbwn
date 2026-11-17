<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;

use App\Models\Attribute;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Shop;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function home()
    {
        // Get main banners (TYPE_MAIN)
        $mainBanners = Banner::visible()
            ->ofType(Banner::TYPE_MAIN)
            ->ordered()
            ->get();

        // Get single promo banner (TYPE_PROMO)
        $promoBanner = Banner::visible()
            ->ofType(Banner::TYPE_PROMO)
            ->ordered()
            ->first();

        // Get popular brands
        $brands = Brand::where('is_active', true)
            //->has('products')
            ->limit(8)
            ->get();

        // Get top products (inventory items with products)
        $topProducts = Inventory::with('product')
            ->whereHas('product')
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get flash sale products (example: products with discount)
        $flashSaleProducts = Inventory::with('product')
            ->whereHas('product', function($query) {
                $query->whereColumn('mrp', '>', 'retail_price');
            })
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        // Get best sellers (example: most ordered products)
        $bestSellers = Inventory::with('product')
            ->whereHas('product')
            ->where('stock', '>', 0)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(10)
            ->get();

        return view('guest.home', compact(
            'mainBanners',
            'promoBanner',
            'brands',
            'topProducts',
            'flashSaleProducts',
            'bestSellers'
        ));
    }
    public function explore()
    {
        // Get popular brands with product counts
        $popularBrands = Brand::withCount('products')
            ->where('is_active', true)
            ->orderBy('products_count', 'desc')
            ->get();

        // Get main categories with their children and product counts
        $mainCategories = Category::with(['children' => function($query) {
                $query->withCount('products');
            }])
            ->withCount('products')
            ->whereNull('parent_category_id')
            ->orderBy('display_order')
            ->get();

        // Featured products
        $featuredProducts = Inventory::with(['product' => function($query) {
                $query->with(['productAttributes.attribute', 'productAttributes.attributeValue']);
            }])
            ->where('stock', '>', 0)
            ->whereHas('product')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get colors from inventory
        $colors = Inventory::select('color')
            ->distinct()
            ->whereNotNull('color')
            ->get()
            ->map(function ($item) {
                $colorMap = [
                    'black' => ['name' => 'Black', 'hex' => '#000000'],
                    'white' => ['name' => 'White', 'hex' => '#ffffff'],
                    'blue' => ['name' => 'Blue', 'hex' => '#2563eb'],
                    'green' => ['name' => 'Green', 'hex' => '#10b981'],
                    'red' => ['name' => 'Red', 'hex' => '#ef4444'],
                    'purple' => ['name' => 'Purple', 'hex' => '#8b5cf6'],
                    'pink' => ['name' => 'Pink', 'hex' => '#ec4899'],
                    'silver' => ['name' => 'Silver', 'hex' => '#9ca3af'],
                ];

                $color = strtolower($item->color);
                return [
                    'name' => $colorMap[$color]['name'] ?? ucfirst($color),
                    'value' => $color,
                    'hex' => $colorMap[$color]['hex'] ?? '#cccccc'
                ];
            })
            ->unique('value')
            ->values()
            ->toArray();

        // Get condition options from inventory health
        $conditionOptions = Inventory::select('health')
            ->distinct()
            ->whereNotNull('health')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->health,
                    'label' => ucfirst($item->health)
                ];
            })
            ->toArray();

        // Get warranty options from inventory
        $warrantyOptions = Inventory::select('warranty')
            ->distinct()
            ->whereNotNull('warranty')
            ->orderBy('warranty')
            ->pluck('warranty')
            ->toArray();

        // Get RAM options from product attributes
        $ramAttribute = Attribute::where('name', 'ram')->first();
        $ramOptions = $ramAttribute
            ? $ramAttribute->values()
                ->orderByRaw('CAST(value AS UNSIGNED)')
                ->pluck('value')
                ->unique()
                ->toArray()
            : [4, 6, 8, 12, 16];

        // Get Storage options from product attributes
        $storageAttribute = Attribute::where('name', 'storage')->first();
        $storageOptions = $storageAttribute
            ? $storageAttribute->values()
                ->orderByRaw('CAST(value AS UNSIGNED)')
                ->pluck('value')
                ->unique()
                ->toArray()
            : [32, 64, 128, 256, 512, 1024];

        // Price ranges
        $priceRanges = [
            ['min' => null, 'max' => 7000, 'label' => 'Budget phones'],
            ['min' => 7000, 'max' => 15000, 'label' => 'Mid-range'],
            ['min' => 15000, 'max' => 30000, 'label' => 'Premium'],
            ['min' => 30000, 'max' => null, 'label' => 'Flagship'],
        ];

        return view('guest.explore', compact(
            'popularBrands',
            'mainCategories',
            'featuredProducts',
            'colors',
            'priceRanges',
            'ramOptions',
            'storageOptions',
            'conditionOptions',
            'warrantyOptions'
        ));
    }

    public function stores()
    {
        $shops = Shop::with(['user' => function($query) {
                $query->select('user_id', 'mobile');
            }])
            ->get()
            ->map(function($shop) {
                $shop->rating = 4.8;
                $shop->address = $shop->shop_address ?? 'Not Found';
                $shop->phone = $shop->user->mobile;
                $shop->hours = '10AM - 9PM';
                return $shop;
            });

        return view('guest.stores', compact('shops'));
    }


    public function products(Request $request)
    {
        // Get filters from request
        $filters = [
            'search' => $request->input('search'),
            'brand' => $request->input('brand'),
            'category' => $request->input('category'),
            'color' => $request->input('color'),
            'health' => $request->input('health'),
            'warranty' => $request->input('warranty'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'attribute' => $request->input('attribute'),
            'value' => $request->input('value'),
            'sort' => $request->input('sort', 'newest'), // Default sort by newest
        ];

        // Base query for inventories with products
        $inventories = Inventory::with(['product' => function($query) {
                $query->with(['brand', 'category', 'productAttributes.attribute', 'productAttributes.attributeValue']);
            }])
            ->where('stock', '>', 0)
            ->whereHas('product', function($query) use ($filters) {
                // Apply search filter
                if ($filters['search']) {
                    $query->where(function($q) use ($filters) {
                        $q->where('name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('description', 'like', '%'.$filters['search'].'%')
                        ->orWhereHas('brand', function($brandQuery) use ($filters) {
                            $brandQuery->where('name', 'like', '%'.$filters['search'].'%');
                        });
                    });
                }

                // Apply brand filter
                if ($filters['brand']) {
                    $query->whereHas('brand', function($q) use ($filters) {
                        $q->where('slug', $filters['brand']);
                    });
                }

                // Apply category filter
                if ($filters['category']) {
                    $query->whereHas('category', function($q) use ($filters) {
                        $q->where('slug', $filters['category']);
                    });
                }

                // Apply attribute filter
                if ($filters['attribute'] && $filters['value']) {
                    $query->whereHas('productAttributes', function($q) use ($filters) {
                        $q->whereHas('attribute', function($attrQuery) use ($filters) {
                            $attrQuery->where('name', $filters['attribute']);
                        })
                        ->whereHas('attributeValue', function($valQuery) use ($filters) {
                            $valQuery->where('value', $filters['value']);
                        });
                    });
                }
            });

        // Apply inventory-specific filters
        if ($filters['color']) {
            $inventories->where('color', $filters['color']);
        }

        if ($filters['health']) {
            $inventories->where('health', $filters['health']);
        }

        if ($filters['warranty']) {
            $inventories->where('warranty', $filters['warranty']);
        }

        // Apply price range filter
        if ($filters['min_price']) {
            $inventories->where('sell_rate', '>=', $filters['min_price']);
        }

        if ($filters['max_price']) {
            $inventories->where('sell_rate', '<=', $filters['max_price']);
        }

        // Apply sorting
        switch ($filters['sort']) {
            case 'popular':
                $inventories->orderByDesc('stock');
                break;
            case 'price_asc':
                $inventories->orderBy('sell_rate');
                break;
            case 'price_desc':
                $inventories->orderByDesc('sell_rate');
                break;
            case 'newest':
            default:
                $inventories->orderByDesc('created_at');
                break;
        }

        // Get filter options (same as before)
        $popularBrands = Brand::withCount('products')
            ->where('is_active', true)
            ->orderBy('products_count', 'desc')
            ->limit(8)
            ->get();

        $mainCategories = Category::with(['children' => function($query) {
                $query->withCount('products');
            }])
            ->withCount('products')
            ->whereNull('parent_category_id')
            ->orderBy('display_order')
            ->limit(4)
            ->get();

        $colors = Inventory::select('color')
            ->distinct()
            ->whereNotNull('color')
            ->get()
            ->map(function ($item) {
                $colorMap = [
                    'black' => ['name' => 'Black', 'hex' => '#000000'],
                    'white' => ['name' => 'White', 'hex' => '#ffffff'],
                    'blue' => ['name' => 'Blue', 'hex' => '#2563eb'],
                    'green' => ['name' => 'Green', 'hex' => '#10b981'],
                    'red' => ['name' => 'Red', 'hex' => '#ef4444'],
                    'purple' => ['name' => 'Purple', 'hex' => '#8b5cf6'],
                    'pink' => ['name' => 'Pink', 'hex' => '#ec4899'],
                    'silver' => ['name' => 'Silver', 'hex' => '#9ca3af'],
                ];

                $color = strtolower($item->color);
                return [
                    'name' => $colorMap[$color]['name'] ?? ucfirst($color),
                    'value' => $color,
                    'hex' => $colorMap[$color]['hex'] ?? '#cccccc'
                ];
            })
            ->unique('value')
            ->values()
            ->toArray();

        $conditionOptions = Inventory::select('health')
            ->distinct()
            ->whereNotNull('health')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->health,
                    'label' => ucfirst($item->health)
                ];
            })
            ->toArray();

        $warrantyOptions = Inventory::select('warranty')
            ->distinct()
            ->whereNotNull('warranty')
            ->orderBy('warranty')
            ->pluck('warranty')
            ->toArray();

        $ramAttribute = Attribute::where('name', 'ram')->first();
        $ramOptions = $ramAttribute
            ? $ramAttribute->values()
                ->orderByRaw('CAST(value AS UNSIGNED)')
                ->pluck('value')
                ->unique()
                ->toArray()
            : [4, 6, 8, 12, 16];

        $storageAttribute = Attribute::where('name', 'storage')->first();
        $storageOptions = $storageAttribute
            ? $storageAttribute->values()
                ->orderByRaw('CAST(value AS UNSIGNED)')
                ->pluck('value')
                ->unique()
                ->toArray()
            : [32, 64, 128, 256, 512, 1024];

        $priceRanges = [
            ['min' => null, 'max' => 7000, 'label' => 'Budget phones'],
            ['min' => 7000, 'max' => 15000, 'label' => 'Mid-range'],
            ['min' => 15000, 'max' => 30000, 'label' => 'Premium'],
            ['min' => 30000, 'max' => null, 'label' => 'Flagship'],
        ];

        // Paginate results
        $inventories = $inventories->paginate(12);

        return view('guest.products', compact(
            'inventories',
            'popularBrands',
            'mainCategories',
            'colors',
            'priceRanges',
            'ramOptions',
            'storageOptions',
            'conditionOptions',
            'warrantyOptions',
            'filters'
        ));
    }
    public function show($product, $inventory)
    {
        // Get the inventory with related product data
        $inventory = Inventory::with([
                'product' => function($query) {
                    $query->with([
                        'brand',
                        'category',
                        'productAttributes.attribute',
                        'productAttributes.attributeValue'
                    ]);
                },
                'shop'
            ])
            ->findOrFail($inventory);

        // Increment view count
        // $inventory->increment('view_count');

        // Get related products (from same category)
        $relatedProducts = Inventory::with(['product' => function($query) {
                $query->with(['brand', 'productAttributes.attributeValue']);
            }])
            ->whereHas('product', function($query) use ($inventory) {
                $query->where('category_id', $inventory->product->category_id)
                    ->where('product_id', '!=', $inventory->product_id);
            })
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Prepare specifications from attributes
        $specifications = [];
        foreach ($inventory->product->productAttributes as $attribute) {
            $specifications[] = [
                'name' => $attribute->attribute->display_name ?? $attribute->attribute->name,
                'value' => $attribute->attributeValue->value
            ];
        }

        // Add inventory-specific specs
        $specifications = array_merge($specifications, [
            ['name' => 'Condition', 'value' => ucfirst($inventory->health)],
            ['name' => 'Color', 'value' => ucfirst($inventory->color)],
            [
                'name' => 'Warranty',
                'value' => !empty($inventory->activation_date)
                    ? 'Act. Date: ' . \Carbon\Carbon::parse($inventory->activation_date)->format('d/m/Y') . ' (~' . (int) \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($inventory->activation_date)->copy()->addYear(), false) . ' days left)'
                    : ($inventory->warranty ?? 'N/A')
            ],
        ]);

        // 1. Append Battery Health ONLY if it exists (no nulls in the array)
        if (!is_null($inventory->battery_health) && $inventory->battery_health !== '') {
            $specifications[] = ['name' => 'Battery Health', 'value' => $inventory->battery_health . '%'];
        }

        // 2. Append Age at the end
        $specifications[] = ['name' => 'Age', 'value' => $inventory->age];

        // 3. Fix: Divide by 2 (not 1) to split columns evenly
        $half = (int) ceil(count($specifications) / 2);
        $specsColumn1 = array_slice($specifications, 0, $half);
        $specsColumn2 = array_slice($specifications, $half);

        return view('guest.product', compact(
            'inventory',
            'relatedProducts',
            'specsColumn1',
            'specsColumn2'
        ));
    }

    public function search(Request $request)
    {
        $searchTerm = trim($request->input('q', $request->input('search')));

        $products = collect();
        $resultsCount = 0;

        if (!empty($searchTerm)) {
            $products = Inventory::with([
                    'product' => function ($query) use ($searchTerm) {
                        $query->with([
                            'brand',
                            'category',
                            'productAttributes.attribute',
                            'productAttributes.attributeValue'
                        ]);
                    }
                ])
                ->where('stock', '>', 0)
                ->whereHas('product', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('brand', function ($b) use ($searchTerm) {
                            $b->where('name', 'like', '%' . $searchTerm . '%');
                        });
                })
                ->paginate(12)
                ->appends(['q' => $searchTerm]);

            $resultsCount = $products->total();
        }

        return view('guest.products', [
            'inventories' => $products,
            'searchTerm' => $searchTerm,
            'resultsCount' => $resultsCount,
            'filters' => ['search' => $searchTerm, 'sort' => 'newest']
        ]);
    }
}
