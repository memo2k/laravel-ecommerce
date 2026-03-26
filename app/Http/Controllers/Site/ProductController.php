<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = $products ?? collect(range(1, 12))->map(function ($i) {
            return (object) [
                'id' => $i,
                'name' => "Demo product $i",
                'category' => ['Accessories', 'Shoes', 'Bags', 'Jackets'][($i - 1) % 4],
                'price' => 19 + ($i * 3),
                'old_price' => $i % 3 === 0 ? (29 + ($i * 3)) : null,
            ];
        });

        $categories = $categories ?? ['Accessories', 'Shoes', 'Bags', 'Jackets', 'T-Shirts'];
        $selectedCategory = request('category');
        $sortBy = request('sort', 'newest');
        $search = request('q', '');

        return view('pages.site.products.products_list', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'sortBy' => $sortBy,
            'search' => $search,
        ]);
    }

    public function show()
    {
        return view('pages.site.products.product');
    }
}
