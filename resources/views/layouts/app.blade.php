@php
    $user = Auth::user();
@endphp
<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="{{ asset('/materialize') }}/assets/"
    data-template="vertical-menu-template-no-customizer" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>â˜• @yield('title') | {{ config('app.name') }}</title>

    <meta name="description"
        content="
    System Management Inventory is a web-based application that is used to manage the inventory of goods in a company. This application is built using the Laravel 8 framework and the {{ config('app.name') }} CSS framework. This application is equipped with features that can help companies manage their goods inventory. This application is also equipped with a role-based access control system that can help companies manage their users. This application is also equipped with a feature to manage the purchase and sale of goods. This application is also equipped with a feature to manage the goods that have been entered into the warehouse. This application is also equipped with a feature to manage the goods that have been sold. This application is also equipped with a feature to manage the goods that have been purchased
    " />
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet"
        href="{{ asset('/materialize') }}/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('/materialize') }}/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('/materialize') }}/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('/materialize') }}/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/libs/@form-validation/form-validation.css" />
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/libs/toastr/toastr.css" />
    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- Page CSS -->

    <link rel="stylesheet" href="{{ asset('/materialize') }}/assets/vendor/css/pages/app-logistics-dashboard.css" />

    <!-- Helpers -->
    <script src="{{ asset('/materialize') }}/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('/materialize') }}/assets/js/config.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/jquery/jquery.js"></script>

    @livewireStyles
    @stack('styles')
    <style>
        :root {
            --brand-surface: #f4f7fb;
            --brand-accent: #0f6f8a;
            --brand-accent-soft: #d9eef4;
        }

        body {
            background: radial-gradient(circle at 4% 8%, rgba(181, 225, 238, 0.35), transparent 30%),
                radial-gradient(circle at 92% 2%, rgba(255, 225, 199, 0.3), transparent 30%),
                var(--brand-surface);
        }

        .layout-page {
            background: transparent;
        }

        .bg-navbar-theme {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid rgba(15, 111, 138, 0.08);
        }

        .layout-menu {
            border-right: 1px solid rgba(15, 111, 138, 0.08);
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .btn-primary {
            background-color: var(--brand-accent);
            border-color: var(--brand-accent);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #0c5b72;
            border-color: #0c5b72;
        }

        .page-title {
            color: #0f172a;
            font-weight: 700;
            letter-spacing: -0.2px;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ url('/dashboard') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <i class="ri-cup-line text-primary fs-2"></i>
                        </span>
                        <span class="app-brand-text demo menu-text fw-semibold ms-2">{{ config('app.name') }}</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z"
                                fill-opacity="0.9" />
                            <path
                                d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z"
                                fill-opacity="0.4" />
                        </svg>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                @include('_partials.sidebar_menus')
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="ri-menu-fill ri-22px"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

                        <ul class="navbar-nav flex-row align-items-center ms-auto">

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="{{ $user->avatarUrl }}" alt
                                            class="rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ $user->avatarUrl }}"
                                                            alt class="rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span
                                                        class="fw-medium d-block small">{{ $user ? $user->name : 'John Doe' }}</span>
                                                        <small class="text-muted">
                                                            {{ strtoupper(optional($user->roles->first())->name ?? 'user') }}
                                                        </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="post" class="d-none"
                                            id="form-logout">
                                            @csrf
                                        </form>
                                        <div class="d-grid px-4 pt-2 pb-1">
                                            <a class="btn btn-sm btn-danger d-flex" href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); $('#form-logout').submit();">
                                                <small class="align-middle">Logout</small>
                                                <i class="ri-logout-box-r-line ms-2 ri-16px"></i>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    @yield('content')

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="text-body mb-2 mb-md-0">
                                    © Copyright
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                    
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/hammer/hammer.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/i18n/i18n.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="{{ asset('/materialize') }}/assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script>
        var urlRoot = '{{ url('/') }}';
        var urlSearch = urlRoot + "/api/search?user_id=" + '{{ Auth::id() }}';
    </script>
    @vite(['resources/js/main.js', 'resources/js/app-logistics-dashboard.js'])

    <!-- Page JS -->

    <script src="{{ asset('/materialize') }}/assets/vendor/libs/toastr/toastr.js"></script>
    <script>
        @if (session('success'))
            toastr.success('{{ session('success') }}', 'Success!')
        @elseif (session('error'))
            toastr.error('{{ session('error') }}', 'Error!')
        @elseif (session('info'))
            toastr.info('{{ session('info') }}', 'Info!')
        @elseif (session('status'))
            toastr.info('{{ session('status') }}', 'Info!')
        @elseif (session('warning'))
            toastr.warning('{{ session('warning') }}', 'Warning!') @endif
    </script>

@livewireScripts
    @stack('scripts')
</body>

</html>

