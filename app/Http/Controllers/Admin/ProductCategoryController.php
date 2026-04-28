<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $productCategories = ProductCategory::all();

        return view('pages.admin.product_categories.product_categories_list', [
            'productCategories' => $productCategories,
        ]);
    }

    public function edit(Request $request)
    {
        $productCategory = $request->id ? ProductCategory::find($request->id) : new ProductCategory();
        $attributes = $productCategory->attributes;

        return view('pages.admin.product_categories.product_category_edit', [
            'productCategory' => $productCategory,
            'attributes' => $attributes,
        ]);  
    }

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $productCategory = $request->id ? ProductCategory::find($request->id) : new ProductCategory();
        $productCategory->name = $request->name;
        $productCategory->slug = $request->slug;
        $productCategory->description = $request->description;
        // $productCategory->is_active = $request->is_active;
        // $productCategory->sort_order = $request->sort_order;
        $productCategory->save();

        return redirect()->route('admin.product-categories')->with('success', 'Product category saved successfully');
    }

    public function delete(Request $request)
    {
        $productCategory = ProductCategory::find($request->product_category_id);
        $productCategory->delete();

        return response()->json(['success' => true]);
    }

    public function addAttribute(Request $request)
    {
        $productCategory = ProductCategory::find($request->product_category_id);
        $productCategory->attributes()->syncWithoutDetaching([$request->attribute_id]);

        $htmlContent = view('pages.admin.product_categories._product_category_edit_attributes', [
            'attributes' => $productCategory->attributes]
        )->render();

        return response()->json(['success' => true, 'html' => $htmlContent]);
    }
    
    public function removeAttribute(Request $request)
    {
        $productCategory = ProductCategory::find($request->product_category_id);
        $productCategory->attributes()->detach($request->attribute_id);

        $htmlContent = view('pages.admin.product_categories._product_category_edit_attributes', [
            'attributes' => $productCategory->attributes]
        )->render();

        return response()->json(['success' => true, 'html' => $htmlContent]);
    }
}
