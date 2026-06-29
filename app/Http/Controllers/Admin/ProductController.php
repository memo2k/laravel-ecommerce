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
        $lowStockThreshold = (int) setting_value(\App\Constants\SettingConstant::PRODUCT_LOW_STOCK_THRESHOLD);

        $totalProducts = Product::count();
        $activeCount = Product::where('is_active', true)->count();
        $lowStockCount = Product::where('stock', '<=', $lowStockThreshold)->count();

        $query = Product::with('productCategory');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        $stock = $request->input('stock');
        if ($stock && in_array($stock, ProductStockConstant::PRODUCT_STOCK_STATES, true)) {
            if ($stock === ProductStockConstant::OUT_OF_STOCK) {
                $query->where('stock', '<=', 0);
            } elseif ($stock === ProductStockConstant::LOW_STOCK) {
                $query->where('stock', '>', 0)->where('stock', '<=', $lowStockThreshold);
            } else {
                $query->where('stock', '>', $lowStockThreshold);
            }
        }

        $status = $request->input('status');
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $category = $request->input('category');
        if ($category === 'Uncategorized') {
            $query->whereNull('product_category_id');
        } elseif ($category) {
            $query->whereHas('productCategory', fn ($q) => $q->where('name', $category));
        }

        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        match ($sort) {
            'price' => $query->orderBy('price', $direction),
            'stock' => $query->orderBy('stock', $direction),
            default => $query->orderBy('name', $direction),
        };

        $products = $query->paginate(20)->withQueryString();

        $categories = ProductCategory::withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->pluck('products_count', 'name')
            ->all();

        $uncategorizedCount = Product::whereNull('product_category_id')->count();
        if ($uncategorizedCount > 0) {
            $categories['Uncategorized'] = $uncategorizedCount;
        }

        $resolveStockState = function ($product) use ($lowStockThreshold) {
            if ($product->stock <= 0) {
                return ProductStockConstant::OUT_OF_STOCK;
            }
            if ($product->stock <= $lowStockThreshold) {
                return ProductStockConstant::LOW_STOCK;
            }
            return ProductStockConstant::IN_STOCK;
        };

        return view('pages.admin.products.products_list', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'activeCount' => $activeCount,
            'lowStockCount' => $lowStockCount,
            'lowStockThreshold' => $lowStockThreshold,
            'categories' => $categories,
            'resolveStockState' => $resolveStockState,
            'search' => $search,
            'stock' => $stock,
            'status' => $status,
            'category' => $category,
            'sort' => $sort,
            'direction' => $direction,
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
