<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::all();

        return view('pages.admin.attributes.attributes_list', [
            'attributes' => $attributes,
        ]);
    }

    public function edit(Request $request)
    {
        $attribute = $request->id ? Attribute::find($request->id) : new Attribute();

        return view('pages.admin.attributes.attribute_edit', [
            'attribute' => $attribute,
        ]);
    }

    public function save(Request $request)
    {
        $attribute = $request->id ? Attribute::find($request->id) : new Attribute();
        $attribute->name = $request->name;
        $attribute->description = $request->description;

        $submittedOptionIds = [];
        if(!empty($request->options)) {
            foreach($request->options as $option) {
                $attributeOption = !empty($option['id']) ? AttributeOption::find($option['id']) : new AttributeOption();
                $attributeOption->attribute_id = $attribute->id;
                $attributeOption->name = $option['name'] ?? '';
                $attributeOption->description = $option['description'] ?? '';
                $attributeOption->save();

                $submittedOptionIds[] = $attributeOption->id;
            }
        }

        $attribute->attributeOptions()
            ->whereNotIn('id', $submittedOptionIds)
            ->delete();

        $attribute->save();

        return redirect()->route('admin.attribute.edit', $attribute->id)->with('success', 'Attribute saved successfully');
    }
}
