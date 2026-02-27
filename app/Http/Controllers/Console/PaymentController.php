<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        if ($this->isCashierOnlyUser($request)) {
            return $this->indexForCashier($request);
        }

        $table = null;
        $orders = collect();

        if ($request->filled('table_number')) {
            $keyword = trim((string) $request->table_number);

            $table = Table::where('table_number', $keyword)
                ->orWhere('table_code', $keyword)
                ->first();

            if ($table) {
                $orders = Order::with(['orderItems.product', 'payments'])
                    ->where('table_id', $table->id)
                    ->whereNotIn('status', [Order::STATUS_CANCELLED, Order::STATUS_COMPLETED])
                    ->orderByDesc('ordered_at')
                    ->get();
            }
        }

        return view('console.payments.index', compact('table', 'orders'));
    }

    private function indexForCashier(Request $request): View
    {
        $table = null;
        $cashOrders = collect();
        $tableKeyword = trim((string) $request->input('table_number', ''));

        $cashOrdersQuery = Order::with(['table', 'orderItems.product', 'payments'])
            ->where('payment_method', Order::PAYMENT_METHOD_CASH)
            ->where('payment_status', Order::PAYMENT_STATUS_PENDING)
            ->whereNotIn('status', [Order::STATUS_CANCELLED, Order::STATUS_COMPLETED]);

        if ($tableKeyword !== '') {
            $table = Table::where('table_number', $tableKeyword)
                ->orWhere('table_code', $tableKeyword)
                ->first();

            if ($table) {
                $cashOrders = (clone $cashOrdersQuery)
                    ->where('table_id', $table->id)
                    ->orderBy('ordered_at')
                    ->get();
            }
        } else {
            $cashOrders = (clone $cashOrdersQuery)
                ->orderBy('ordered_at')
                ->get();
        }

        $today = Carbon::today();
        $qrisOrders = Order::with([
            'table',
            'payments' => fn($query) => $query->latest(),
        ])
            ->where('payment_method', Order::PAYMENT_METHOD_QRIS)
            ->whereDate('ordered_at', $today)
            ->orderByDesc('ordered_at')
            ->limit(20)
            ->get();

        $dailyReport = [
            'date' => $today,
            'total_orders' => Order::whereDate('ordered_at', $today)->count(),
            'paid_orders' => Order::whereDate('ordered_at', $today)
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->count(),
            'pending_cash_orders' => Order::where('payment_method', Order::PAYMENT_METHOD_CASH)
                ->where('payment_status', Order::PAYMENT_STATUS_PENDING)
                ->whereNotIn('status', [Order::STATUS_CANCELLED, Order::STATUS_COMPLETED])
                ->count(),
            'cash_paid_orders' => Order::whereDate('ordered_at', $today)
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->where('payment_method', Order::PAYMENT_METHOD_CASH)
                ->count(),
            'qris_paid_orders' => Order::whereDate('ordered_at', $today)
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->where('payment_method', Order::PAYMENT_METHOD_QRIS)
                ->count(),
            'total_revenue' => (float) Order::whereDate('ordered_at', $today)
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->sum('total_amount'),
        ];

        return view('console.payments.cashier', compact('table', 'cashOrders', 'qrisOrders', 'dailyReport'));
    }

    public function pay(Request $request, Order $order): RedirectResponse
    {
        $isCashierOnlyUser = $this->isCashierOnlyUser($request);

        if ($isCashierOnlyUser) {
            $request->merge(['payment_method' => Order::PAYMENT_METHOD_CASH]);
        } else {
            $request->validate([
                'payment_method' => 'required|in:cash,QRIS',
            ]);
        }

        if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
            return back()->with('info', 'Order ini sudah dibayar.');
        }

        if ($isCashierOnlyUser && strcasecmp((string) $order->payment_method, Order::PAYMENT_METHOD_CASH) !== 0) {
            return back()->with('error', 'Kasir hanya dapat mengonfirmasi pembayaran tunai.');
        }

        DB::transaction(function () use ($order, $request) {
            $method = $request->payment_method;
            $paymentNote = 'Pembayaran dikonfirmasi kasir pada ' . now()->format('d-m-Y H:i:s');

            $order->update([
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'status' => Order::STATUS_COMPLETED,
                'payment_method' => $method,
                'completed_at' => now(),
                'note' => trim(($order->note ? $order->note . ' | ' : '') . $paymentNote),
            ]);

            if ($order->table) {
                $order->table->update([
                    'status' => 'available',
                ]);
            }

            $payment = $order->payments()->latest()->first();
            if ($payment) {
                $payment->update([
                    'method' => $method,
                    'amount' => $order->total_amount,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
            } else {
                Payment::create([
                    'order_id' => $order->id,
                    'method' => $method,
                    'amount' => $order->total_amount,
                    'status' => 'completed',
                    'paid_at' => now(),
                    'payment_gateway_ref' => 'CASHIER_' . $order->order_number,
                ]);
            }
        });

        return redirect()
            ->route('console.payments.index', ['table_number' => $order->table?->table_number])
            ->with('success', 'Pembayaran berhasil. Meja kembali tersedia dan transaksi masuk ke catatan.');
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
