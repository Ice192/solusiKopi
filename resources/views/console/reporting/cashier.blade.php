@extends('layouts.app')

@section('title', 'Kasir - Laporan Harian')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #1f2937, #111827); color: #fff;">
            <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-white">Laporan Harian Kasir</h4>
                    <p class="mb-0 text-white-50">Pantau transaksi harian dan siapkan laporan untuk diserahkan.</p>
                </div>
                <div class="align-self-lg-center">
                    <span class="badge bg-label-light text-dark px-3 py-2">
                        <i class="ri-calendar-line me-1"></i>{{ $date->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Laporan</label>
                        <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Outlet</label>
                        <select name="outlet_id" class="form-select">
                            <option value="">Semua Outlet</option>
                            @foreach (\App\Models\Outlet::all() as $outlet)
                                <option value="{{ $outlet->id }}" {{ (string) $outletId === (string) $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line me-1"></i>Tampilkan
                        </button>
                        <a href="{{ route('console.reporting.index') }}" class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Total Omzet</small>
                        <h5 class="mb-0">Rp {{ number_format($revenueStats['total_revenue'] ?? 0, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Total Transaksi</small>
                        <h5 class="mb-0">{{ number_format($revenueStats['total_orders'] ?? 0) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Tunai Lunas</small>
                        <h5 class="mb-0">{{ number_format($cashPaid) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">QRIS Lunas</small>
                        <h5 class="mb-0">{{ number_format($qrisPaid) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body d-flex flex-wrap gap-2">
                <a href="{{ route('console.reporting.export', ['date_from' => $date->format('Y-m-d'), 'date_to' => $date->format('Y-m-d'), 'outlet_id' => $outletId, 'report_type' => 'orders']) }}"
                    class="btn btn-success">
                    <i class="ri-download-line me-1"></i>Unduh Laporan Harian
                </a>
                <a href="{{ route('console.reporting.exportSummary', ['date_from' => $date->format('Y-m-d'), 'date_to' => $date->format('Y-m-d'), 'outlet_id' => $outletId, 'type' => 'all']) }}"
                    class="btn btn-outline-primary">
                    <i class="ri-file-download-line me-1"></i>Unduh Ringkasan Harian
                </a>
                <span class="badge bg-label-warning align-self-center">
                    Pending Pembayaran: {{ number_format($pendingPayments) }}
                </span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Transaksi Harian</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Outlet</th>
                                <th>Meja</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Status Pembayaran</th>
                                <th>Waktu Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                @php
                                    $badge = match ($order->payment_status) {
                                        'paid' => 'success',
                                        'failed', 'cancelled' => 'danger',
                                        default => 'warning',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->outlet?->name ?? '-' }}</td>
                                    <td>{{ $order->table?->table_number ?? '-' }}</td>
                                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>{{ strtoupper($order->payment_method) }}</td>
                                    <td><span class="badge bg-label-{{ $badge }}">{{ strtoupper($order->payment_status) }}</span></td>
                                    <td>{{ $order->ordered_at?->format('d-m-Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi pada tanggal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
