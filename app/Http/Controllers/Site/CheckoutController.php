<?php

namespace App\Http\Controllers\Site;

use App\Constants\OrderStatusConstant;
use App\Constants\PaymentMethodConstant;
use App\Http\Controllers\Controller;
use App\Mail\OrderPlaceMail;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Repositories\CartRepository;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Laravel\Cashier\Cashier;

class CheckoutController extends Controller
{
    protected $cartRepository;

    protected $cartService;

    public function __construct(CartRepository $cartRepository, CartService $cartService)
    {
        $this->cartRepository = $cartRepository;
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartData = $this->cartRepository->getCartData();

        if ($cartData['totalProducts'] === 0) {
            return redirect()->route('cart.index');
        }

        $userAddress = Auth::user()?->userAddress;

        return view('pages.site.checkout.checkout', [
            'cartData' => $cartData,
            'userAddress' => $userAddress,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:40',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'payment_method' => 'required|in:'.implode(',', array_keys(PaymentMethodConstant::PAYMENT_METHODS)),
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cartData = $this->cartRepository->getCartData();

        $products = Product::whereIn('id', array_column($cartData['items'], 'product_id'))->get();
        foreach ($products as $product) {
            if ($product->stock < array_column($cartData['items'], 'quantity', 'product_id')[$product->id]) {
                return redirect()->back()->with('error', 'The quantity of product ' . $product->name . ' is not available in stock.');
            }
        }

        $initialStatus = $request->payment_method === PaymentMethodConstant::CARD
            ? OrderStatusConstant::UNPAID
            : OrderStatusConstant::PENDING;

        $order = Order::create([
            'user_id' => Auth::user()->id ?? null,
            'products_total_amount' => $cartData['itemsTotalAmount'],
            'shipping_amount' => $cartData['shippingAmount'],
            'total_amount' => $cartData['totalPrice'],
            'status' => $initialStatus,
            'payment_method' => $request->payment_method,
            'delivery_address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'country' => $request->country,
            'customer_email' => $request->email,
            'customer_phone' => $request->phone,
            'customer_first_name' => $request->first_name,
            'customer_last_name' => $request->last_name,
            'customer_notes' => $request->notes,
        ]);

        foreach ($cartData['items'] as $item) {
            $price = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
            $total = $price * $item['quantity'];

            $product = Product::find($item['product_id']);
            $product->stock -= $item['quantity'];
            $product->save();
            
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $price,
                'total' => $total,
            ]);
        }
        
        $this->cartService->clearCart();
        
        if ($request->payment_method === PaymentMethodConstant::CARD) {
            $successUrl = $this->orderSummaryUrl($order).'&session_id={CHECKOUT_SESSION_ID}';

            return $request->user()->checkoutCharge(
                round($order->total_amount * 100),
                'Order #'.$order->id,
                1,
                [
                    'success_url' => $successUrl,
                    'cancel_url' => route('checkout.index'),
                ]
            );
        }

        Mail::to($order->customer_email)->send(new OrderPlaceMail($order));

        return redirect()->to($this->orderSummaryUrl($order))->with('success', 'Order created successfully');
    }

    public function orderSummary(Request $request, Order $order)
    {
        $this->authorizeOrderAccess($request, $order);

        $order->load(['orderProducts.product']);

        if ($order->status === OrderStatusConstant::UNPAID && $request->filled('session_id')) {
            try {
                $session = Cashier::stripe()->checkout->sessions->retrieve($request->query('session_id'));

                if ($session->payment_status === 'paid') {
                    $order->status = OrderStatusConstant::PENDING;
                    $order->save();

                    Mail::to($order->customer_email)->send(new OrderPlaceMail($order));
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to verify Stripe session for order #'.$order->id.': '.$e->getMessage());
            }
        }

        return view('pages.site.checkout.order_summary', ['order' => $order]);
    }

    private function orderSummaryUrl(Order $order): string
    {
        return URL::temporarySignedRoute(
            'checkout.order-summary',
            now()->addDays(30),
            ['order' => $order->id],
        );
    }

    private function authorizeOrderAccess(Request $request, Order $order): void
    {
        $hasValidSignature = $request->hasValidSignatureWhileIgnoring(['session_id']);
        $ownsOrder = Auth::check() && $order->user_id === Auth::id();

        if (! $hasValidSignature && ! $ownsOrder) {
            abort(403);
        }
    }
}
