<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\ReportingService;

class ReportingController extends Controller
{
    protected $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Display reporting dashboard
     */
    public function index(Request $request)
    {
        if ($this->isCashierOnlyUser($request)) {
            return $this->indexForCashier($request);
        }

        $dateFrom = $request->get('date_from', Carbon::today()->subDays(30));
        $dateTo = $request->get('date_to', Carbon::today());
        $outletId = $request->get('outlet_id');
        $recapMode = $request->get('recap_mode', 'daily');

        // Ambil data dari service
        $revenueStats = $this->reportingService->getSummaryStats($dateFrom, $dateTo, $outletId);
        $recap = $this->reportingService->getRecapData($dateFrom, $dateTo, $outletId, $recapMode);
        $chart = $this->reportingService->getChartData($dateFrom, $dateTo, $outletId);
        $topProducts = $this->reportingService->getTopProducts($dateFrom, $dateTo, $outletId);
        $outletPerformance = $this->reportingService->getOutletPerformance($dateFrom, $dateTo);
        // TODO: tambahkan pemanggilan service untuk statistik lain jika perlu

        // Data lain yang belum di-service (sementara, akan dipindah)
        $orderStatusStats = [];
        $paymentMethodStats = [];
        $dailyRevenue = collect();
        $tableUtilization = collect();
        $weeklyRecap = collect();
        $monthlyRecap = collect();
        $foodDrinkRecap = collect();
        $dates = collect();
        $recapTabs = [
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
        ];

        // Data recap dari service
        $recapData = $recap['recapData'] ?? collect();
        $periods = $recap['periods'] ?? collect();

        // Untuk AJAX partial
        if ($request->ajax()) {
            return view('console.reporting.partials.recap-area', compact(
                'revenueStats',
                'weeklyRecap',
                'monthlyRecap',
                'foodDrinkRecap',
                'recapTabs',
                'recapMode',
                'recapData',
                'dateFrom',
                'dateTo',
                'dates',
                'outletId',
            ));
        }
        return view('console.reporting.index', compact(
            'revenueStats',
            'orderStatusStats',
            'paymentMethodStats',
            'topProducts',
            'dailyRevenue',
            'outletPerformance',
            'tableUtilization',
            'dateFrom',
            'dateTo',
            'outletId',
            'recapTabs',
            'recapMode',
            'recapData',
            'dates',
            'weeklyRecap',
            'monthlyRecap',
            'foodDrinkRecap',
        ));
    }

    private function indexForCashier(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))
            : Carbon::today();
        $dateFrom = $date->copy()->startOfDay();
        $dateTo = $date->copy()->endOfDay();
        $outletId = $request->get('outlet_id');

        $revenueStats = $this->reportingService->getSummaryStats($dateFrom, $dateTo, $outletId);

        $orders = Order::with(['table', 'outlet'])
            ->whereBetween('ordered_at', [$dateFrom, $dateTo])
            ->when($outletId, fn($query) => $query->where('outlet_id', $outletId))
            ->orderByDesc('ordered_at')
            ->get();

        $cashPaid = $orders->where('payment_status', 'paid')
            ->where('payment_method', 'cash')
            ->count();
        $qrisPaid = $orders->where('payment_status', 'paid')
            ->where('payment_method', 'QRIS')
            ->count();
        $pendingPayments = $orders->where('payment_status', 'pending')->count();

        return view('console.reporting.cashier', compact(
            'date',
            'dateFrom',
            'dateTo',
            'outletId',
            'revenueStats',
            'orders',
            'cashPaid',
            'qrisPaid',
            'pendingPayments',
        ));
    }

    /**
     * Generate sample daily revenue data for demonstration
     */
    private function generateSampleDailyRevenue($dateFrom, $dateTo)
    {
        $from = $dateFrom ? Carbon::parse($dateFrom) : Carbon::now()->subDays(30);
        $to = $dateTo ? Carbon::parse($dateTo) : Carbon::now();

        $data = collect();
        $current = $from->copy();

        while ($current <= $to) {
            $data->push((object) [
                'date' => $current->format('Y-m-d'),
                'revenue' => rand(50000, 500000),
                'orders_count' => rand(1, 10)
            ]);
            $current->addDay();
        }

        return $data;
    }

    /**
     * Export detailed report
     */
    public function export(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::today()->subDays(30));
        $dateTo = $request->get('date_to', Carbon::today());
        $outletId = $request->get('outlet_id');
        $reportType = $request->get('report_type', 'orders');

        $filename = $reportType . '_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportType, $dateFrom, $dateTo, $outletId) {
            $file = fopen('php://output', 'w');

            switch ($reportType) {
                case 'orders':
                    $this->exportOrdersReport($file, $dateFrom, $dateTo, $outletId);
                    break;
                case 'products':
                    $this->exportProductsReport($file, $dateFrom, $dateTo, $outletId);
                    break;
                case 'revenue':
                    $this->exportRevenueReport($file, $dateFrom, $dateTo, $outletId);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export orders report
     */
    private function exportOrdersReport($file, $dateFrom, $dateTo, $outletId)
    {
        fputcsv($file, [
            'Order Number', 'Customer', 'Outlet', 'Table', 'Status',
            'Payment Status', 'Payment Method', 'Total Amount', 'Subtotal',
            'Tax', 'Service Fee', 'Discount', 'Order Date', 'Completed Date'
        ]);

        $query = Order::with(['user', 'outlet', 'table'])
            ->whereBetween('ordered_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        $orders = $query->get();

        foreach ($orders as $order) {
            fputcsv($file, [
                $order->order_number,
                $order->user ? $order->user->name : ($order->guest_info['name'] ?? 'Guest'),
                $order->outlet->name,
                $order->table->table_number,
                $order->status,
                $order->payment_status,
                $order->payment_method,
                $order->total_amount,
                $order->subtotal,
                $order->other_fee,
                $order->additional_fee,
                $order->discount_amount ?? 0,
                $order->ordered_at->format('Y-m-d H:i:s'),
                $order->completed_at ? $order->completed_at->format('Y-m-d H:i:s') : '',
            ]);
        }
    }

    /**
     * Export products report
     */
    private function exportProductsReport($file, $dateFrom, $dateTo, $outletId)
    {
        fputcsv($file, [
            'Product Name', 'Category', 'Total Quantity Sold', 'Total Revenue',
            'Average Price', 'Orders Count'
        ]);

        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.ordered_at', [$dateFrom, $dateTo])
            ->where('orders.payment_status', 'paid');

        if ($outletId) {
            $query->where('orders.outlet_id', $outletId);
        }

        $products = $query->select(
            'products.name',
            'categories.name as category_name',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.quantity * order_items.price_at_order) as total_revenue'),
            DB::raw('AVG(order_items.price_at_order) as avg_price'),
            DB::raw('COUNT(DISTINCT orders.id) as orders_count')
        )
        ->groupBy('products.id', 'products.name', 'categories.name')
        ->orderBy('total_quantity', 'desc')
        ->get();

        foreach ($products as $product) {
            fputcsv($file, [
                $product->name,
                $product->category_name,
                $product->total_quantity,
                $product->total_revenue,
                $product->avg_price,
                $product->orders_count,
            ]);
        }
    }

    /**
     * Export revenue report
     */
    private function exportRevenueReport($file, $dateFrom, $dateTo, $outletId)
    {
        fputcsv($file, [
            'Date', 'Revenue', 'Orders Count', 'Average Order Value',
            'Payment Method', 'Status'
        ]);

        $query = Order::whereBetween('ordered_at', [$dateFrom, $dateTo])
            ->where('payment_status', 'paid');

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        $revenue = $query->select(
            DB::raw('DATE(ordered_at) as date'),
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw('COUNT(*) as orders_count'),
            DB::raw('AVG(total_amount) as avg_order_value'),
            'payment_method',
            'status'
        )
        ->groupBy('date', 'payment_method', 'status')
        ->orderBy('date')
        ->get();

        foreach ($revenue as $row) {
            fputcsv($file, [
                $row->date,
                $row->revenue,
                $row->orders_count,
                $row->avg_order_value,
                $row->payment_method,
                $row->status,
            ]);
        }
    }

    /**
     * Export summary report (weekly, monthly, foodDrink, all)
     */
    public function exportSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::today()->subDays(30));
        $dateTo = $request->get('date_to', Carbon::today());
        $outletId = $request->get('outlet_id');
        $type = $request->get('type', 'all');

        $filename = 'summary_report_' . $type . '_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($dateFrom, $dateTo, $outletId, $type) {
            $file = fopen('php://output', 'w');
            if ($type === 'weekly' || $type === 'all') {
                fputcsv($file, ['Rekap Penjualan per Minggu']);
                fputcsv($file, ['Minggu', 'Omzet', 'Transaksi']);
                $start = Carbon::parse($dateFrom)->startOfWeek();
                $end = Carbon::parse($dateTo);
                while ($start <= $end) {
                    $weekStart = $start->copy();
                    $weekEnd = $start->copy()->endOfWeek();
                    $omzet = Order::where('payment_status', 'paid')
                        ->whereBetween('ordered_at', [$weekStart, $weekEnd])
                        ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
                        ->sum('total_amount');
                    $orders = Order::whereBetween('ordered_at', [$weekStart, $weekEnd])
                        ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
                        ->count();
                    fputcsv($file, [
                        $weekStart->format('d M') . ' - ' . $weekEnd->format('d M'),
                        $omzet,
                        $orders
                    ]);
                    $start->addWeek();
                }
                fputcsv($file, []);
            }
            if ($type === 'monthly' || $type === 'all') {
                fputcsv($file, ['Rekap Penjualan per Bulan']);
                fputcsv($file, ['Bulan', 'Omzet', 'Transaksi']);
                $start = Carbon::parse($dateFrom)->startOfMonth();
                $end = Carbon::parse($dateTo);
                while ($start <= $end) {
                    $monthStart = $start->copy();
                    $monthEnd = $start->copy()->endOfMonth();
                    $omzet = Order::where('payment_status', 'paid')
                        ->whereBetween('ordered_at', [$monthStart, $monthEnd])
                        ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
                        ->sum('total_amount');
                    $orders = Order::whereBetween('ordered_at', [$monthStart, $monthEnd])
                        ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
                        ->count();
                    fputcsv($file, [
                        $monthStart->format('M Y'),
                        $omzet,
                        $orders
                    ]);
                    $start->addMonth();
                }
                fputcsv($file, []);
            }
            if ($type === 'foodDrink' || $type === 'all') {
                fputcsv($file, ['Rincian Penjualan Makanan & Minuman']);
                fputcsv($file, ['Kategori', 'Qty Terjual', 'Total Omzet']);
                $rows = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->whereBetween('orders.ordered_at', [$dateFrom, $dateTo])
                    ->where('orders.payment_status', 'paid')
                    ->when($outletId, fn($q) => $q->where('orders.outlet_id', $outletId))
                    ->select(
                        'categories.name as category',
                        DB::raw('SUM(order_items.quantity) as qty'),
                        DB::raw('SUM(order_items.quantity * order_items.price_at_order) as omzet')
                    )
                    ->groupBy('categories.id', 'categories.name')
                    ->orderByDesc('qty')
                    ->get();
                foreach ($rows as $row) {
                    fputcsv($file, [
                        $row->category,
                        $row->qty,
                        $row->omzet
                    ]);
                }
                fputcsv($file, []);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get real-time statistics for AJAX
     */
    public function getRealTimeStats()
    {
        $today = Carbon::today();

        $stats = [
            'today_orders' => Order::whereDate('ordered_at', $today)->count(),
            'today_revenue' => Order::whereDate('ordered_at', $today)
                ->where('payment_status', 'paid')
                ->sum('total_amount') ?? 0,
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
        ];

        return response()->json($stats);
    }

    private function isCashierOnlyUser(Request $request): bool
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasAnyRole')) {
            return false;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return false;
        }

        return $user->hasAnyRole(['kasir', 'cashier']);
    }
}
