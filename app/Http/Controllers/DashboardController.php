<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $ordersQuery = DB::table('orders')->where('order_status', 'delivered');
        
        if ($startDate && $endDate) {
            $ordersQuery->whereBetween('order_purchase_timestamp', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // 1. KPI Metrics
        $totalRevenue = (float) (clone $ordersQuery)
            ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
            ->selectRaw('COALESCE(SUM(order_items.price + order_items.freight_value), 0) AS total')
            ->value('total');

        $totalOrders = (int) (clone $ordersQuery)->count();

        $activeCustomers = (int) (clone $ordersQuery)
            ->distinct()
            ->count('customer_id');

        $avgOrderValue = $totalOrders > 0
            ? round($totalRevenue / $totalOrders, 2)
            : 0.0;

        // 2. Monthly Revenue Trend
        $monthlyRevenue = (clone $ordersQuery)
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

        // 3. Customer Segment Distribution
        $segmentDistribution = DB::table('customer_segments')
            ->select('segment_label', DB::raw('COUNT(*) AS total'))
            ->groupBy('segment_label')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => (object) [
                'segment_label' => $row->segment_label,
                'total'         => (int) $row->total,
            ]);

        // 4. Top 10 Product Recommendations
        $recommendations = DB::table('product_recommendations')
            ->select(
                'product_id',
                DB::raw('GROUP_CONCAT(recommended_product_id ORDER BY confidence DESC SEPARATOR ", ") AS recommended_products'),
                DB::raw('ROUND(MAX(confidence), 2) AS max_confidence'),
                DB::raw('COUNT(*) AS rec_count'),
            )
            ->groupBy('product_id')
            ->orderByDesc('max_confidence')
            ->limit(10)
            ->get()
            ->map(fn ($row) => (object) [
                'product_id'           => $row->product_id,
                'recommended_products' => $row->recommended_products,
                'max_confidence'       => $row->max_confidence,
                'rec_count'            => $row->rec_count,
            ]);

        // 5. Geomap Data (Cached for performance)
        $geomapData = Cache::remember('geomap_data', 3600, function () {
            // Check if geolocations table exists to prevent errors during initial setup
            if (!\Illuminate\Support\Facades\Schema::hasTable('geolocations')) {
                return collect([]);
            }
            return DB::table('geolocations')
                ->select('geolocation_state as state', 'geolocation_city as city', 
                         DB::raw('AVG(geolocation_lat) as lat'), 
                         DB::raw('AVG(geolocation_lng) as lng'),
                         DB::raw('COUNT(*) as weight'))
                ->groupBy('geolocation_state', 'geolocation_city')
                ->having('weight', '>', 50)
                ->get();
        });

        // 6. Top 10 Product Categories (Bar Chart)
        $topCategories = collect([]);
        if (\Illuminate\Support\Facades\Schema::hasTable('product_category_name_translations')) {
            $topCategories = (clone $ordersQuery)
                ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.product_id')
                ->leftJoin('product_category_name_translations', 'products.product_category_name', '=', 'product_category_name_translations.product_category_name')
                ->select(
                    DB::raw('COALESCE(product_category_name_translations.product_category_name_english, products.product_category_name, "Unknown") as category'),
                    DB::raw('COUNT(order_items.order_item_id) as total_sold')
                )
                ->groupBy('category')
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(compact(
                'totalRevenue', 'totalOrders', 'activeCustomers', 'avgOrderValue',
                'monthlyRevenue', 'segmentDistribution', 'recommendations', 'geomapData', 'topCategories'
            ));
        }

        return view('dashboard', compact(
            'totalRevenue', 'totalOrders', 'activeCustomers', 'avgOrderValue',
            'monthlyRevenue', 'segmentDistribution', 'recommendations', 'geomapData', 'topCategories'
        ));
    }

    /**
     * Run data mining engine via background job or process.
     */
    public function runDataMining()
    {
        try {
            // Check if already running
            if (Cache::get('data_mining_status') === 'running') {
                return response()->json([
                    'success' => false,
                    'message' => 'Proses data mining sedang berjalan di latar belakang.',
                ], 429);
            }

            // Dispatch to Queue
            \App\Jobs\RunDataMiningJob::dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Proses mining ditambahkan ke antrean latar belakang.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Data mining dispatch exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check the current status of the data mining job.
     */
    public function checkMiningStatus()
    {
        $status = Cache::get('data_mining_status', 'none');
        
        return response()->json([
            'status' => $status
        ]);
    }
}
