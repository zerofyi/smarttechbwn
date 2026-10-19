<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->get();
        $total = $brands->count();
        $active = $brands->where('is_active', true)->count();

        return view('admin.brands', compact('brands', 'total', 'active'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:brands,name',
                'description' => 'nullable|string',
                'logo' => 'required|image|max:2048',
                'website' => 'nullable',
            ]);

            // Generate unique slug
            $slug = $this->generateUniqueSlug($validatedData['name']);

            // Handle logo upload
            $logoPath = $this->uploadBrandLogo($request->file('logo'), $slug);
            if (!$logoPath) {
                return back()->withErrors(['logo' => 'Failed to upload logo'])->withInput();
            }

            Brand::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
                'slug' => $slug,
                'website' => $validatedData['website'] ?? null,
                'logo_url' => $logoPath,
                'is_active' => true,
            ]);

            return redirect()->route('admin.brands')->with('success', 'Brand added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        // Prevent deletion if brand has products
        if ($brand->products()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete brand with associated products');
        }

        // Delete the logo file if exists
        if ($brand->logo_url && Storage::disk('public_html')->exists($brand->logo_url)) {
            Storage::disk('public_html')->delete($brand->logo_url);
        }

        $brand->delete();

        return redirect()->route('admin.brands')
            ->with('success', 'Brand deleted successfully');
    }

    // Helper methods
    protected function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Brand::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    protected function uploadBrandLogo($file, $slug)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $randomString = Str::random(6);
            $fileName = $slug . '-' . $randomString . '.' . $extension;
            return $file->storeAs('Brand', $fileName, 'public_html');
        } catch (\Exception $e) {
            logger()->error('Brand logo upload failed: ' . $e->getMessage());
            return false;
        }
    }
}
