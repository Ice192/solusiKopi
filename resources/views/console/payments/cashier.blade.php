@extends('layouts.app')

@section('title', 'Kasir - Pembayaran & Laporan Harian')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #14532d, #166534); color: #fff;">
            <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-white">Pusat Kerja Kasir</h4>
                    <p class="mb-0 text-white-50">
                        Fokus kasir: konfirmasi pembayaran tunai, monitor informasi pesanan QRIS, dan laporan harian.
                    </p>
                </div>
                <div class="align-self-lg-center">
                    <span class="badge bg-label-light text-dark px-3 py-2">
                        <i class="ri-calendar-line me-1"></i>{{ $dailyReport['date']->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Order Hari Ini</small>
                        <h4 class="mb-0">{{ number_format($dailyReport['total_orders']) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Omzet Hari Ini</small>
                        <h4 class="mb-0">Rp {{ number_format($dailyReport['total_revenue'], 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Tunai Pending</small>
                        <h4 class="mb-0">{{ number_format($dailyReport['pending_cash_orders']) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">QRIS Lunas Hari Ini</small>
                        <h4 class="mb-0">{{ number_format($dailyReport['qris_paid_orders']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body d-flex flex-wrap gap-2">
                <a href="{{ route('console.reporting.index', ['date' => now()->toDateString()]) }}"
                    class="btn btn-primary">
                    <i class="ri-file-chart-line me-1"></i>Lihat Laporan Harian
                </a>
                <a href="{{ route('console.reporting.export', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString(), 'report_type' => 'orders']) }}"
                    class="btn btn-outline-success">
                    <i class="ri-download-line me-1"></i>Export Laporan Harian
                </a>
                <a href="{{ route('console.reporting.exportSummary', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString(), 'type' => 'all']) }}"
                    class="btn btn-outline-secondary">
                    <i class="ri-download-cloud-line me-1"></i>Export Ringkasan Harian
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Konfirmasi Pembayaran Tunai</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('console.payments.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="table_number">Filter Nomor/Kode Meja</label>
                        <input type="text" name="table_number" id="table_number" class="form-control"
                            placeholder="Contoh: 01 atau TB-01" value="{{ request('table_number') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line me-1"></i>Cari
                        </button>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <a href="{{ route('console.payments.index') }}" class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if (request('table_number') && !$table)
            <div class="alert alert-warning mb-4">Meja tidak ditemukan.</div>
        @endif

        @forelse ($cashOrders as $order)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="mb-1">{{ $order->order_number }}</h6>
                        <small class="text-muted">
                            Meja {{ $order->table?->table_number ?? '-' }} | {{ $order->ordered_at?->format('d-m-Y H:i') }}
                        </small>
                    </div>
                    <span class="badge bg-label-warning">TUNAI - MENUNGGU KONFIRMASI</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->product?->name ?? 'Produk' }}</td>
                                        <td class="text-end">Rp {{ number_format($item->price_at_order, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($item->price_at_order * $item->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h5>
                        <form method="POST" action="{{ route('console.payments.pay', $order) }}">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="ri-check-line me-1"></i>Konfirmasi Tunai
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info mb-4">
                Tidak ada order tunai yang menunggu konfirmasi.
            </div>
        @endforelse

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Informasi Pesanan QRIS Hari Ini</h5>
                <span class="badge bg-label-primary">Total: {{ $qrisOrders->count() }} order</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Meja</th>
                                <th>Total</th>
                                <th>Status Order</th>
                                <th>Status Bayar</th>
                                <th>Ref Gateway</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($qrisOrders as $order)
                                @php
                                    $latestPayment = $order->payments->first();
                                    $paymentBadge = match ($order->payment_status) {
                                        'paid' => 'success',
                                        'failed', 'cancelled' => 'danger',
                                        default => 'warning',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->table?->table_number ?? '-' }}</td>
                                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($order->status) }}</td>
                                    <td><span class="badge bg-label-{{ $paymentBadge }}">{{ strtoupper($order->payment_status) }}</span></td>
                                    <td>{{ $latestPayment?->payment_gateway_ref ?? '-' }}</td>
                                    <td>{{ $order->ordered_at?->format('d-m-Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi QRIS hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
