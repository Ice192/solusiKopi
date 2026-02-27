@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4 dashboard-hero">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 page-title">Dashboard Operasional</h4>
                    <p class="mb-0 text-muted">Pantau performa outlet, pesanan, dan penjualan dari satu layar.</p>
                </div>
                <span class="badge bg-label-info px-3 py-2">
                    <i class="ri-time-line me-1"></i>
                    {{ now()->translatedFormat('d M Y') }}
                </span>
            </div>
        </div>

        @php
            $recapMode = request('recap_mode', 'daily');
            $recapTabs = [
                'daily' => 'Harian',
                'weekly' => 'Mingguan',
                'monthly' => 'Bulanan',
            ];
            $query = request()->all();
        @endphp
        <div class="row mb-3">
            <div class="col-12">
                <ul class="nav nav-pills mb-2 gap-2" id="recapTab" role="tablist">
                    @foreach($recapTabs as $mode => $label)
                        @php $query['recap_mode'] = $mode; @endphp
                        <li class="nav-item" role="presentation">
                            <a class="nav-link @if($recapMode === $mode) active @endif" href="?{{ http_build_query($query) }}">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end" id="dashboardFilterForm">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Awal</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-success ms-auto" id="btnExportDashboard">
                            <i class="ri-download-line me-1"></i> Export Ringkasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @role('admin')
            <!-- Skeleton Loader -->
            <div id="dashboard-skeleton" style="display:none;">
                <div class="skeleton skeleton-title mb-3" style="height:32px;width:30%;"></div>
                <div class="skeleton skeleton-table mb-3" style="height:180px;width:100%;"></div>
                <div class="skeleton skeleton-table mb-3" style="height:180px;width:100%;"></div>
                <div class="skeleton skeleton-table mb-3" style="height:180px;width:100%;"></div>
            </div>
            <!-- AJAX Data Area -->
            <div id="dashboard-data-area">
                @include('dashboard-admin-partial', [
                    'summary' => $summary ?? [],
                    'topProducts' => $topProducts ?? [],
                    'outletPerformance' => $outletPerformance ?? [],
                    'recapData' => $recapData ?? [],
                    'periods' => $periods ?? [],
                    'dates' => $dates ?? [],
                    'omzet7days' => $omzet7days ?? [],
                    'orders7days' => $orders7days ?? [],
                    'dateFrom' => $dateFrom ?? null,
                    'dateTo' => $dateTo ?? null,
                    'recapMode' => $recapMode ?? 'daily',
                ])
            </div>
        @endrole
        {{-- Role kasir/user bisa dioptimalkan berikutnya --}}
    </div>
@endsection

@push('styles')
<style>
.dashboard-hero {
    background: linear-gradient(135deg, rgba(15, 111, 138, 0.96) 0%, rgba(9, 54, 69, 0.96) 100%);
    color: #fff;
}

.dashboard-hero .page-title,
.dashboard-hero .text-muted {
    color: #fff !important;
}

.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
    background-size: 400% 100%;
    animation: skeleton-loading 1.4s ease infinite;
    border-radius: 6px;
}
@keyframes skeleton-loading {
    0% { background-position: 100% 50%; }
    100% { background-position: 0 50%; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function showSkeleton() {
    $('#dashboard-data-area').hide();
    $('#dashboard-skeleton').show();
}
function hideSkeleton() {
    $('#dashboard-skeleton').hide();
    $('#dashboard-data-area').show();
}
function fetchDashboardData() {
    showSkeleton();
    const params = {
        date_from: $("input[name='date_from']").val(),
        date_to: $("input[name='date_to']").val(),
        recap_mode: $(".nav-link.active").attr('href').split('recap_mode=')[1] || 'daily',
    };
    $.get(window.location.pathname, params, function(res) {
        const html = $(res).find('#dashboard-data-area').html();
        $('#dashboard-data-area').html(html);
        hideSkeleton();
    }).fail(function(xhr) {
        hideSkeleton();
        toastr.error('Gagal memuat data dashboard!');
    });
}
$(document).ready(function() {
    $(document).on('submit', '#dashboardFilterForm', function(e) {
        e.preventDefault();
        fetchDashboardData();
    });
    $(document).on('click', '#recapTab .nav-link', function(e) {
        if ($(this).hasClass('active')) return;
        e.preventDefault();
        history.replaceState(null, '', $(this).attr('href'));
        fetchDashboardData();
    });
    // Export Ringkasan langsung dari filter aktif
    $('#btnExportDashboard').on('click', function() {
        const params = {
            date_from: $("input[name='date_from']").val(),
            date_to: $("input[name='date_to']").val(),
            type: 'all',
        };
        const url = `/console/reporting/export-summary?${$.param(params)}`;
        window.open(url, '_blank');
        toastr.success('Export dimulai, file akan segera diunduh.');
    });
});
</script>
@endpush
