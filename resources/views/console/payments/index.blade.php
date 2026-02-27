@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Pembayaran Kasir</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('console.payments.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="table_number">Nomor/Kode Meja</label>
                        <input type="text" name="table_number" id="table_number" class="form-control"
                            placeholder="Contoh: 01 atau TB-01" value="{{ request('table_number') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line me-1"></i>Cari Order
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
            <div class="alert alert-warning">
                Meja tidak ditemukan.
            </div>
        @endif

        @if ($table)
            <div class="card mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="mb-1">Meja {{ $table->table_number }} ({{ $table->table_code }})</h6>
                        <small class="text-muted">Status meja saat ini: {{ strtoupper($table->status) }}</small>
                    </div>
                    <span class="badge bg-label-primary">Total Order Aktif: {{ $orders->count() }}</span>
                </div>
            </div>
        @endif

        @forelse ($orders as $order)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="mb-1">{{ $order->order_number }}</h6>
                        <small class="text-muted">{{ $order->ordered_at?->format('d-m-Y H:i') }}</small>
                    </div>
                    <span class="badge bg-label-warning">{{ strtoupper($order->payment_status) }}</span>
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

                    <div class="d-flex justify-content-end mb-3">
                        <h5 class="mb-0">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h5>
                    </div>

                    <form method="POST" action="{{ route('console.payments.pay', $order) }}"
                        class="d-flex justify-content-end gap-2 flex-wrap">
                        @csrf
                        <select name="payment_method" class="form-select" style="max-width: 160px;">
                            <option value="cash">Cash</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-secure-payment-line me-1"></i>Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        @empty
            @if ($table)
                <div class="alert alert-info">
                    Tidak ada order aktif untuk meja ini.
                </div>
            @endif
        @endforelse
    </div>
@endsection
