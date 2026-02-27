@extends('layouts.app')

@section('title', 'Edit Promosi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Konsol / Manajemen Promosi /</span> Edit</h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Form Edit Promosi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('promotions.update', $promotion->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="code" class="form-label">Kode Promo</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                            name="code" value="{{ old('code', $promotion->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Promosi</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $promotion->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="3">{{ old('description', $promotion->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="discount_type" class="form-label">Tipe Diskon</label>
                        <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type"
                            name="discount_type" required>
                            <option value="">Pilih Tipe Diskon</option>
                            <option value="percentage"
                                {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>
                                Persentase</option>
                            <option value="fixed"
                                {{ old('discount_type', $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>Nominal
                                Tetap</option>
                        </select>
                        @error('discount_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="discount_value" class="form-label">Nilai Diskon</label>
                        <input type="number" class="form-control @error('discount_value') is-invalid @enderror"
                            id="discount_value" name="discount_value"
                            value="{{ old('discount_value', $promotion->discount_value) }}" required min="0">
                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror flatpickr-datetime"
                            id="start_date" name="start_date"
                            value="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d')) }}"
                            required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Berakhir</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror flatpickr-datetime"
                            id="end_date" name="end_date"
                            value="{{ old('end_date', \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d')) }}"
                            required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="active"
                                {{ old('status', $promotion->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive"
                                {{ old('status', $promotion->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <a href="{{ route('promotions.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.flatpickr-datetime').flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                locale: "id" // If you have id locale for flatpickr
            });
        });
    </script>
@endpush
