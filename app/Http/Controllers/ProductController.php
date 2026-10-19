<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
                    'brand',
                    'category',
                    'productAttributes.attribute',
                    'productAttributes.attributeValue'
                ])
                ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%")
                ->orWhereHas('brand', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('category', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status === 'active') {
            $query->where('is_active', true);
        }

        // Stock filter - updated to match Inventory model
        if ($request->has('stock')) {
            if ($request->stock === 'out') {
                $query->whereDoesntHave('inventories', function($q) {
                    $q->where('stock', '>', 0);
                });
            } elseif ($request->stock === 'low') {
                $query->whereHas('inventories', function($q) {
                    $q->select('product_id')
                    ->groupBy('product_id')
                    ->havingRaw('SUM(stock) BETWEEN 1 AND 10');
                });
            }
        }

        $products = $query->paginate(15);
        return view('admin.products', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        return view('admin.add-product', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'color' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
            'brand_id' => 'required|exists:brands,brand_id',
            'sku' => 'required|string|unique:products,sku|max:100',
            'barcode' => 'nullable|string|unique:products,barcode|max:100',
            'retail_price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'vid_url' => 'nullable|url|max:255',
            'image_one' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'image_two' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'image_three' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'image_four' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'image_five' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'required_with:attributes|exists:attributes,attribute_id',
            'attributes.*.attribute_value_id' => 'required_with:attributes|exists:attribute_values,attribute_value_id',
        ]);

        try {
            DB::beginTransaction();

            // Create slug
            $slug = Str::slug($validatedData['title']);
            $originalSlug = $slug;
            $counter = 1;

            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            // Create product
            $product = Product::create([
                'name' => $validatedData['title'],
                'description' => $validatedData['description'],
                'color' => $validatedData['color'],
                'category_id' => $validatedData['category_id'],
                'brand_id' => $validatedData['brand_id'],
                'slug' => $slug,
                'sku' => $validatedData['sku'],
                'barcode' => $this->generateUniqueBarcode(8) ?? null,
                'retail_price' => $validatedData['retail_price'],
                'mrp' => $validatedData['mrp'] ?? null,
                'is_active' => $validatedData['is_active'] ?? true,
                'vid_url' => $validatedData['vid_url'] ?? null,
            ]);

            // Upload product images using the old location method
            $this->uploadProductImages($product, $request);

            // Save attributes
            if (!empty($validatedData['attributes'])) {
                foreach ($validatedData['attributes'] as $attributeData) {
                    ProductAttribute::create([
                        'product_id' => $product->product_id,
                        'attribute_id' => $attributeData['attribute_id'],
                        'attribute_value_id' => $attributeData['attribute_value_id'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.products')
                ->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create product. Please try again. Error: ' . $e->getMessage());
        }
    }

    private function generateUniqueBarcode(int $length = 12): string
    {
        if ($length < 8 || $length > 20) {
            throw new \InvalidArgumentException('Barcode length must be between 8-20 digits');
        }

        $maxAttempts = 10;
        $attempt = 0;

        do {
            $barcode = str_pad(
                (string) mt_rand(0, pow(10, $length) - 1),
                $length,
                '0',
                STR_PAD_LEFT
            );

            if (!Product::where('barcode', $barcode)->exists()) {
                return $barcode;
            }

            $attempt++;
        } while ($attempt < $maxAttempts);

        throw new \RuntimeException('Failed to generate unique barcode after '.$maxAttempts.' attempts');
    }

    private function uploadProductImages($product, $request)
    {
        $imageFields = [
            'image_one' => 'img_one',
            'image_two' => 'img_two',
            'image_three' => 'img_three',
            'image_four' => 'img_four',
            'image_five' => 'img_five',
        ];

        $updates = [];

        foreach ($imageFields as $requestField => $dbField) {
            if ($request->hasFile($requestField)) {
                $file = $request->file($requestField);

                // Generate unique filename using the old method
                $randomString = Str::random(6);
                $filename = $product->slug . '-' . $randomString . '.' . $file->getClientOriginalExtension();

                // Store file in the old location (public_html)
                $path = $file->storeAs('Products', $filename, 'public_html');

                // Add to updates array
                $updates[$dbField] = $path;
            }
        }

        // Update product with image paths if any were uploaded
        if (!empty($updates)) {
            $product->update($updates);
        }
    }

    public function edit(Product $product)
    {
        try {
            // Load product with necessary relationships
            $product->load([
                'category',
                'brand',
                'productAttributes.attribute',
                'productAttributes.attributeValue'
            ]);

            // Get data for dropdowns
            $categories = Category::where('is_active', true)->get();
            $brands = Brand::where('is_active', true)->get();
            $attributes = Attribute::with('values')->get();

            return view('admin.edit-product', compact(
                'product',
                'categories',
                'brands',
                'attributes'
            ));

        } catch (\Exception $e) {
            return redirect()->route('admin.products')
                ->with('error', 'Failed to load product: '.$e->getMessage());
        }
    }

    public function duplicate(Product $product)
    {
        try {
            // Load product with necessary relationships
            $product->load([
                'category',
                'brand',
                'productAttributes.attribute',
                'productAttributes.attributeValue'
            ]);

            // Get data for dropdowns
            $categories = Category::where('is_active', true)->get();
            $brands = Brand::where('is_active', true)->get();
            $attributes = Attribute::with('values')->get();

            return view('admin.duplicate-product', compact(
                'product',
                'categories',
                'brands',
                'attributes'
            ));

        } catch (\Exception $e) {
            return redirect()->route('admin.products')
                ->with('error', 'Failed to load product: '.$e->getMessage());
        }
    }

    public function update(Request $request, Product $product)
    {
        DB::beginTransaction();

        try {
            // Validate request data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'color' => 'nullable|string',
                'sku' => 'required|string|max:255|unique:products,sku,'.$product->product_id.',product_id',
                'barcode' => 'nullable|string|max:255|unique:products,barcode,'.$product->product_id.',product_id',
                'retail_price' => 'required|numeric|min:0',
                'mrp' => 'nullable|numeric|min:0',
                'category_id' => 'required|exists:categories,category_id',
                'brand_id' => 'required|exists:brands,brand_id',
                'is_active' => 'required|boolean',
                'vid_url' => 'nullable|url',
                'image_one' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_two' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_three' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_four' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_five' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'attributes' => 'nullable|array',
                'attributes.*.attribute_id' => 'required|exists:attributes,attribute_id',
                'attributes.*.attribute_value_id' => 'required|exists:attribute_values,attribute_value_id',
                'remove_image_one' => 'nullable|boolean',
                'remove_image_two' => 'nullable|boolean',
                'remove_image_three' => 'nullable|boolean',
                'remove_image_four' => 'nullable|boolean',
                'remove_image_five' => 'nullable|boolean',
            ]);

            // Update product details
            $product->update([
                'name' => $validated['title'],
                'description' => $validated['description'],
                'color' => $validated['color'],
                'sku' => $validated['sku'],
                'barcode' => $validated['barcode'],
                'retail_price' => $validated['retail_price'],
                'mrp' => $validated['mrp'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'],
                'is_active' => $validated['is_active'],
                'vid_url' => $validated['vid_url'],
            ]);

            // Handle image updates and removals
            $imageFields = [
                'image_one' => 'img_one',
                'image_two' => 'img_two',
                'image_three' => 'img_three',
                'image_four' => 'img_four',
                'image_five' => 'img_five',
            ];

            $updates = [];

            foreach ($imageFields as $requestField => $dbField) {
                // Handle image removal
                if ($request->has('remove_'.$requestField) && $request->input('remove_'.$requestField)) {
                    if ($product->$dbField && Storage::disk('public_html')->exists($product->$dbField)) {
                        Storage::disk('public_html')->delete($product->$dbField);
                    }
                    $updates[$dbField] = null;
                }
                // Handle new image upload
                elseif ($request->hasFile($requestField)) {
                    // Delete old image if exists
                    if ($product->$dbField && Storage::disk('public_html')->exists($product->$dbField)) {
                        Storage::disk('public_html')->delete($product->$dbField);
                    }

                    // Generate filename using same method as store function
                    $file = $request->file($requestField);
                    $randomString = Str::random(6);
                    $filename = $product->slug . '-' . $randomString . '.' . $file->getClientOriginalExtension();

                    // Store new image
                    $path = $file->storeAs('Products', $filename, 'public_html');
                    $updates[$dbField] = $path;
                }
            }

            // Update product with image paths if any were changed
            if (!empty($updates)) {
                $product->update($updates);
            }

            // Handle product attributes
            $product->productAttributes()->delete(); // Remove existing attributes

            if ($request->has('attributes')) {
                foreach ($request->input('attributes') as $attribute) {
                    ProductAttribute::create([
                        'product_id' => $product->product_id,
                        'attribute_id' => $attribute['attribute_id'],
                        'attribute_value_id' => $attribute['attribute_value_id']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.products.edit', $product->product_id)
                ->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update product: '.$e->getMessage());
        }
    }

    public function destroy($product_id)
    {
        try {
            DB::beginTransaction();

            // Find the product with relationships
            $product = Product::with(['productAttributes', 'inventories'])
                            ->findOrFail($product_id);

            // Check if product has any inventory
            if ($product->inventories->count() > 0) {
                return back()->with('error', 'Cannot delete product because it has inventory items. Please clear inventory first.');
            }

            // Delete product images from storage
            $images = [
                $product->img_one,
                $product->img_two,
                $product->img_three,
                $product->img_four,
                $product->img_five
            ];

            foreach ($images as $image) {
                if ($image && Storage::disk('public_html')->exists($image)) {
                    Storage::disk('public_html')->delete($image);
                }
            }

            // Delete all product attributes
            $product->productAttributes()->delete();

            // Finally delete the product
            $product->delete();

            DB::commit();

            return redirect()->route('admin.products')
                ->with('success', 'Product deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function data()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $attributes = Attribute::with('values')->get();

        return response()->json([
            'categories' => $categories,
            'brands' => $brands,
            'attributes' => $attributes,
        ]);
    }
}
