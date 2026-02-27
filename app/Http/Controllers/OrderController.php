<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    /**
     * Menampilkan menu berdasarkan kode meja.
     */
    public function showMenuByTableCode(Request $request, $table_code = null)
    {
        if ($request->filled('table_code')) {
            $table_code = trim((string) $request->input('table_code'));
        }

        if (!$table_code) {
            $table_code = session('current_table_code');
        }

        $table = null;
        if (!empty($table_code)) {
            $table = Table::where(function ($query) use ($table_code) {
                $query->where('table_number', $table_code)
                    ->orWhere('table_code', $table_code);
            })
                ->where('status', '!=', 'unavailable')
                ->with('outlet')
                ->first();
        }

        // Fallback: jika user belum pilih meja, ambil meja tersedia pertama
        if (!$table) {
            $table = Table::where('status', '!=', 'unavailable')
                ->with('outlet')
                ->orderBy('id')
                ->first();
        }

        if (!$table) {
            return redirect()->route('dashboard')->with('error', 'Belum ada meja yang tersedia.');
        }

        Session::put('current_table_code', $table->table_code ?? $table->table_number);

        $outlet = $table->outlet ?? Outlet::first();
        if (!$outlet) {
            return redirect()->route('dashboard')->with('error', 'Outlet tidak ditemukan.');
        }

        // Fallback non-Livewire: proses add/remove cart via query param.
        // Ini membantu saat event Livewire di client tidak terpanggil.
        $addProductId = $request->filled('add_product') ? (int) $request->input('add_product') : null;
        $removeProductId = $request->filled('remove_product') ? (int) $request->input('remove_product') : null;
        if ($addProductId || $removeProductId) {
            $cartKey = 'guest_cart_' . $table->id;
            $cart = Session::get($cartKey, []);
            $targetProductId = $addProductId ?: $removeProductId;

            $product = Product::where('outlet_id', $outlet->id)
                ->where('is_available', true)
                ->find($targetProductId);

            if ($product) {
                $productId = $product->id;
                if ($addProductId) {
                    if (isset($cart[$productId])) {
                        $cart[$productId]['quantity']++;
                    } else {
                        $cart[$productId] = [
                            'product_id' => $productId,
                            'name' => $product->name ?? 'Produk',
                            'price' => (float) ($product->price ?? 0),
                            'quantity' => 1,
                        ];
                    }
                }

                if ($removeProductId && isset($cart[$productId])) {
                    $cart[$productId]['quantity']--;
                    if (($cart[$productId]['quantity'] ?? 0) <= 0) {
                        unset($cart[$productId]);
                    }
                }

                Session::put($cartKey, $cart);
            }

            $redirectParams = [
                'table_code' => $table->table_code ?? $table->table_number,
                'tab' => $request->input('tab', 'menu'),
            ];

            if ($request->filled('category')) {
                $redirectParams['category'] = trim((string) $request->input('category'));
            }

            return redirect()->route('order.menu', $redirectParams);
        }

        // Ambil kategori dan produk
        $categories = Category::with(['products' => function ($query) use ($outlet) {
            $query->where('outlet_id', $outlet->id)->where('is_available', true);
        }])->whereHas('products', function ($query) use ($outlet) {
            $query->where('outlet_id', $outlet->id)->where('is_available', true);
        })->get();

        // Ambil promo yang aktif
        $promotions = Promotion::where('status', 'active')->get();

        return view('order.menu', compact('categories', 'outlet', 'table', 'promotions'));
    }

    /**
     * Menampilkan riwayat pesanan.
     */
    public function showOrderHistory()
    {
        $orders = collect();
        $user = auth()->user();

        if ($user) {
            // Jika user login, ambil riwayat dari database
            $orders = Order::where('user_id', $user->id)
                            ->orderByDesc('ordered_at')
                            ->with(['outlet', 'table', 'orderItems.product'])
                            ->get();
        } elseif (Session::has('guest_order_ids')) {
            // Jika guest, ambil riwayat dari session
            $guestOrderIds = Session::get('guest_order_ids');
            $orders = Order::whereIn('id', $guestOrderIds)
                            ->orderByDesc('ordered_at')
                            ->with(['outlet', 'table', 'orderItems.product'])
                            ->get();
        } else if(Session::has('guest_order_history')){
            // Jika guest, ambil riwayat dari session guest_order_history
            $guestOrderIdsHistory = Session::get('guest_order_history');
            $orders = Order::whereIn('id', $guestOrderIdsHistory)
                            ->orderByDesc('ordered_at')
                            ->with(['outlet', 'table', 'orderItems.product'])
                            ->get();
        } else {
            // Jika tidak ada riwayat, tampilkan pesan kosong
            $orders = collect();
        }

        $statuses = Order::getStatuses();
        $paymentStatuses = Order::getPaymentStatuses();

        return view('order.history.index', compact('orders', 'statuses', 'paymentStatuses'));
    }

    /**
     * Menampilkan detail pesanan.
     */
    public function showOrderDetail($order_number)
    {
        $order = Order::where('order_number', $order_number)
                      ->with(['outlet', 'table', 'orderItems.product', 'promotion', 'payments'])
                      ->firstOrFail();

        $statuses = Order::getStatuses();
        $paymentStatuses = Order::getPaymentStatuses();

        return view('order.history.show', compact('order', 'statuses', 'paymentStatuses'));
    }

    /**
     * Menampilkan halaman pembayaran QRIS.
     */
    public function showPaymentQris($order_number)
    {
        $order = Order::where('order_number', $order_number)
                      ->with('payments')
                      ->firstOrFail();

        if ($order->payment_status === 'paid' || $order->payment_method !== 'QRIS') {
            return redirect()->route('order.success', $order->order_number)->with('error', 'Pesanan ini sudah dibayar atau bukan metode QRIS.');
        }

        // Cek payment QRIS yang masih pending
        $payment = $order->payments()->where('method', 'qris')->where('status', 'pending')->first();

        if ($payment && $payment->snap_token) {
            $snapToken = $payment->snap_token;
            $midtransError = null;
            $qr_image = null;
            return view('order.payment_qris', compact('order', 'snapToken', 'midtransError', 'qr_image'));
        }

        // Generate order_id unik untuk Midtrans
        $midtransOrderId = $order->order_number . '-' . time();

        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) $order->total_amount > 0 ? (int) $order->total_amount : 1,
            ],
            'item_details' => [
                [
                    'id' => $order->order_number,
                    'price' => (int) $order->total_amount > 0 ? (int) $order->total_amount : 1,
                    'quantity' => 1,
                    'name' => 'Pembayaran Pesanan #' . $order->order_number,
                ]
            ],
            'customer_details' => [
                'first_name' => $order->guest_info['name'] ?? 'Guest',
                'email' => $order->guest_info['email'] ?? 'customer@gmail.com',
                'phone' => $order->guest_info['phone'] ?? '081234567890',
            ],
        ];

        if (auth()->check()) {
            $params['customer_details']['first_name'] = auth()->user()->name;
            $params['customer_details']['email'] = auth()->user()->email;
            $params['customer_details']['phone'] = auth()->user()->phone ?? '081234567890';
        }

        $snapToken = null;
        $midtransError = null;
        $qr_image = null;

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $paymentData = [
                'amount' => $order->total_amount,
                'method' => 'qris',
                'status' => 'pending',
                'payment_gateway_ref' => $midtransOrderId,
                'snap_token' => $snapToken,
            ];

            if ($payment) {
                $payment->update($paymentData);
            } else {
                $order->payments()->create($paymentData);
            }
        } catch (\Exception $e) {
            $midtransError = $e->getMessage();
            Log::error('Midtrans error: ' . $e->getMessage());
        }

        return view('order.payment_qris', compact('order', 'snapToken', 'midtransError', 'qr_image'));
    }

    /**
     * Konfirmasi pembayaran.
     */
    public function confirmPayment($order_number)
    {
        $order = Order::where('order_number', $order_number)
                      ->with(['outlet', 'table', 'orderItems.product'])
                      ->firstOrFail();

        $order->update([
            'payment_status' => 'paid',
            'status' => 'preparing', // Update ke status baru
        ]);

        $order->table->update([
            'status' => 'occupied',
        ]);

        $payment = $order->payments()->first();
        if ($payment) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);
        } else {
            // Jika tidak ada payment, buat yang baru
            $order->payments()->create([
                'amount' => $order->total_amount,
                'method' => 'qris',
                'status' => 'completed',
                'payment_gateway_ref' => $order->order_number . '-' . time(),
                'paid_at' => now(),
            ]);
        }

        return view('order.success', compact('order'));
    }

    /**
     * Mengambil status pembayaran pesanan untuk polling.
     */
    public function getPaymentStatus($order_number)
    {
        $order = Order::where('order_number', $order_number)
        ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'payment_status' => $order->payment_status,
            'order_status' => $order->status
        ]);
    }

    /**
     * Membatalkan pesanan
     */
    public function cancelOrder($order_number)
    {
        try {
            $order = Order::where('order_number', $order_number)->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.'
                ], 404);
            }

            // Validasi status order yang bisa dibatalkan
            if (!in_array($order->status, ['pending', 'preparing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak dapat dibatalkan karena status: ' . ucfirst($order->status)
                ], 400);
            }

            // Validasi payment status
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak dapat dibatalkan karena sudah dibayar.'
                ], 400);
            }

            // Update status order menjadi cancelled
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'cancelled'
            ]);

            // Update status table menjadi available jika ada
            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }

            // Cancel payment jika ada
            if ($order->payments()->exists()) {
                $order->payments()->update([
                    'status' => 'cancelled'
                ]);
            }

            // Log pembatalan
            Log::info('Order cancelled', [
                'order_number' => $order->order_number,
                'cancelled_at' => now(),
                'previous_status' => $order->getOriginal('status'),
                'previous_payment_status' => $order->getOriginal('payment_status')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan.',
                'order_number' => $order->order_number
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling order: ' . $e->getMessage(), [
                'order_number' => $order_number,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman sukses pesanan.
     */
    public function showOrderSuccess($order_number)
    {
        $order = Order::where('order_number', $order_number)
                      ->with(['outlet', 'table', 'orderItems.product', 'promotion'])
                      ->firstOrFail();

        $statuses = Order::getStatuses();
        $paymentStatuses = Order::getPaymentStatuses();

        return view('order.success', compact('order', 'statuses', 'paymentStatuses'));
    }

    /**
     * Proses pembayaran dengan Midtrans.
     */
    public function payWithMidtrans(Request $request, Order $order)
    {
        // Implementasi Midtrans payment gateway
        // Untuk demo, kita langsung update status
        $order->update([
            'payment_status' => 'paid',
            'status' => 'preparing', // Update ke status baru
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diproses'
        ]);
    }

    /**
     * Menampilkan QRIS Midtrans.
     */
    public function showMidtransQris(Order $order)
    {
        return view('order.payment_midtrans_qris', compact('order'));
    }

    /**
     * Handle Midtrans notification callback.
     */
    public function handleMidtransNotification(Request $request)
    {
        try {
            $json = $request->getContent();
            $notification = json_decode($json, true);

            Log::info('Midtrans notification received', $notification);

            // Verify signature key
            $signatureKey = $request->header('X-Signature-Key');
            $expectedSignature = hash('sha512', $json . env('MIDTRANS_SERVER_KEY'));

            if ($signatureKey !== $expectedSignature) {
                Log::error('Invalid Midtrans signature', [
                    'received' => $signatureKey,
                    'expected' => $expectedSignature
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            // Extract order information
            $orderId = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;

            if (!$orderId) {
                Log::error('Midtrans notification missing order_id', $notification);
                return response()->json(['error' => 'Missing order_id'], 400);
            }

            // Find order by payment gateway reference
            $payment = Payment::where('payment_gateway_ref', $orderId)->first();

            if (!$payment) {
                Log::error('Payment not found for Midtrans order_id', ['order_id' => $orderId]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $order = $payment->order;

            // Update payment status based on notification
            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    if ($fraudStatus === 'challenge') {
                        $payment->update(['status' => 'challenge']);
                        $order->update(['payment_status' => 'pending']);
                    } elseif ($fraudStatus === 'accept') {
                        $payment->update([
                            'status' => 'completed',
                            'paid_at' => now()
                        ]);
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'preparing'
                        ]);

                        // Update table status
                        if ($order->table) {
                            $order->table->update(['status' => 'occupied']);
                        }
                    }
                    break;

                case 'pending':
                    $payment->update(['status' => 'pending']);
                    $order->update(['payment_status' => 'pending']);
                    break;

                case 'deny':
                    $payment->update(['status' => 'failed']);
                    $order->update(['payment_status' => 'failed']);
                    break;

                case 'expire':
                    $payment->update(['status' => 'expired']);
                    $order->update(['payment_status' => 'failed']);
                    break;

                case 'cancel':
                    $payment->update(['status' => 'cancelled']);
                    $order->update([
                        'payment_status' => 'cancelled',
                        'status' => 'cancelled'
                    ]);

                    // Update table status
                    if ($order->table) {
                        $order->table->update(['status' => 'available']);
                    }
                    break;

                default:
                    Log::warning('Unknown Midtrans transaction status', [
                        'order_id' => $orderId,
                        'status' => $transactionStatus
                    ]);
                    break;
            }

            Log::info('Midtrans notification processed successfully', [
                'order_id' => $orderId,
                'order_number' => $order->order_number,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            return response()->json(['status' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Error processing Midtrans notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
