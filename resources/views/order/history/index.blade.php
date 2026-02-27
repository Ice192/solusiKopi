<x-order-layout>
    <x-slot name="title">
        Riwayat Pesanan
    </x-slot>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Riwayat Pesanan Anda</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($orders->isEmpty())
                            <p class="text-center text-muted">Belum ada riwayat pesanan.</p>
                            <div class="d-grid">
                                <a href="{{ route('order.menu', ['table_code' => session('current_table_code', '57')]) }}" class="btn btn-primary mt-3">Mulai Pesan Sekarang</a>
                            </div>
                        @else
                            <div class="list-group">
                                @foreach ($orders as $order)
                                    <a href="{{ route('order.detail', $order->order_number) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">#{{ $order->order_number }} - {{ $order->outlet->name }} (Meja {{ $order->table->table_number }})</h6>
                                            <small class="text-muted">Tanggal: {{ $order->ordered_at->format('d M Y H:i') }}</small><br>
                                            <small class="text-muted">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</small>
                                            @if($order->status === 'cancelled')
                                                <br><small class="text-danger"><i class="ri-close-circle-line me-1"></i>Dibatalkan</small>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <span class="badge mb-1 bg-label-{{
                                                $order->status === 'completed' ? 'success' :
                                                ($order->status === 'cancelled' ? 'danger' :
                                                ($order->status === 'served' ? 'info' :
                                                ($order->status === 'ready' ? 'secondary' :
                                                ($order->status === 'preparing' ? 'warning' : 'primary'))))
                                            }}">
                                                {{ $statuses[$order->status] ?? ucfirst($order->status) }}
                                            </span>
                                            <br>
                                            <small class="badge bg-label-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                {{ $paymentStatuses[$order->payment_status] ?? ucfirst($order->payment_status) }}
                                            </small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-order-layout>
