<?php

namespace App\Http\Controllers;

use App\Models\CustomerSegment;
use App\Models\ProductRecommendation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /** Cache TTL: 6 hours (in seconds) — refresh after data mining runs */
    private const CACHE_TTL = 21600;

    public function index()
    {
        // ----------------------------------------------------------------
        // KPI Metrics
        // ----------------------------------------------------------------
        $totalRevenue = Cache::remember('kpi.total_revenue', self::CACHE_TTL, function () {
            return (float) DB::table('orders')
                ->where('orders.order_status', 'delivered')
                ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
                ->selectRaw('COALESCE(SUM(order_items.price + order_items.freight_value), 0) AS total')
                ->value('total');
        });

        $totalOrders = Cache::remember('kpi.total_orders', self::CACHE_TTL, function () {
            return (int) DB::table('orders')
                ->where('order_status', 'delivered')
                ->count();
        });

        $activeCustomers = Cache::remember('kpi.active_customers', self::CACHE_TTL, function () {
            return (int) DB::table('orders')
                ->where('order_status', 'delivered')
                ->distinct()
                ->count('customer_id');
        });

        // ----------------------------------------------------------------
        // Monthly Revenue Trend — last 24 months, ordered chronologically
        // ----------------------------------------------------------------
        $monthlyRevenue = Cache::remember('chart.monthly_revenue', self::CACHE_TTL, function () {
            return DB::table('orders')
                ->where('orders.order_status', 'delivered')
                ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
                ->selectRaw("DATE_FORMAT(orders.order_purchase_timestamp, '%Y-%m') AS month")
                ->selectRaw('ROUND(SUM(order_items.price + order_items.freight_value), 2) AS revenue')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(fn ($row) => [
                    'month'   => $row->month,
                    'revenue' => (float) $row->revenue,
                ]);
        });

        // ----------------------------------------------------------------
        // K-Means Segment Distribution
        // ----------------------------------------------------------------
        $segmentDistribution = Cache::remember('chart.segment_distribution', self::CACHE_TTL, function () {
            return CustomerSegment::select('segment_label', DB::raw('COUNT(*) AS total'))
                ->groupBy('segment_label')
                ->orderByDesc('total')
                ->get();
        });

        // ----------------------------------------------------------------
        // Top 10 Products with MBA Recommendations
        // ----------------------------------------------------------------
        $recommendations = Cache::remember('table.recommendations', self::CACHE_TTL, function () {
            return ProductRecommendation::query()
                ->select(
                    'product_id',
                    DB::raw('GROUP_CONCAT(recommended_product_id ORDER BY confidence DESC SEPARATOR ", ") AS recommended_products'),
                    DB::raw('ROUND(MAX(confidence), 2) AS max_confidence'),
                    DB::raw('COUNT(*) AS rec_count'),
                )
                ->groupBy('product_id')
                ->orderByDesc('max_confidence')
                ->limit(10)
                ->get();
        });

        // ----------------------------------------------------------------
        // Additional KPI: Average Order Value
        // ----------------------------------------------------------------
        $avgOrderValue = $totalOrders > 0
            ? round($totalRevenue / $totalOrders, 2)
            : 0.0;

        return view('dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'activeCustomers',
            'avgOrderValue',
            'monthlyRevenue',
            'segmentDistribution',
            'recommendations',
        ));
    }
}
