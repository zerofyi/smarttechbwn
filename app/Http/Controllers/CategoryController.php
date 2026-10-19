<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent' => function($query) {
            $query->select('category_id', 'name');
        }])
        ->withCount('products')
        ->orderBy('parent_category_id')
        ->orderBy('display_order')
        ->get();

        $total = $categories->count();
        $active = $categories->where('is_active', true)->count();

        return view('admin.categories', compact('categories', 'total', 'active'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon_url' => 'nullable|image|max:2048',
            'parent_category_id' => 'nullable|exists:categories,category_id',
        ]);

        $slug = $this->generateUniqueSlug($validatedData['name']);

        if ($request->hasFile('logo')) {
            $validatedData['logo_url'] = $this->uploadCategoryLogo($request->file('logo'), $slug);
            if (!$validatedData['logo_url']) {
                return back()->withErrors(['logo' => 'Failed to upload logo'])->withInput();
            }
        }

        Category::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
            'slug' => $slug,
            'parent_category_id' => $validatedData['parent_category_id'] ?? null,
            'icon_url' => $validatedData['logo_url'] ?? null,
            'is_active' => true,
            'display_order' => Category::max('display_order') + 1,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Category added successfully.');
    }

    protected function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    protected function uploadCategoryLogo($file, $slug)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $randomString = Str::random(6);
            $fileName = $slug . '-' . $randomString . '.' . $extension;
            return $file->storeAs('Category', $fileName, 'public_html');
        } catch (\Exception $e) {
            logger()->error('Category logo upload failed: ' . $e->getMessage());
            return false;
        }
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with associated products');
        }

        // Prevent deletion if category has children
        if ($category->children()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with subcategories');
        }

        // Delete the logo file if exists (using same storage disk as store)
        if ($category->icon_url && Storage::disk('public_html')->exists($category->icon_url)) {
            Storage::disk('public_html')->delete($category->icon_url);
        }

        $category->delete();

        return redirect()->route('admin.categories')
            ->with('success', 'Category deleted successfully');
    }
}
