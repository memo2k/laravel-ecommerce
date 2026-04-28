<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    public function index(Request $request) 
    {
        $name = $request->name;

        switch ($name) {
            case 'attribute':
                return $this->attribute($request->term);
            case 'attribute-option':
                return $this->attributeOption($request->term, $request->attribute_id);
            default:
                return response()->json([]);
                break;
        }
    }

    public function attribute($term)
    {
        $attributes = Attribute::where('name', 'like', '%' . $term . '%')->get();
        return response()->json($attributes);
    }

    public function attributeOption($term, $attributeId)
    {
        $attributeOptions = AttributeOption::where('attribute_id', $attributeId)->where('name', 'like', '%' . $term . '%')->get();
        return response()->json($attributeOptions);
    }
}
