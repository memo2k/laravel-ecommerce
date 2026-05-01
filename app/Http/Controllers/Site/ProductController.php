<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->q ?? '');
        $selectedCategory = $request->category;
        $sortBy = $request->sort ?? 'newest';
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price;
        $selectedAttributeOptions = $request->attribute_options;

        $cacheKey = 'products.index.'.md5(json_encode([
            'q' => $search,
            'category' => $selectedCategory,
            'sort' => $sortBy,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'attribute_options' => $selectedAttributeOptions,
        ]));

        if (Cache::has($cacheKey)) {
            $viewParams = Cache::get($cacheKey);
        } else {
            $categories = ProductCategory::query()
                ->orderBy('name')
                ->get(['id', 'name']);
    
            $products = ProductRepository::getProducts($search, $selectedCategory, $minPrice, $maxPrice, $sortBy, $selectedAttributeOptions);

            if ($selectedCategory) {
                $productCategoryAttributeIds = ProductCategory::find($selectedCategory)
                    ->attributes()
                    ->pluck('attributes.id')
                    ->toArray();

                $attributeOptions = AttributeOption::with('attribute')
                    ->whereIn('attribute_id', $productCategoryAttributeIds)
                    ->whereHas('products', function ($query) use ($products) {
                        $query->whereIn('products.id', $products->pluck('id'));
                    })
                    ->get()
                    ->groupBy(fn ($option) => $option->attribute->name);
            }

            $viewParams = [
                'products' => $products,
                'categories' => $categories,
                'selectedCategory' => $selectedCategory,
                'sortBy' => $sortBy,
                'search' => $search,
                'attributeOptions' => $attributeOptions ?? null,
                'selectedAttributeOptions' => $selectedAttributeOptions,
                'lowStockThreshold' => setting_value(\App\Constants\SettingConstant::PRODUCT_LOW_STOCK_THRESHOLD),
            ];

            Cache::put($cacheKey, $viewParams, now()->addMinutes(15));
        }

        return view('pages.site.products.products_list', $viewParams);
    }

    public function show(Request $request)
    {
        $cacheKey = 'products.show.'.md5($request->slug);
        if (Cache::has($cacheKey)) {
            $product = Cache::get($cacheKey);
        } else {
            $product = Product::query()
                ->with('productCategory')
                ->where('slug', $request->slug)
                ->firstOrFail();

            Cache::put($cacheKey, $product, now()->addMinutes(15));
        }

        return view('pages.site.products.product', [
            'product' => $product,
            'lowStockThreshold' => setting_value(\App\Constants\SettingConstant::PRODUCT_LOW_STOCK_THRESHOLD),
        ]);
    }
}
