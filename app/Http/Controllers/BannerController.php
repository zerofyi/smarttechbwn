<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::with('category')->orderBy('display_order')->get();
        $total = $banners->count();
        $active = $banners->where('is_active', true)->count();
        $categories = Category::where('is_active', true)->get();

        return view('admin.banners', compact('banners', 'total', 'active', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'required|image|max:2048',
                'target_url' => 'nullable|url',
                'type' => 'required|in:main,promo,category',
                'category_id' => 'nullable|exists:categories,category_id',
                'is_active' => 'boolean',
                'display_order' => 'required|integer',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            // Handle image upload
            $imagePath = $this->uploadBannerImage($request->file('image'));
            if (!$imagePath) {
                return back()->withErrors(['image' => 'Failed to upload image'])->withInput();
            }

            Banner::create([
                'title' => $validatedData['title'],
                'image_url' => $imagePath,
                'target_url' => $validatedData['target_url'] ?? null,
                'type' => $validatedData['type'],
                'category_id' => $validatedData['type'] === 'category' ? $validatedData['category_id'] : null,
                'is_active' => $validatedData['is_active'] ?? true,
                'display_order' => $validatedData['display_order'],
                'start_date' => $validatedData['start_date'] ?? null,
                'end_date' => $validatedData['end_date'] ?? null,
            ]);

            return redirect()->route('admin.banners')->with('success', 'Banner added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $banner = Banner::findOrFail($id);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'nullable|image|max:2048',
                'target_url' => 'nullable|url',
                'type' => 'required|in:main,promo,category',
                'category_id' => 'nullable|exists:categories,category_id',
                'is_active' => 'boolean',
                'display_order' => 'required|integer',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            // Handle image upload if new image is provided
            if ($request->hasFile('image')) {
                // Delete old image
                if ($banner->image_url && Storage::disk('public_html')->exists($banner->image_url)) {
                    Storage::disk('public_html')->delete($banner->image_url);
                }

                $imagePath = $this->uploadBannerImage($request->file('image'));
                if (!$imagePath) {
                    return back()->withErrors(['image' => 'Failed to upload image'])->withInput();
                }
                $validatedData['image_url'] = $imagePath;
            }

            $banner->update([
                'title' => $validatedData['title'],
                'target_url' => $validatedData['target_url'] ?? null,
                'type' => $validatedData['type'],
                'category_id' => $validatedData['type'] === 'category' ? $validatedData['category_id'] : null,
                'is_active' => $validatedData['is_active'] ?? $banner->is_active,
                'display_order' => $validatedData['display_order'],
                'start_date' => $validatedData['start_date'] ?? null,
                'end_date' => $validatedData['end_date'] ?? null,
                'image_url' => $validatedData['image_url'] ?? $banner->image_url,
            ]);

            return redirect()->route('admin.banners')->with('success', 'Banner updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Delete the image file if exists
        if ($banner->image_url && Storage::disk('public_html')->exists($banner->image_url)) {
            Storage::disk('public_html')->delete($banner->image_url);
        }

        $banner->delete();

        return redirect()->route('admin.banners')
            ->with('success', 'Banner deleted successfully');
    }

    protected function uploadBannerImage($file)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $randomString = Str::random(10);
            $fileName = 'banner-' . time() . '-' . $randomString . '.' . $extension;
            return $file->storeAs('Banners', $fileName, 'public_html');
        } catch (\Exception $e) {
            logger()->error('Banner image upload failed: ' . $e->getMessage());
            return false;
        }
    }
}
