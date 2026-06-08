<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ProductStockConstant;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $productsQb = Product::orderBy('created_at', 'desc');

        if ($request->has('filters')) {
            $productsQb = $productsQb->where(function ($query) use ($request) {
                if ($request->has('filters.search')) {
                    $query->where('name', 'like', '%' . $request->filters['search'] . '%')
                        ->orWhere('sku', 'like', '%' . $request->filters['search'] . '%');
                }
                if ($request->has('filters.stock')) {
                    $query->where('stock', $request->filters['stock']);
                }
                if ($request->has('filters.status')) {
                    $query->where('is_active', $request->filters['status']);
                }
            })->whereHas('productCategory', function ($query) use ($request) {
                if ($request->has('filters.category')) {
                    $query->where('name', 'like', '%' . $request->filters['category'] . '%');
                }
            });
        }

        $allProducts = $productsQb->get();
        $paginatedProducts = $productsQb->paginate(20);

        $lowStockThreshold = (int) setting_value(\App\Constants\SettingConstant::PRODUCT_LOW_STOCK_THRESHOLD);

        $resolveStockState = function ($product) use ($lowStockThreshold) {
            if ($product->stock > $lowStockThreshold) {
                return ProductStockConstant::IN_STOCK;
            }
            if ($product->stock > 0) {
                return ProductStockConstant::LOW_STOCK;
            }
            return ProductStockConstant::OUT_OF_STOCK;
        };

        $inventoryCounts = [
            ProductStockConstant::IN_STOCK => 0,
            ProductStockConstant::LOW_STOCK => 0,
            ProductStockConstant::OUT_OF_STOCK => 0,
        ];

        $activeCount = 0;
        $inactiveCount = 0;
        $categories = [];

        foreach ($allProducts as $product) {
            $state = $resolveStockState($product);
            $inventoryCounts[$state]++;

            if ($product->is_active) {
                $activeCount++;
            } else {
                $inactiveCount++;
            }

            $categoryName = $product->productCategory?->name ?? 'Uncategorized';
            $categories[$categoryName] = ($categories[$categoryName] ?? 0) + 1;
        }

        ksort($categories);
        
        return view('pages.admin.products.products_list', [
            'products' => $paginatedProducts,
            'totalProducts' => $allProducts->count(),
            'inventoryCounts' => $inventoryCounts,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'categories' => $categories,
            'lowStockThreshold' => $lowStockThreshold,
            'resolveStockState' => $resolveStockState,
            'filters' => $request->filters ?? [],
        ]);
    }

    public function edit(Request $request)
    {
        $product = $request->id ? Product::find($request->id) : new Product();
        $productCategories = ProductCategory::all();
        $attributeOptions = $product->attributeOptions;
        
        return view('pages.admin.products.product_edit', [
            'product' => $product,
            'productCategories' => $productCategories,
            'attributeOptions' => $attributeOptions,
        ]);
    }

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'sku' => 'required|string|max:255',
            'image' => 'nullable|image|max:1024',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer',
            'category' => 'required|exists:product_categories,id',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $product = $request->id ? Product::find($request->id) : new Product();
        $product->sku = $request->sku;
        $product->name = $request->name;
        $product->slug = $request->slug ?? Str::slug($request->name, '_');
        $product->description = $request->description;
        $product->price = $request->price;
        $product->discount_price = $request->discount_price ?? 0;
        $product->stock = $request->stock;
        $product->is_active = $request->is_active == 'on' ? 1 : 0;
        $product->product_category_id = $request->category;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $product->image = 'products/' . $imageName;
        }

        $product->save();

        return redirect()->route('admin.product.edit', $product->id)->with('success', 'Product saved successfully');
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

    public function addAttributeOption(Request $request)
    {
        $messages = [
            'attribute_id.required' => 'Attribute is required',
            'attribute_id.exists' => 'Attribute not found',
            'attribute_option_id.required' => 'Attribute option is required',
            'attribute_option_id.exists' => 'Attribute option not found',
        ]; 

        $validation = Validator::make($request->all(), [
            'attribute_id' => 'required|exists:attributes,id',
            'attribute_option_id' => 'required|exists:attribute_options,id',
        ], $messages);


        if ($validation->fails()) {
            return response()->json(['success' => false, 'errors' => $validation->errors()->toArray()]);
        }

        $product = Product::find($request->product_id);
        $product->attributeOptions()->syncWithoutDetaching([$request->attribute_option_id]);

        $htmlContent = view('pages.admin.products._product_edit_attributes', [
            'attributeOptions' => $product->attributeOptions]
        )->render();

        return response()->json(['success' => true, 'html' => $htmlContent]);
    }

    public function removeAttributeOption(Request $request)
    {
        $product = Product::find($request->product_id);
        $product->attributeOptions()->detach($request->attribute_option_id);

        $htmlContent = view('pages.admin.products._product_edit_attributes', [
            'attributeOptions' => $product->attributeOptions]
        )->render();

        return response()->json(['success' => true, 'html' => $htmlContent]);
    }
}