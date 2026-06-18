<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI Metrics
        $totalRevenue = (float) DB::table('orders')
            ->where('orders.order_status', 'delivered')
            ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
            ->selectRaw('COALESCE(SUM(order_items.price + order_items.freight_value), 0) AS total')
            ->value('total');

        $totalOrders = (int) DB::table('orders')
            ->where('order_status', 'delivered')
            ->count();

        $activeCustomers = (int) DB::table('orders')
            ->where('order_status', 'delivered')
            ->distinct()
            ->count('customer_id');

        $avgOrderValue = $totalOrders > 0
            ? round($totalRevenue / $totalOrders, 2)
            : 0.0;

        // Monthly Revenue Trend
        $monthlyRevenue = DB::table('orders')
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

        // Customer Segment Distribution
        $segmentDistribution = DB::table('customer_segments')
            ->select('segment_label', DB::raw('COUNT(*) AS total'))
            ->groupBy('segment_label')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => (object) [
                'segment_label' => $row->segment_label,
                'total'         => (int) $row->total,
            ]);

        // Top 10 Product Recommendations (Market Basket Analysis)
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

    /**
     * Run data mining engine via AJAX from the dashboard sidebar.
     */
    public function runDataMining()
    {
        try {
            $exitCode = Artisan::call('data-mining:run');
            $output = Artisan::output();
            $output = mb_convert_encoding($output, 'UTF-8', 'UTF-8, ISO-8859-1, WINDOWS-1252');

            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data mining selesai! Halaman akan dimuat ulang.',
                    'output'  => $output,
                ]);
            }

            \Illuminate\Support\Facades\Log::error('Data mining failed. Output: ' . $output);

            return response()->json([
                'success' => false,
                'message' => 'Data mining gagal. Periksa log untuk detail.',
                'output'  => $output,
            ], 500);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Data mining exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
