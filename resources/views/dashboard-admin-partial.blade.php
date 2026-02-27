<div id="dashboard-admin-content">
    <div class="row mb-4">
        <div class="col-md-4 col-6 mb-3">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <div class="mb-2"><i class="ri-money-dollar-circle-line ri-2x"></i></div>
                    <h6 class="mb-1">Omzet (Periode Dipilih)</h6>
                    <h4 class="mb-0">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6 mb-3">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <div class="mb-2"><i class="ri-bar-chart-line ri-2x"></i></div>
                    <h6 class="mb-1">Pesanan (Periode Dipilih)</h6>
                    <h4 class="mb-0">{{ $summary['total_orders'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6 mb-3">
            <div class="card text-center bg-info text-white">
                <div class="card-body">
                    <div class="mb-2"><i class="ri-user-add-line ri-2x"></i></div>
                    <h6 class="mb-1">Pesanan Lunas</h6>
                    <h4 class="mb-0">{{ $summary['paid_orders'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6 mb-3">
            <div class="card text-center bg-warning text-white">
                <div class="card-body">
                    <div class="mb-2"><i class="ri-time-line ri-2x"></i></div>
                    <h6 class="mb-1">Pesanan Pending</h6>
                    <h4 class="mb-0">{{ $summary['pending_orders'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6 mb-3">
            <div class="card text-center bg-outline-primary text-dark">
                <div class="card-body">
                    <div class="mb-2"><i class="ri-bar-chart-line ri-2x"></i></div>
                    <h6 class="mb-1">Rata-rata Order</h6>
                    <h4 class="mb-0">Rp {{ number_format($summary['average_order_value'] ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Grafik Omzet & Pesanan 7 Hari Terakhir -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><b>Grafik Omzet</b></div>
                <div class="card-body">
                    <canvas id="omzetChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><b>Grafik Pesanan</b></div>
                <div class="card-body">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Top Produk Terlaris -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><b>Top Produk Terlaris</b></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-end">{{ $product->total_quantity ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><b>Performa Outlet</b></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Outlet</th>
                                <th class="text-end">Total Orders</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Avg Order Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outletPerformance as $outlet)
                                <tr>
                                    <td>{{ $outlet->name }}</td>
                                    <td class="text-end">{{ number_format($outlet->orders_count) }}</td>
                                    <td class="text-end">Rp {{ number_format($outlet->orders_sum_total_amount ?? 0) }}</td>
                                    <td class="text-end">
                                        @if($outlet->orders_count > 0)
                                            Rp {{ number_format(($outlet->orders_sum_total_amount ?? 0) / $outlet->orders_count) }}
                                        @else
                                            Rp 0
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Rekap KPI & Breakdown per Periode (Tab Recap) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <b>Rekap {{ $recapMode }} ({{ $dateFrom->format('d M Y') }} - {{ $dateTo->format('d M Y') }})</b>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Periode</th>
                                    <th class="text-end">Omzet</th>
                                    <th class="text-end">Pesanan</th>
                                    <th>Breakdown Produk per Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recapData as $recap)
                                    <tr>
                                        <td>{{ $recap['label'] }}</td>
                                        <td class="text-end">Rp {{ number_format($recap['omzet'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($recap['orders']) }}</td>
                                        <td>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Kategori</th>
                                                            <th class="text-end">Qty</th>
                                                            <th class="text-end">Omzet</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($recap['produk'] as $row)
                                                            <tr>
                                                                <td>{{ $row->category_name }}</td>
                                                                <td class="text-end">{{ number_format($row->total_quantity) }}</td>
                                                                <td class="text-end">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center">-</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Render Chart.js setelah AJAX load
    setTimeout(function() {
        if (window.omzetChartInstance) window.omzetChartInstance.destroy();
        if (window.ordersChartInstance) window.ordersChartInstance.destroy();
        const omzetCtx = document.getElementById('omzetChart').getContext('2d');
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        window.omzetChartInstance = new Chart(omzetCtx, {
            type: 'line',
            data: {
                labels: @json($dates),
                datasets: [{
                    label: 'Omzet',
                    data: @json($omzet7days),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40,167,69,0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
        window.ordersChartInstance = new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: @json($dates),
                datasets: [{
                    label: 'Pesanan',
                    data: @json($orders7days),
                    backgroundColor: '#17a2b8',
                    borderRadius: 6
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    }, 300);
    </script>
</div>
