<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::withCount(['values', 'productAttributes'])
            ->with(['values' => function($query) {
                $query->orderBy('value');
            }])
            ->orderBy('display_name')
            ->get();

        $total = $attributes->count();

        return view('admin.attributes', compact('attributes', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
            'display_name' => 'required|string|max:255',
        ]);

        DB::transaction(function() use ($request) {
            Attribute::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
            ]);
        });

        return redirect()->route('admin.attributes')
            ->with('success', 'Attribute added successfully.');
    }

    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);

        DB::transaction(function() use ($attribute) {
            // Delete associated values first
            $attribute->values()->delete();
            $attribute->delete();
        });

        return redirect()->route('admin.attributes')
            ->with('success', 'Attribute and all its values deleted successfully.');
    }
}
