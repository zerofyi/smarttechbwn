<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeValueController extends Controller
{
    public function index($attribute_id)
    {
        $attribute = Attribute::withCount('values')
            ->with(['values' => function($query) {
                $query->orderBy('value')->withCount('productAttributes');
            }])
            ->findOrFail($attribute_id);

        return view('admin.attribute_values', compact('attribute'));
    }

    public function store(Request $request, $attribute_id)
    {
        $request->validate([
            'values' => 'required|string|max:1000'
        ]);

        $values = $this->parseInputValues($request->values);

        DB::transaction(function() use ($attribute_id, $values) {
            foreach ($values as $value) {
                $trimmedValue = trim($value);
                if (!empty($trimmedValue)) {
                    AttributeValue::firstOrCreate([
                        'attribute_id' => $attribute_id,
                        'value' => $trimmedValue
                    ]);
                }
            }
        });

        return redirect()->route('admin.attribute-values', $attribute_id)
            ->with('success', count($values) . ' values added successfully');
    }

    public function destroy($attribute_value_id)
    {
        $attributeValue = AttributeValue::findOrFail($attribute_value_id);
        $attribute_id = $attributeValue->attribute_id;

        // Prevent deletion if value is in use
        if ($attributeValue->productAttributes()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete value used in product variants');
        }

        $attributeValue->delete();

        return redirect()->route('admin.attribute-values', $attribute_id)
            ->with('success', 'Value deleted successfully');
    }

    protected function parseInputValues($values)
    {
        return array_unique(
            array_filter(
                array_map('trim',
                    preg_split('/[,]+/', $values)
                )
            )
        );
    }
}
