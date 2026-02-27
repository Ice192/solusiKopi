<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingService
{
    public function getRecapData($dateFrom, $dateTo, $outletId = null, $recapMode = 'daily')
    {
        $dateFrom = Carbon::parse($dateFrom);
        $dateTo = Carbon::parse($dateTo);
        $periods = collect();
        if ($recapMode === 'weekly') {
            $start = $dateFrom->copy()->startOfWeek();
            $end = $dateTo->copy();
            while ($start <= $end) {
                $weekStart = $start->copy();
                $weekEnd = $start->copy()->endOfWeek();
                $periods->push([
                    'label' => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M'),
                    'from' => $weekStart->copy(),
                    'to' => $weekEnd->copy()->endOfDay(),
                ]);
                $start->addWeek();
            }
        } elseif ($recapMode === 'monthly') {
            $start = $dateFrom->copy()->startOfMonth();
            $end = $dateTo->copy();
            while ($start <= $end) {
                $monthStart = $start->copy();
                $monthEnd = $start->copy()->endOfMonth();
                $periods->push([
                    'label' => $monthStart->format('M Y'),
                    'from' => $monthStart->copy(),
                    'to' => $monthEnd->copy()->endOfDay(),
                ]);
                $start->addMonth();
            }
        } else {
            $period = new \DatePeriod($dateFrom, new \DateInterval('P1D'), $dateTo->copy()->addDay());
            foreach ($period as $date) {
                $carbonDate = Carbon::instance($date);
                $periods->push([
                    'label' => $carbonDate->format('d M'),
                    'from' => $carbonDate->copy(),
                    'to' => $carbonDate->copy()->endOfDay(),
                ]);
            }
        }
        $recapData = $periods->map(function($p) use ($outletId) {
            $omzet = Order::where('payment_status', 'paid')
                ->whereBetween('ordered_at', [$p['from'], $p['to']])
                ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
                ->sum('total_amount');
            $orders = Order::whereBetween('ordered_at', [$p['from'], $p['to']])
                ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
                ->count();
            $produk = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->whereBetween('orders.ordered_at', [$p['from'], $p['to']])
                ->where('orders.payment_status', 'paid')
                ->when($outletId, fn($q) => $q->where('orders.outlet_id', $outletId))
                ->select(
                    'categories.name as category_name',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.quantity * order_items.price_at_order) as total_revenue')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('total_quantity')
                ->get();
            return [
                'label' => $p['label'],
                'omzet' => $omzet,
                'orders' => $orders,
                'produk' => $produk,
            ];
        });
        return [
            'periods' => $periods,
            'recapData' => $recapData,
        ];
    }

    public function getSummaryStats($dateFrom, $dateTo, $outletId = null)
    {
        $baseQuery = Order::query();
        $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $to   = $dateTo   ? Carbon::parse($dateTo)->endOfDay()   : Carbon::now()->endOfDay();
        $baseQuery->whereBetween('ordered_at', [$from, $to]);
        if ($outletId) {
            $baseQuery->where('outlet_id', $outletId);
        }
        return [
            'total_revenue' => $baseQuery->clone()->where('payment_status', 'paid')->sum('total_amount') ?? 0,
            'total_orders' => $baseQuery->clone()->count(),
            'paid_orders' => $baseQuery->clone()->where('payment_status', 'paid')->count(),
            'pending_orders' => $baseQuery->clone()->where('payment_status', 'pending')->count(),
            'average_order_value' => $baseQuery->clone()->where('payment_status', 'paid')->avg('total_amount') ?? 0,
        ];
    }

    public function getTopProducts($dateFrom, $dateTo, $outletId = null, $limit = 10)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.ordered_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()])
            ->where('orders.payment_status', 'paid')
            ->when($outletId, function($query) use ($outletId) {
                return $query->where('orders.outlet_id', $outletId);
            })
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price_at_order) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOutletPerformance($dateFrom, $dateTo)
    {
        $from = Carbon::parse($dateFrom)->startOfDay();
        $to = Carbon::parse($dateTo)->endOfDay();
        return Outlet::withCount(['orders' => function($query) use ($from, $to) {
            $query->whereBetween('ordered_at', [$from, $to]);
        }])
        ->withSum(['orders' => function($query) use ($from, $to) {
            $query->whereBetween('ordered_at', [$from, $to])
                  ->where('payment_status', 'paid');
        }], 'total_amount')
        ->get();
    }

    public function getChartData($dateFrom, $dateTo, $outletId = null)
    {
        $from = Carbon::parse($dateFrom)->startOfDay();
        $to = Carbon::parse($dateTo)->endOfDay();
        $query = Order::query()->where('payment_status', 'paid')->whereBetween('ordered_at', [$from, $to]);
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        $daily = $query->select(
            DB::raw('DATE(ordered_at) as date'),
            DB::raw('SUM(total_amount) as omzet'),
            DB::raw('COUNT(*) as orders')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        $labels = $daily->pluck('date');
        $omzet = $daily->pluck('omzet');
        $orders = $daily->pluck('orders');
        return [
            'labels' => $labels,
            'omzet' => $omzet,
            'orders' => $orders,
        ];
    }
}
