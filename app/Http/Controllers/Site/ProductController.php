<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $search = trim((string) request('q', ''));
        $selectedCategory = request('category');
        $sortBy = request('sort', 'newest');
        $minPrice = request('min_price');
        $maxPrice = request('max_price');

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
                $sortBy = 'newest';
                $productsQuery->latest();
                break;
        }

        $products = $productsQuery->get();

        return view('pages.site.products.products_list', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'sortBy' => $sortBy,
            'search' => $search,
        ]);
    }

    public function show(Request $request)
    {
        $product = Product::query()
            ->with('productCategory')
            ->where('slug', $request->slug)
            ->firstOrFail();

        return view('pages.site.products.product', [
            'product' => $product,
        ]);
    }
}
