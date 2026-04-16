<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index()
    {
        $search = trim((string) request('q', ''));
        $selectedCategory = request('category');
        $sortBy = request('sort', 'newest');
        $minPrice = request('min_price');
        $maxPrice = request('max_price');

        $cacheKey = 'products.index.'.md5(json_encode([
            'q' => $search,
            'category' => $selectedCategory,
            'sort' => $sortBy,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
        ]));

        if (Cache::has($cacheKey)) {
            $viewParams = Cache::get($cacheKey);
        } else {
            $categories = ProductCategory::query()
                ->orderBy('name')
                ->get(['id', 'name']);

            $productsQuery = Product::query()
                ->with('productCategory')
                ->where('is_active', true);

            if ($search !== '') {
                $productsQuery->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('productCategory', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if (!empty($selectedCategory)) {
                $productsQuery->where('product_category_id', $selectedCategory);
            }

            if ($minPrice !== null && $minPrice !== '') {
                $productsQuery->where('price', '>=', (float) $minPrice);
            }

            if ($maxPrice !== null && $maxPrice !== '') {
                $productsQuery->where('price', '<=', (float) $maxPrice);
            }

            $resolvedSort = $sortBy;
            switch ($sortBy) {
                case 'price_asc':
                    $productsQuery->orderBy('price');
                    break;
                case 'price_desc':
                    $productsQuery->orderByDesc('price');
                    break;
                case 'name_asc':
                    $productsQuery->orderBy('name');
                    break;
                case 'newest':
                default:
                    $resolvedSort = 'newest';
                    $productsQuery->latest();
                    break;
            }

            $viewParams = [
                'products' => $productsQuery->get(),
                'categories' => $categories,
                'selectedCategory' => $selectedCategory,
                'sortBy' => $resolvedSort,
                'search' => $search,
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

        return view('pages.site.products.product', ['product' => $product]);
    }
}
