<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('pages.site.products.products_list');
    }

    public function show()
    {
        return view('pages.site.products.product');
    }
}
