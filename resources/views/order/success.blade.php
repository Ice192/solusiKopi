@extends('layouts.guest-order')

@section('title', $title ?? 'Pesanan Berhasil!')

@section('content')

    <div class="card p-4 p-md-5 mb-4">
        <div class="card-body">
            <div class="app-brand justify-content-center mb-5">
                <a href="{{ route('dashboard') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        <!-- SVG Logo here -->
                        <i class="ri-cup-line text-primary fs-2"></i>
                    </span>
                    <span class="app-brand-text demo text-body fw-semibold">{{ config('app.name') }}</span>
                </a>
            </div>

            {{-- Notifikasi (akan ditangani via Toastr JS) --}}
            <div id="payment-status-message" class="alert d-none" role="alert"></div>

            @if($order->status === 'cancelled')
                <h4 class="mb-2 text-center text-danger">Pesanan Dibatalkan</h4>
                <p class="mb-4 text-center">Nomor Pesanan: <span class="fw-bold">{{ $order->order_number }}</span></p>
                <div class="alert alert-danger text-center mb-4">
                    <i class="ri-close-circle-line me-2"></i>
                    <strong>Pesanan Dibatalkan</strong><br>
                    Pesanan ini telah dibatalkan dan tidak dapat diproses kembali.
                </div>
            @else
                <h4 class="mb-2 text-center">Pesanan Berhasil Dibuat!</h4>
                <p class="mb-4 text-center">Nomor Pesanan Anda: <span class="fw-bold">{{ $order->order_number }}</span></p>
            @endif

            <ul class="list-group mb-4">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Outlet</span>
                    <span>{{ $order->outlet->name }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Meja</span>
                    <span>{{ $order->table->table_number }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Status Pesanan</span>
                    <span class="badge bg-label-{{
                        $order->status === 'completed' ? 'success' :
                        ($order->status === 'cancelled' ? 'danger' :
                        ($order->status === 'served' ? 'info' :
                        ($order->status === 'ready' ? 'secondary' :
                        ($order->status === 'preparing' ? 'warning' : 'primary'))))
                    }}">
                        {{ $statuses[$order->status] ?? ucfirst($order->status) }}
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Status Pembayaran</span>
                    <span class="badge bg-label-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                        {{ $paymentStatuses[$order->payment_status] ?? ucfirst($order->payment_status) }}
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Metode Pembayaran</span>
                    <span>{{ Str::ucfirst($order->payment_method) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Total Belanja</span>
                    <span class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </li>
            </ul>

            @if($order->status !== 'cancelled')
            <h5 class="mb-3">Detail Item Pesanan:</h5>
            <ul class="list-group mb-4">
                @foreach ($order->orderItems as $item)
                    <li class="list-group-item d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{{ $item->product->name }}</h6>
                            <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->price_at_order, 0, ',', '.') }}</small>
                        </div>
                        <span class="fw-bold">Rp {{ number_format($item->quantity * $item->price_at_order, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            @endif

            <div class="d-grid">
                @if($order->status !== 'cancelled')
                    {{-- <button onclick="window.print()" class="btn btn-primary mb-3">
                        <i class="ri-printer-line me-2"></i>Print Struk
                    </button> --}}
                    <a href="{{ route('order.history') }}" class="btn btn-outline-primary mb-3">Lihat Riwayat Pesanan</a>
                @else
                    <a href="{{ route('order.history') }}" class="btn btn-outline-secondary mb-3">Kembali ke Riwayat Pesanan</a>
                @endif
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    {{-- Print Styles --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-section, .print-section * {
                visibility: visible;
            }
            .print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    {{-- Print Section --}}
    @if($order->status !== 'cancelled')
    <div class="print-section d-none">
        <div class="text-center p-4">
            <h4>{{ config('app.name') }}</h4>
            <p class="mb-1">{{ $order->outlet->name }}</p>
            <p class="mb-3">Meja: {{ $order->table->table_number }}</p>
            <hr>
            <p class="mb-1">Order #: {{ $order->order_number }}</p>
            <p class="mb-1">Tanggal: {{ $order->ordered_at->format('d/m/Y H:i') }}</p>
            <p class="mb-1">Status Pesanan: {{ Str::ucfirst($order->status) }}</p>
            <p class="mb-3">Status Pembayaran: {{ Str::ucfirst($order->payment_status) }}</p>
            <hr>
            @if($order->status !== 'cancelled')
            @foreach ($order->orderItems as $item)
                <div class="d-flex justify-content-between mb-1">
                    <span>{{ $item->product->name }} ({{ $item->quantity }}x)</span>
                    <span>Rp {{ number_format($item->quantity * $item->price_at_order, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <hr>
            @endif
            @if($order->status !== 'cancelled')
            <div class="d-flex justify-content-between mb-1">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Pajak:</span>
                <span>Rp {{ number_format($order->other_fee, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Biaya Layanan:</span>
                <span>Rp {{ number_format($order->additional_fee, 0, ',', '.') }}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-bold">
                <span>Total:</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <hr>
            <p class="mb-1">Metode: {{ Str::ucfirst($order->payment_method) }}</p>
            @if($order->note)
                <p class="mb-1">Catatan: {{ $order->note }}</p>
            @endif
            <hr>
            @if($order->status === 'cancelled')
                <p class="mb-0 text-danger fw-bold">PESANAN DIBATALKAN</p>
            @else
                <p class="mb-0">Terima kasih atas pesanan Anda!</p>
            @endif
        </div>
    </div>
    @endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Check for session messages (e.g., from OrderController redirect)
        const successMessage = '{{ session('success') }}';
        const errorMessage = '{{ session('error') }}';
        const infoMessage = '{{ session('info') }}';
        const warningMessage = '{{ session('warning') }}';

        if (successMessage) {
            toastr.success(successMessage);
        }
        if (errorMessage) {
            toastr.error(errorMessage);
        }
        if (infoMessage) {
            toastr.info(infoMessage);
        }
        if (warningMessage) {
            toastr.warning(warningMessage);
        }
    });
</script>
@endpush
@endsection

