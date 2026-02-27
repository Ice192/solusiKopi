@extends('layouts.app')

@section('title', 'Tambah Meja')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Konsol / Manajemen Meja /</span> Tambah</h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Form Tambah Meja</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tables.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="outlet_id" class="form-label">Outlet</label>
                        <select class="form-select @error('outlet_id') is-invalid @enderror" id="outlet_id"
                            name="outlet_id" required>
                            <option value="">Pilih Outlet</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}"
                                    {{ old('outlet_id', $outlets->first()?->id) == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('outlet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- <div class="mb-3 d-none">
                        <label for="name" class="form-label">Nama Meja</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror " id="name"
                            name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 d-none">
                        <label for="capacity" class="form-label">Kapasitas</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                            name="capacity" value="{{ old('capacity') }}" required min="1">
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}
                    <div class="mb-3">
                        <label for="code" class="form-label">Kode Meja</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                            name="code" value="{{ old('code') }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Tersedia
                            </option>
                            <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Terisi
                            </option>
                            <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Tidak Tersedia
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <a href="{{ route('tables.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
