<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = in_array($request->query('period'), ['7', '30', '90'], true)
            ? $request->query('period')
            : '30';

        $since = now()->subDays((int) $period);

        $totalRevenue = Order::where('created_at', '>=', $since)->sum('total_amount');
        $totalOrders = Order::where('created_at', '>=', $since)->count();

        $lowStockThreshold = setting_value(\App\Constants\SettingConstant::PRODUCT_LOW_STOCK_THRESHOLD);
        $lowStockProducts = Product::where('stock', '<=', $lowStockThreshold)
            ->orderBy('stock')
            ->get();

        $recentOrders = Order::orderByDesc('created_at')->take(10)->get();

        return view('pages.admin.dashboard', [
            'period' => $period,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'lowStockThreshold' => $lowStockThreshold,
            'lowStockProducts' => $lowStockProducts,
            'recentOrders' => $recentOrders,
        ]);
    }
}
