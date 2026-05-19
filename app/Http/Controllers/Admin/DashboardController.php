<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $period = $period ?? '30';

        $totalRevenue = Order::where('created_at', '>=', now()->subDays($period))->sum('total_amount');

        $previousRevenue = Order::whereBetween('created_at', [
                now()->subDays($period * 2),
                now()->subDays($period),
            ])->sum('total_amount');

        $totalOrders = Order::where('created_at', '>=', now()->subDays($period))->count();

        $previousOrders = Order::whereBetween('created_at', [
                now()->subDays($period * 2),
                now()->subDays($period),
            ])->count();
            
        $avgOrderValue = Order::where('created_at', '>=', now()->subDays($period))->avg('total_amount');

        $previousAvgOrderValue = Order::whereBetween('created_at', [
            now()->subDays($period * 2),
            now()->subDays($period),
        ])->avg('total_amount');

        $totalCustomers = User::where('created_at', '>=', now()->subDays($period))->count();

        $previousCustomers = User::whereBetween('created_at', [
            now()->subDays($period * 2),
            now()->subDays($period),
        ])->count();

        $revenueDelta = $previousRevenue == 0 ? 0 : ($totalRevenue - $previousRevenue) / $previousRevenue * 100;
        $ordersDelta = $previousOrders == 0 ? 0 : ($totalOrders - $previousOrders) / $previousOrders * 100;
        $avgOrderValueDelta = $previousAvgOrderValue == 0 ? 0 : ($avgOrderValue - $previousAvgOrderValue) / $previousAvgOrderValue * 100;
        $customersDelta = $previousCustomers == 0 ? 0 : ($totalCustomers - $previousCustomers) / $previousCustomers * 100;
        
        $totalProducts = Product::count();

        $activeProducts = Product::where('is_active', true)->get();

        $outOfStockProducts = Product::where('stock', '<=', 0)->get();

        $lowStockThreshold = setting_value(\App\Constants\SettingConstant::PRODUCT_LOW_STOCK_THRESHOLD);
        $lowStockProducts = Product::where('stock', '<=', $lowStockThreshold)->get();

        $inStockProducts = Product::where('stock', '>', 0)->get();

        $recentOrders = Order::orderBy('created_at', 'desc')->take(10)->get();

        $orderProducts = OrderProduct::groupBy('product_id')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->orderBy('total_quantity', 'desc')
            ->take(10)
            ->pluck('product_id')
            ->toArray();

        $topProducts = Product::whereIn('id', $orderProducts)->get();

        $dailyTotals = Order::selectRaw('DATE(created_at) as day, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('day')
            ->pluck('revenue', 'day')  // for a single column you'd use pluck; for both use ->get()->keyBy('day')
            ->all();

        $dailyOrders = Order::selectRaw('DATE(created_at) as day, COUNT(*) as orders')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('day')
            ->pluck('orders', 'day')
            ->all();

        $labels = [];
        $revenue = [];
        $orders = [];

        for ($i = $period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString(); // "2026-05-19"
            $labels[]  = now()->subDays($i)->format('M j'); // "May 19"
            $revenue[] = (float) ($dailyTotals[$date] ?? 0);
            $orders[]  = (int)   ($dailyOrders[$date] ?? 0);
        }

        $revenueTrend = [
            'labels'  => $labels,
            'revenue' => $revenue,
            'orders'  => $orders,
        ];

        $orderStatusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        return view('pages.admin.dashboard', [
            'period' => $period,
            'totalRevenue' => $totalRevenue,
            'revenueDelta' => $revenueDelta,
            'totalOrders' => $totalOrders,
            'ordersDelta' => $ordersDelta,
            'avgOrderValue' => $avgOrderValue,
            'avgOrderValueDelta' => $avgOrderValueDelta,
            'totalCustomers' => $totalCustomers,
            'customersDelta' => $customersDelta,
            'totalProducts' => $totalProducts,
            'activeProducts' => $activeProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'lowStockThreshold' => $lowStockThreshold,
            'lowStockProducts' => $lowStockProducts,
            'inStockProducts' => $inStockProducts,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'revenueTrend' => $revenueTrend,
            'orderStatusCounts' => $orderStatusCounts,
        ]);
    }
}
