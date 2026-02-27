@extends('layouts.app')

@section('title', 'Reporting Dashboard')

@section('content')
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
<div class="row mb-2">
    <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-success" id="btnExportSummary">
            <i class="ri-download-line me-1"></i> Export Ringkasan
        </button>
    </div>
</div>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Reporting Dashboard</h4>
                    <div>
                        <a href="{{ route('console.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i>Order Management
                        </a>
                        <button class="btn btn-outline-success btn-sm" id="btnExportReport">
                            <i class="ri-download-line me-1"></i>Export Report
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="GET" class="row g-3" id="filterForm">
                                        <div class="col-md-3">
                                            <label class="form-label">Date From</label>
                                            <input type="date" name="date_from" class="form-control"
                                                   value="{{ $dateFrom->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Date To</label>
                                            <input type="date" name="date_to" class="form-control"
                                                   value="{{ $dateTo->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Outlet</label>
                                            <select name="outlet_id" class="form-select">
                                                <option value="">All Outlets</option>
                                                @foreach(\App\Models\Outlet::all() as $outlet)
                                                <option value="{{ $outlet->id }}" {{ $outletId == $outlet->id ? 'selected' : '' }}>
                                                    {{ $outlet->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary d-block w-100">
                                                <i class="ri-search-line me-1"></i>Apply Filter
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Skeleton Loader -->
                    <div id="dashboard-skeleton" style="display:none;">
                        <div class="skeleton skeleton-title mb-3" style="height:32px;width:30%;"></div>
                        <div class="skeleton skeleton-table mb-3" style="height:180px;width:100%;"></div>
                        <div class="skeleton skeleton-table mb-3" style="height:180px;width:100%;"></div>
                        <div class="skeleton skeleton-table mb-3" style="height:180px;width:100%;"></div>
                    </div>

                    <!-- AJAX Data Area -->
                    <div id="dashboard-data-area">
                        @include('console.reporting.partials.recap-area', [
                            'revenueStats' => $revenueStats,
                            'weeklyRecap' => $weeklyRecap,
                            'monthlyRecap' => $monthlyRecap,
                            'foodDrinkRecap' => $foodDrinkRecap,
                            'recapTabs' => $recapTabs,
                            'recapMode' => $recapMode,
                            'recapData' => $recapData,
                            'dateFrom' => $dateFrom,
                            'dateTo' => $dateTo,
                            'dates' => $dates,
                            'outletId' => $outletId,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
        outlet_id: $("select[name='outlet_id']").val(),
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
    $(document).on('submit', '#filterForm', function(e) {
        e.preventDefault();
        fetchDashboardData();
    });
    $(document).on('click', '.nav-link', function(e) {
        if ($(this).hasClass('active')) return;
        e.preventDefault();
        history.replaceState(null, '', $(this).attr('href'));
        fetchDashboardData();
    });
    // Export Ringkasan langsung dari filter aktif
    $('#btnExportSummary').on('click', function() {
        const params = {
            date_from: $("input[name='date_from']").val(),
            date_to: $("input[name='date_to']").val(),
            outlet_id: $("select[name='outlet_id']").val(),
            type: 'all',
        };
        const url = `/console/reporting/export-summary?${$.param(params)}`;
        window.open(url, '_blank');
        toastr.success('Export dimulai, file akan segera diunduh.');
    });
    // Export Report langsung dari filter aktif
    $('#btnExportReport').on('click', function() {
        const params = {
            date_from: $("input[name='date_from']").val(),
            date_to: $("input[name='date_to']").val(),
            outlet_id: $("select[name='outlet_id']").val(),
            report_type: 'orders',
        };
        const url = `/console/reporting/export?${$.param(params)}`;
        window.open(url, '_blank');
        toastr.success('Export dimulai, file akan segera diunduh.');
    });
});
</script>
@endpush
