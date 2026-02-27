<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Payment;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Console\{
    UserController,
    OutletController,
    TableController,
    CategoryController,
    ProductController,
    PromotionController,
    PaymentController,
    OrderManagementController,
    ReportingController
};
use App\Http\Controllers\{
    DashboardController,
    ProfileController,
    OrderController,
    OrderHistoryController,
    WelcomeController
};

// Landing
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');
Route::get('/welcome', function () {
    return redirect()->route('dashboard');
})->name('welcome');
Route::get('/dashboard', [DashboardController::class, '__invoke'])->name('dashboard');
Route::post('/dashboard/search-table', [WelcomeController::class, 'searchTable'])->name('welcome.search-table');
Route::get('/dashboard/select-table/{table_code}', [WelcomeController::class, 'selectTable'])->name('welcome.select-table');

// ==================================================
// 🛡️ Protected Routes (auth + verified)
// ==================================================
Route::middleware(['auth'])->group(function () {

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Order history (admin/kasir/cashier only)
    Route::middleware('role:admin|kasir|cashier')->prefix('order-history')->name('order.history.')->group(function () {
        Route::get('/', [OrderHistoryController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderHistoryController::class, 'show'])->name('show');
    });

    // Admin Console
    Route::prefix('console')->middleware('role:admin')->group(function () {
        Route::prefix('user-management')->group(function () {
            Route::resource('users', UserController::class);
        });

        Route::resources([
            'outlets'     => OutletController::class,
            'tables'      => TableController::class,
            'categories'  => CategoryController::class,
            'products'    => ProductController::class,
            'promotions'  => PromotionController::class,
        ]);
    });

    // Operasional Kasir (Admin, Kasir & Cashier)
    Route::prefix('console')->middleware('role:admin|kasir|cashier')->group(function () {
        Route::prefix('payments')->name('console.payments.')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::post('/{order}/pay', [PaymentController::class, 'pay'])->name('pay');
        });
    });

    // Order Management (Admin only)
    Route::prefix('console')->middleware('role:admin')->group(function () {
        Route::prefix('orders')->name('console.orders.')->group(function () {
            Route::get('/', [OrderManagementController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderManagementController::class, 'show'])->name('show');
            Route::patch('/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('update-status');
            Route::patch('/{order}/payment', [OrderManagementController::class, 'updatePaymentStatus'])->name('update-payment');
            Route::get('/stats', [OrderManagementController::class, 'getStats'])->name('stats');
            Route::get('/export', [OrderManagementController::class, 'export'])->name('export');
        });
    });

    // Reporting (Admin, Kasir & Cashier)
    Route::prefix('console')->middleware('role:admin|kasir|cashier')->group(function () {
        Route::prefix('reporting')->name('console.reporting.')->group(function () {
            Route::get('/', [ReportingController::class, 'index'])->name('index');
            Route::get('/export', [ReportingController::class, 'export'])->name('export');
            Route::get('/stats/realtime', [ReportingController::class, 'getRealTimeStats'])->name('realtime-stats');
        });
    });
});

// ==================================================
// 🔐 Auth & Social Login
// ==================================================
require __DIR__ . '/auth.php';

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('google', [SocialLoginController::class, 'redirectToGoogle'])->name('google');
    Route::get('google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

    Route::get('facebook', [SocialLoginController::class, 'redirectToFacebook'])->name('facebook');
    Route::get('facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);
});

// ==================================================
// 🧾 Guest Order Routes
// ==================================================
Route::prefix('dashboard/order')->name('order.')->group(function () {
    Route::get('/history', [OrderController::class, 'showOrderHistory'])->name('history');
    Route::get('/menu', [OrderController::class, 'showMenuByTableCode'])->name('menu');
    Route::get('/{table_code}', [OrderController::class, 'showMenuByTableCode'])->name('menu.with-table');
    Route::get('/history/{order_number}', [OrderController::class, 'showOrderDetail'])->name('detail');
    Route::get('/payment/qris/{order_number}', [OrderController::class, 'showPaymentQris'])->name('payment.qris');
    Route::post('/payment/confirm/{order_number}', [OrderController::class, 'confirmPayment'])->name('payment.confirm');
    Route::get('/success/{order_number}', [OrderController::class, 'showOrderSuccess'])->name('success');
    Route::get('/payment/status/{order_number}', [OrderController::class, 'getPaymentStatus'])->name('payment.status');
    Route::post('/payment/midtrans/{order}', [OrderController::class, 'payWithMidtrans'])->name('order.payment.midtrans');
    Route::get('/payment/midtrans/{order}/qris', [OrderController::class, 'showMidtransQris'])->name('order.payment.midtrans.qris');
    Route::post('/cancel/{order_number}', [OrderController::class, 'cancelOrder'])->name('cancel');
});

// Route clear session guest-order flow
Route::post('/clear-session', function () {
    session()->flush();
    return redirect()->route('dashboard');
})->name('clear.session');

// ==================================================
// ✅ Simulasi QRIS POS (Dev/Test Only?)
// ==================================================
Route::prefix('dashboard/order')->group(function () {
    Route::get('/payment/{order}', function (Order $order) {
        if ($order->payment_status === 'paid') {
            return redirect()->route('order.success.sim', $order)->with('info', 'Pesanan sudah dibayar.');
        }
        return view('order.payment_qris', compact('order'));
    })->name('order.payment.sim');

    Route::post('/payment/{order}/confirm', function (Order $order) {
        $order->update(['payment_status' => 'paid', 'status' => 'preparing']);

        Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'method' => $order->payment_method,
            'status' => 'completed',
            'payment_gateway_ref' => 'QRIS_SIM_' . Str::upper(Str::random(8)),
            'paid_at' => now(),
        ]);

        return redirect()->route('order.success.sim', $order)->with('success', 'Pembayaran berhasil dikonfirmasi!');
    })->name('order.payment.confirm.sim');

    // success fallback route (already defined above as named route, but this is backup)
    Route::get('/success/{order}', [OrderController::class, 'orderSuccess'])->name('order.success.sim');
});

Route::get('/console/reporting/export-summary', [\App\Http\Controllers\Console\ReportingController::class, 'exportSummary'])
    ->middleware(['auth', 'role:admin|kasir|cashier'])
    ->name('console.reporting.exportSummary');
