<?php

namespace App\Http\Controllers\Admin;

use App\Constants\OrderStatusConstant;
use App\Http\Controllers\Controller;
use App\Mail\OrderStatusMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();

        $statusCounts = array_fill_keys(OrderStatusConstant::ORDER_STATUSES, 0);
        $paymentMethods = [];
        $totalRevenue = 0;
        $needsActionCount = 0;

        foreach ($orders as $order) {
            $status = $order->status;
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            } else {
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
            }

            $totalRevenue += (float) $order->total_amount;

            if (in_array($status, [
                OrderStatusConstant::PENDING,
                OrderStatusConstant::PROCESSING,
                OrderStatusConstant::UNPAID,
            ], true)) {
                $needsActionCount++;
            }

            $method = $order->payment_method ?: 'Unknown';
            $paymentMethods[$method] = ($paymentMethods[$method] ?? 0) + 1;
        }

        ksort($paymentMethods);

        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return view('pages.admin.orders.orders_list', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'avgOrderValue' => $avgOrderValue,
            'needsActionCount' => $needsActionCount,
            'paymentMethods' => $paymentMethods,
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
