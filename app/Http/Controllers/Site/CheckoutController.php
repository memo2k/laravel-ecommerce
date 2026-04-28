<?php

namespace App\Http\Controllers\Site;

use App\Constants\OrderStatusConstant;
use App\Constants\PaymentMethodConstant;
use App\Http\Controllers\Controller;
use App\Mail\OrderPlaceMail;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Repositories\CartRepository;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        return view('pages.site.checkout.checkout', ['cartData' => $cartData]);
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

        $initialStatus = $request->payment_method === PaymentMethodConstant::CARD
            ? OrderStatusConstant::UNPAID
            : OrderStatusConstant::PENDING;

        $order = Order::create([
            'user_id' => Auth::user()->id ?? null,
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
            $successUrl = route('checkout.order-summary', ['id' => $order->id]).'&session_id={CHECKOUT_SESSION_ID}';
            
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
            
        return redirect()->route('checkout.order-summary', ['id' => $order->id])->with('success', 'Order created successfully');
    }

    public function orderSummary(Request $request)
    {
        $order = Order::query()
            ->with(['orderProducts.product'])
            ->findOrFail($request->query('id'));

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
}
