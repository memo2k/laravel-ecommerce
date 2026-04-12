<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function addToCart(Request $request)
    {
        $user = Auth::user();
        $cart = null;

        if ($user) {
            $cart = Cart::query()->firstOrCreate([
                'user_id' => $user->id,
            ]);
        } else {
            $guestCartId = $request->session()->get('guest_cart_id');

            if ($guestCartId) {
                $cart = Cart::query()->find($guestCartId);
            }

            if (!$cart) {
                $cart = Cart::query()->create(['user_id' => null]);
                $request->session()->put('guest_cart_id', $cart->id);
            }
        }

        $cartProduct = CartProduct::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $request->product_id,
        ]);

        $cartProduct->quantity = ($cartProduct->exists ? $cartProduct->quantity : 0) + (int) ($request->quantity ?? 1);
        $cartProduct->save();

        $cartData = (new CartRepository())->getCartData();

        $htmlContent = view('pages.components.cart_preview', ['cartData' => $cartData])->render();

        return response()->json(['success' => true, 'message' => 'Product added to cart successfully', 'htmlContent' => $htmlContent]);
    }

    public function removeFromCart(Request $request)
    {
        $cartProduct = CartProduct::query()->find($request->product_id);
        $cartProduct->delete();

        $cartData = (new CartRepository())->getCartData();
        $htmlContent = view('pages.components.cart_preview', ['cartData' => $cartData])->render();

        return response()->json(['success' => true, 'message' => 'Product removed from cart successfully', 'htmlContent' => $htmlContent]);
    }

    public function updateCartQuantity(Request $request)
    {
        $cartProduct = CartProduct::query()->find($request->product_id);
        $cartProduct->quantity = $request->action == 'increment' ? $cartProduct->quantity + 1 : $cartProduct->quantity - 1;
        $cartProduct->save();

        if ($cartProduct->quantity <= 0) {
            $cartProduct->delete();
        }

        $cartData = (new CartRepository())->getCartData();
        $htmlContent = view('pages.components.cart_preview', ['cartData' => $cartData])->render();

        return response()->json(['success' => true, 'message' => 'Cart quantity updated successfully', 'htmlContent' => $htmlContent]);
    }

}
