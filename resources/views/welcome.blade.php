<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-content-navbar" dir="ltr"
    data-theme="theme-default" data-assets-path="{{ asset('/materialize') }}/assets/"
    data-template="vertical-menu-template-no-customizer" data-style="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>☕ {{ config('app.name', 'Solusi Kopi') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('/materialize') }}/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/fonts/remixicon/remixicon.css" />
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/fonts/flag-icons.css" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/libs/node-waves/node-waves.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet"
        href="{{ asset('/materialize') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/libs/toastr/toastr.css" />

    @stack('styles')
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    @include('_partials.customer-topbar')

                    <div class="text-center mb-5">
                        <div class="app-brand justify-content-center mb-5">
                            <a href="{{ route('dashboard') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <i class="ri-cup-line text-primary fs-2"></i>
                                </span>
                                <span class="app-brand-text demo text-body fw-semibold">{{ config('app.name') }}</span>
                            </a>
                        </div>
                        <h2 class="mb-2">Selamat Datang di {{ config('app.name') }}</h2>
                        <p class="text-muted">Silakan masukkan kode meja Anda untuk memulai pemesanan</p>
                    </div>

                    <!-- Search Table Form -->
                    <div class="row justify-content-center">
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            {{ session('error') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif

                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <form action="{{ route('welcome.search-table') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="table_code" class="form-label">Kode Meja</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="ri-table-line"></i></span>
                                                <input type="text"
                                                    class="form-control @error('table_code') is-invalid @enderror"
                                                    id="table_code" name="table_code" placeholder="Contoh: 57"
                                                    value="{{ old('table_code') }}" maxlength="10" required>
                                            </div>
                                            @error('table_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Masukkan kode meja yang tertera di meja
                                                Anda</small>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg welcome-action-btn">
                                                <i class="ri-search-line me-2"></i>Cari Meja
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Quick Access -->
                            {{-- <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Akses Cepat</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Social Login Options -->
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <a href="{{ route('auth.google') }}"
                                                class="btn btn-outline-danger w-100">
                                                <i class="ri-google-fill me-2"></i>
                                                Google
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('auth.facebook') }}"
                                                class="btn btn-outline-primary w-100">
                                                <i class="ri-facebook-fill me-2"></i>
                                                Facebook
                                            </a>
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            Login untuk menyimpan riwayat pesanan dan akses fitur lainnya
                                        </small>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/hammer/hammer.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/toastr/toastr.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('/materialize') }}/assets/js/main.js"></script>

    <script>
        // Toastr Options
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function logout() {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("logout") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    }).then(() => {
                        window.location.href = '{{ route("dashboard") }}';
                    }).catch(() => {
                        window.location.href = '{{ route("dashboard") }}';
                    });
                }
            });
        }

        function clearSession() {
            Swal.fire({
                title: 'Konfirmasi Hapus Sesi',
                text: 'Apakah Anda yakin ingin menghapus semua sesi?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("clear.session") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    }).then(() => {
                        window.location.href = '{{ route("dashboard") }}';
                    }).catch(() => {
                        window.location.href = '{{ route("dashboard") }}';
                    });
                }
            });
        }
    </script>

    <!-- CSS untuk padding bottom -->
    <style>
        .welcome-action-btn {
            background: linear-gradient(135deg, #0f6f8a 0%, #0a4f63 100%);
            border-color: #0a4f63;
            color: #fff;
            transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease;
        }

        .welcome-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 111, 138, 0.28);
            filter: brightness(1.05);
            color: #fff;
        }

        @media (max-width: 768px) {
            .container-xxl {
                padding-bottom: 1.25rem !important;
            }
        }
    </style>
</body>

</html>
