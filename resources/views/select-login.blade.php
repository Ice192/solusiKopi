<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-content-navbar" dir="ltr"
    data-theme="theme-default" data-assets-path="{{ asset('/materialize') }}/assets/"
    data-template="vertical-menu-template-no-customizer" data-style="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>â˜• {{ config('app.name', 'Solusi Kopi') }} - Pilih Login</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('/materialize') }}/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />

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
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
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

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <i class="ri-table-line text-primary" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="mb-2">Meja {{ $table->table_number }}</h5>
                                        <p class="text-muted mb-0">{{ $table->outlet->name }}</p>
                                        <small class="text-muted">{{ $table->outlet->address }}</small>
                                    </div>

                                    <hr class="my-4">

                                    @guest
                                        <!-- Guest Option -->
                                        <div class="mb-4">
                                            <a href="{{ route('order.menu', ['table_code' => $table->table_code ?? $table->table_number]) }}"
                                               class="btn btn-primary btn-lg w-100 mb-3">
                                                <i class="ri-user-line me-2"></i>
                                                Lanjutkan sebagai Tamu
                                            </a>
                                            <small class="text-muted d-block text-center">
                                                Anda dapat memesan tanpa login. Data pesanan akan disimpan sementara.
                                            </small>
                                        </div>
                                    @endguest

                                    @auth
                                        <div class="mb-4">
                                            <a href="{{ route('order.menu', ['table_code' => $table->table_code ?? $table->table_number]) }}"
                                               class="btn btn-primary btn-lg w-100 mb-3">
                                                <i class="ri-user-line me-2"></i>
                                                Lanjutkan ke Menu
                                            </a>
                                            <small class="text-muted d-block text-center">
                                                Selamat Belanja {{ auth()->user()->name }}.
                                            </small>
                                        </div>
                                    @endauth

                                    {{-- <div class="text-center mb-3">
                                        <span class="text-muted">atau</span>
                                    </div>

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
                                    </div> --}}
                                </div>
                            </div>

                            <!-- Back Button -->
                            <div class="text-center mt-4">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="ri-arrow-left-line me-2"></i>
                                    Kembali ke Beranda
                                </a>
                            </div>
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
        @media (max-width: 768px) {
            .container-xxl {
                padding-bottom: 1.25rem !important;
            }
        }
    </style>
</body>
</html>



