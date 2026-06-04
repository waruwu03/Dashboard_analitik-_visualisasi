<?php

namespace App\Http\Controllers;

use App\Models\CustomerSegment;
use App\Models\Order;
use App\Models\ProductRecommendation;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $completedOrders = Order::query()
            ->where('order_status', 'delivered')
            ->with('items')
            ->select('order_id', 'order_purchase_timestamp')
            ->get();

        $totalRevenue = DB::table('orders')
            ->where('order_status', 'delivered')
            ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
            ->selectRaw('SUM(order_items.price + order_items.freight_value) as total_revenue')
            ->value('total_revenue') ?: 0;

        $totalOrders = DB::table('orders')
            ->where('order_status', 'delivered')
            ->count();

        $activeCustomers = DB::table('orders')
            ->where('order_status', 'delivered')
            ->distinct('customer_id')
            ->count('customer_id');

        $monthlyRevenue = DB::table('orders')
            ->where('order_status', 'delivered')
            ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
            ->selectRaw("DATE_FORMAT(order_purchase_timestamp, '%Y-%m') as month")
            ->selectRaw('SUM(order_items.price + order_items.freight_value) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $segmentDistribution = CustomerSegment::select('segment_label', DB::raw('count(*) as total'))
            ->groupBy('segment_label')
            ->orderByDesc('total')
            ->get();

        $topRecommendations = ProductRecommendation::query()
            ->select('product_id', DB::raw('GROUP_CONCAT(recommended_product_id ORDER BY confidence DESC SEPARATOR ", ") as recommended_products'), DB::raw('MAX(confidence) as max_confidence'))
            ->groupBy('product_id')
            ->orderByDesc('max_confidence')
            ->limit(10)
            ->get();

        $monthlyRevenue = $monthlyRevenue->map(fn ($item) => [
            'month' => $item->month,
            'revenue' => (float) $item->revenue,
        ]);

        return view('dashboard', [
            'totalRevenue' => (float) $totalRevenue,
            'totalOrders' => (int) $totalOrders,
            'activeCustomers' => (int) $activeCustomers,
            'monthlyRevenue' => $monthlyRevenue,
            'segmentDistribution' => $segmentDistribution,
            'recommendations' => $topRecommendations,
        ]);
    }
}
