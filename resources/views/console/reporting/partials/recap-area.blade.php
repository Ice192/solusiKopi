<div id="recap-area-content">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <b>Ringkasan Penjualan ({{ $dateFrom->format('d M Y') }} - {{ $dateTo->format('d M Y') }})</b>
                </div>
                <div class="card-body">
                    <ul class="mb-3">
                        <li><b>Total Penjualan:</b> Rp {{ number_format($revenueStats['total_revenue'] ?? 0, 0, ',', '.') }}</li>
                        <li><b>Total Transaksi:</b> {{ number_format($revenueStats['total_orders'] ?? 0) }}</li>
                    </ul>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header"><b>Rekap Penjualan per Minggu</b></div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Minggu</th>
                                                    <th class="text-end">Omzet</th>
                                                    <th class="text-end">Transaksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($weeklyRecap ?? [] as $week)
                                                    <tr>
                                                        <td>{{ $week['label'] }}</td>
                                                        <td class="text-end">Rp {{ number_format($week['omzet'], 0, ',', '.') }}</td>
                                                        <td class="text-end">{{ number_format($week['orders']) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header"><b>Rekap Penjualan per Bulan</b></div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Bulan</th>
                                                    <th class="text-end">Omzet</th>
                                                    <th class="text-end">Transaksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($monthlyRecap ?? [] as $month)
                                                    <tr>
                                                        <td>{{ $month['label'] }}</td>
                                                        <td class="text-end">Rp {{ number_format($month['omzet'], 0, ',', '.') }}</td>
                                                        <td class="text-end">{{ number_format($month['orders']) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header"><b>Rincian Penjualan Makanan & Minuman</b></div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th class="text-end">Qty Terjual</th>
                                                    <th class="text-end">Total Omzet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($foodDrinkRecap ?? [] as $row)
                                                    <tr>
                                                        <td>{{ $row['category'] }}</td>
                                                        <td class="text-end">{{ number_format($row['qty']) }}</td>
                                                        <td class="text-end">Rp {{ number_format($row['omzet'], 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <b>Rekap {{ $recapTabs[$recapMode] }} ({{ $dateFrom->format('d M Y') }} - {{ $dateTo->format('d M Y') }})</b>
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
                                @foreach($recapData ?? [] as $recap)
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
</div>
