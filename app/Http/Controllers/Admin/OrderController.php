<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Constants\OrderStatusConstant;
use App\Mail\OrderStatusMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $totalOrders = Order::count();
        $totalRevenue = (float) Order::sum('total_amount');
        $needsActionCount = Order::whereIn('status', [
            OrderStatusConstant::PENDING,
            OrderStatusConstant::PROCESSING,
            OrderStatusConstant::UNPAID,
        ])->count();

        $query = Order::query();

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $searchId = ltrim($search, '#');
            $query->where(function ($q) use ($search, $searchId) {
                $q->where('id', 'like', '%' . $searchId . '%')
                    ->orWhere('customer_first_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_last_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_email', 'like', '%' . $search . '%');
            });
        }

        $status = $request->input('status');
        if ($status && in_array($status, OrderStatusConstant::ORDER_STATUSES, true)) {
            $query->where('status', $status);
        }

        $period = $request->input('period');
        if (in_array($period, ['7', '30', '90'], true)) {
            $query->where('created_at', '>=', now()->subDays((int) $period));
        }

        $payment = $request->input('payment');
        if ($payment) {
            if ($payment === 'unknown') {
                $query->where(function ($q) {
                    $q->whereNull('payment_method')->orWhere('payment_method', '');
                });
            } else {
                $query->where(DB::raw('LOWER(payment_method)'), strtolower($payment));
            }
        }

        $sort = $request->input('sort', 'date');
        $direction = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        if ($sort === 'total') {
            $query->orderBy('total_amount', $direction);
        } else {
            $query->orderBy('created_at', $direction);
        }

        $orders = $query->paginate(20)->withQueryString();

        $paymentMethods = Order::query()
            ->selectRaw('COALESCE(NULLIF(payment_method, ""), "Unknown") as method, COUNT(*) as count')
            ->groupBy('method')
            ->pluck('count', 'method')
            ->all();
        ksort($paymentMethods);

        return view('pages.admin.orders.orders_list', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'needsActionCount' => $needsActionCount,
            'paymentMethods' => $paymentMethods,
            'search' => $search,
            'status' => $status,
            'period' => $period,
            'payment' => $payment,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function view(Request $request)
    {
        $order = Order::find($request->id);

        return view('pages.admin.orders.order_view', [
            'order' => $order,
        ]);
    }

    public function edit(Request $request)
    {
        $order = $request->id ? Order::find($request->id) : new Order();
        
        return view('pages.admin.orders.order_edit', [
            'order' => $order,
        ]);
    }

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
            'payment_method' => 'required|string',
            'delivery_address' => 'required|string',
            'city' => 'required|string',
            'zip' => 'required|string',
            'country' => 'required|string',
            'customer_phone' => 'required|string',
            'customer_email' => 'required|email',
            'customer_first_name' => 'required|string',
            'customer_last_name' => 'required|string',
            'customer_notes' => 'nullable|string',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        
        $order = $request->id ? Order::find($request->id) : new Order();
        $previousStatus = $order->status;

        $order->total_amount = $request->total_amount;
        $order->status = $request->status;
        $order->payment_method = $request->payment_method;
        $order->delivery_address = $request->delivery_address;
        $order->city = $request->city;
        $order->state = $request->state;
        $order->zip = $request->zip;
        $order->country = $request->country;
        $order->customer_phone = $request->customer_phone;
        $order->customer_email = $request->customer_email;
        $order->customer_first_name = $request->customer_first_name;
        $order->customer_last_name = $request->customer_last_name;
        $order->customer_notes = $request->customer_notes;
        $order->save();
        
        if($request->status !== $previousStatus) {
            $newOrderStatusMail = new OrderStatusMail($order, $request->status);
            Mail::to($order->customer_email)->send($newOrderStatusMail);
        }

        return redirect()->route('admin.order.view', $order->id)->with('success', 'Order saved successfully');
    }
}
