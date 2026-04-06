<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('pages.admin.products.products_list');
    }

    public function edit()
    {
        $productCategories = ProductCategory::all();
        
        return view('pages.admin.products.product_edit', [
            'productCategories' => $productCategories,
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'compare_at_price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);
    }
}