<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function index()
    {
        $cartData = $this->cartRepository->getCartData();
        return view('pages.site.cart.cart', ['cartData' => $cartData]);
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

    public function removeProduct(Request $request)
    {
        $cartProduct = CartProduct::query()->find($request->product_id);
        $cartProduct->delete();

        $cartData = $this->cartRepository->getCartData();
        $cartProductsContent = view('pages.site.cart._cart_products', ['cartData' => $cartData])->render();
        $cartPreviewContent = view('pages.components.cart_preview', ['cartData' => $cartData])->render();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully',
            'cartProductsContent' => $cartProductsContent,
            'cartPreviewContent' => $cartPreviewContent,
        ]);
    }

    public function updateQuantity(Request $request)
    {
        $cartProduct = CartProduct::query()->find($request->product_id);
        $cartProduct->quantity = $request->action == 'increment' ? $cartProduct->quantity + 1 : $cartProduct->quantity - 1;
        $cartProduct->save();

        if ($cartProduct->quantity <= 0) {
            $cartProduct->delete();
        }

        $cartData = $this->cartRepository->getCartData();
        $cartProductsContent = view('pages.site.cart._cart_products', ['cartData' => $cartData])->render();
        $cartPreviewContent = view('pages.components.cart_preview', ['cartData' => $cartData])->render();
        
        return response()->json([
            'success' => true,
            'message' => 'Quantity updated successfully',
            'cartProductsContent' => $cartProductsContent,
            'cartPreviewContent' => $cartPreviewContent,
        ]);
    }
}
