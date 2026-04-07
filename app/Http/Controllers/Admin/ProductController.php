<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        
        return view('pages.admin.products.products_list', [
            'products' => $products,
        ]);
    }

    public function edit(Request $request)
    {
        $product = $request->id ? Product::find($request->id) : new Product();
        $productCategories = ProductCategory::all();
        
        return view('pages.admin.products.product_edit', [
            'product' => $product,
            'productCategories' => $productCategories,
        ]);
    }

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category' => 'required|exists:product_categories,id',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $product = $request->id ? Product::find($request->id) : new Product();
        $product->name = $request->name;
        $product->slug = $request->slug ?? Str::slug($request->name);
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->is_active = $request->is_active == 'on' ? 1 : 0;
        $product->product_category_id = $request->category;

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product saved successfully');
    }

    public function delete(Request $request)
    {
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted successfully']);
    }
}