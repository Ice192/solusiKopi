@extends('layouts.app')

@section('title', 'Order Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Order Management</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('console.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-refresh-line me-1"></i>Reset Filters
                        </a>
                        <a href="{{ route('console.reporting.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ri-bar-chart-line me-1"></i>Reporting
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-4 col-6 mb-3">
                            <div class="card bg-primary text-white h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Orders</h6>
                                            <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ri-shopping-cart-line ri-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-6 mb-3">
                            <div class="card bg-warning text-white h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Pending</h6>
                                            <h3 class="mb-0">{{ number_format($stats['pending_orders']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ri-time-line ri-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-6 mb-3">
                            <div class="card bg-info text-white h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Preparing</h6>
                                            <h3 class="mb-0">{{ number_format($stats['preparing_orders']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ri-settings-3-line ri-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-6 mb-3">
                            <div class="card bg-secondary text-white h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Ready</h6>
                                            <h3 class="mb-0">{{ number_format($stats['ready_orders']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ri-check-double-line ri-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-6 mb-3">
                            <div class="card bg-success text-white h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Served</h6>
                                            <h3 class="mb-0">{{ number_format($stats['served_orders']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ri-customer-service-line ri-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-6 mb-3">
                            <div class="card bg-dark text-white h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Completed</h6>
                                            <h3 class="mb-0">{{ number_format($stats['completed_orders']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ri-check-line ri-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Status -->
                    <div class="row mb-4">
                        <div class="col-lg-6 col-md-6 col-12 mb-3">
                            <div class="card bg-success text-white h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Available Tables</h6>
                                            <h3 class="mb-0">{{ number_format($stats['available_tables']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ri-table-line ri-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 mb-3">
                            <div class="card bg-warning text-white h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Occupied Tables</h6>
                                            <h3 class="mb-0">{{ number_format($stats['occupied_tables']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ri-user-line ri-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="GET" class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label">Search Order</label>
                                            <input type="text" name="search" class="form-control"
                                                   value="{{ request('search') }}" placeholder="Order number...">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="">All Status</option>
                                                @foreach($statuses as $value => $label)
                                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Payment Status</label>
                                            <select name="payment_status" class="form-select">
                                                <option value="">All Payment</option>
                                                @foreach($paymentStatuses as $value => $label)
                                                <option value="{{ $value }}" {{ request('payment_status') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Outlet</label>
                                            <select name="outlet_id" class="form-select">
                                                <option value="">All Outlets</option>
                                                @foreach($outlets as $outlet)
                                                <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                                                    {{ $outlet->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Date From</label>
                                            <input type="date" name="date_from" class="form-control"
                                                   value="{{ request('date_from') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Date To</label>
                                            <input type="date" name="date_to" class="form-control"
                                                   value="{{ request('date_to') }}">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-search-line me-1"></i>Filter
                                            </button>
                                            {{-- <a href="{{ route('console.orders.export') }}?{{ http_build_query(request()->all()) }}"
                                               class="btn btn-success">
                                                <i class="ri-download-line me-1"></i>Export
                                            </a> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Outlet</th>
                                    <th>Table</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('console.orders.show', $order) }}" class="fw-bold">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($order->user)
                                            {{ $order->user->name }}
                                        @else
                                            <span class="text-muted">{{ $order->guest_info['name'] ?? 'Guest' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->outlet->name }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $order->table->status === 'available' ? 'success' : 'warning' }}">
                                            {{ $order->table->table_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{
                                            $order->status === 'completed' ? 'success' :
                                            ($order->status === 'cancelled' ? 'danger' :
                                            ($order->status === 'served' ? 'info' :
                                            ($order->status === 'ready' ? 'secondary' :
                                            ($order->status === 'preparing' ? 'warning' : 'primary'))))
                                        }}">
                                            {{ $statuses[$order->status] ?? ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                            {{ $paymentStatuses[$order->payment_status] ?? ucfirst($order->payment_status) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ ucfirst($order->payment_method) }}</small>
                                    </td>
                                    <td class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>{{ $order->ordered_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('console.orders.show', $order) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                    <i class="ri-settings-3-line"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($order->status === 'pending')
                                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('{{ $order->id }}', 'preparing')">
                                                        <i class="ri-settings-3-line me-1"></i>Start Preparing
                                                    </a></li>
                                                    @endif
                                                    @if($order->status === 'preparing')
                                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('{{ $order->id }}', 'ready')">
                                                        <i class="ri-check-double-line me-1"></i>Ready to Serve
                                                    </a></li>
                                                    @endif
                                                    @if($order->status === 'ready')
                                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('{{ $order->id }}', 'served')">
                                                        <i class="ri-customer-service-line me-1"></i>Mark as Served
                                                    </a></li>
                                                    @endif
                                                    @if($order->status === 'served')
                                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('{{ $order->id }}', 'completed')">
                                                        <i class="ri-check-line me-1"></i>Complete Order
                                                    </a></li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="updateOrderStatus('{{ $order->id }}', 'cancelled')">
                                                        <i class="ri-close-line me-1"></i>Cancel Order
                                                    </a></li>
                                                </ul>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="ri-shopping-cart-line ri-3x text-muted"></i>
                                        <p class="text-muted mt-2">Tidak ada order yang ditemukan</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateOrderStatus(orderId, status) {
    event.preventDefault();
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin mengubah status pesanan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/console/orders/${orderId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil', 'Status pesanan berhasil diubah.', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', 'Gagal mengubah status pesanan.', 'error');
                }
            })
            .catch(() => Swal.fire('Gagal', 'Terjadi kesalahan saat mengubah status.', 'error'));
        }
    });
}
</script>
@endpush
