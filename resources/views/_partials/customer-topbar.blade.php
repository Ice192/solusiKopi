@php
    $tableCode = session('current_table_code');
    $activeTab = request('tab');
    $menuCartParams = ['tab' => 'cart'];
    $menuCheckoutParams = ['tab' => 'checkout'];
    if (!empty($tableCode)) {
        $menuCartParams['table_code'] = $tableCode;
        $menuCheckoutParams['table_code'] = $tableCode;
    }
@endphp

<div class="customer-topbar-wrap mb-4">
    <div class="customer-topbar d-flex align-items-center justify-content-between flex-wrap gap-2">
        <a href="{{ route('dashboard') }}" class="customer-brand d-flex align-items-center gap-2 text-decoration-none">
            <span class="customer-brand-icon">
                <i class="ri-cup-line"></i>
            </span>
            <span class="customer-brand-text">{{ config('app.name', 'Solusi Kopi') }}</span>
        </a>

        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
            <a href="{{ route('dashboard') }}"
                class="btn btn-sm customer-nav-btn {{ request()->routeIs('dashboard') || request()->routeIs('home') || request()->routeIs('welcome') || request()->routeIs('welcome.*') ? 'active' : '' }}">
                <i class="ri-home-5-line me-1"></i>Beranda
            </a>
            <a href="{{ route('order.menu', $menuCartParams) }}"
                class="btn btn-sm customer-nav-btn {{ request()->routeIs('order.menu') && $activeTab === 'cart' ? 'active' : '' }}">
                <i class="ri-shopping-cart-2-line me-1"></i>Keranjang
            </a>
            <a href="{{ route('order.menu', $menuCheckoutParams) }}"
                class="btn btn-sm customer-nav-btn {{ request()->routeIs('order.menu') && $activeTab === 'checkout' ? 'active' : '' }}">
                <i class="ri-secure-payment-line me-1"></i>Pembayaran
            </a>
            <a href="{{ route('order.history') }}"
                class="btn btn-sm customer-nav-btn {{ request()->routeIs('order.history') || request()->routeIs('order.detail') ? 'active' : '' }}">
                <i class="ri-history-line me-1"></i>Riwayat
            </a>

            @auth
                <div class="dropdown">
                    <button class="btn customer-avatar-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-user-3-line"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li class="px-3 py-2 border-bottom">
                            <small class="text-muted d-block text-uppercase">Email</small>
                            <span class="fw-semibold d-block">{{ auth()->user()->email }}</span>
                        </li>
                        <li class="p-2">
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="logoutCustomer()">
                                <i class="ri-logout-box-r-line me-1"></i>Logout
                            </button>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn customer-avatar-btn" aria-label="Login">
                    <i class="ri-user-3-line"></i>
                </a>
            @endauth
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .customer-topbar-wrap {
                position: sticky;
                top: 0.75rem;
                z-index: 1050;
            }

            .customer-topbar {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(15, 111, 138, 0.15);
                border-radius: 14px;
                padding: 0.65rem 0.9rem;
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
            }

            .customer-brand-icon {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #e7f5f9;
                color: #0f6f8a;
                font-size: 1rem;
            }

            .customer-brand-text {
                color: #0f172a;
                font-weight: 700;
                letter-spacing: -0.2px;
            }

            .customer-nav-btn {
                border: 1px solid #d9e2ec;
                color: #0f172a;
                background: #fff;
                border-radius: 10px;
                transition: all 0.2s ease;
            }

            .customer-nav-btn:hover,
            .customer-nav-btn.active {
                color: #0f6f8a;
                border-color: #b8d9e3;
                background: #eef8fb;
            }

            .customer-avatar-btn {
                width: 38px;
                height: 38px;
                border-radius: 50%;
                border: 1px solid #b8d9e3;
                background: #eef8fb;
                color: #0f6f8a;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .customer-avatar-btn:hover {
                background: #e0f2f7;
                color: #0b5a70;
            }

            #bottom-navigation {
                display: none !important;
            }

            @media (max-width: 768px) {
                .customer-topbar {
                    padding: 0.55rem 0.65rem;
                }

                .customer-nav-btn {
                    font-size: 0.74rem;
                    padding: 0.33rem 0.52rem;
                }

                .layout-wrapper {
                    padding-bottom: 0 !important;
                }

                .container-xxl,
                .container-fluid {
                    padding-bottom: 1rem !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function logoutCustomer() {
                fetch('{{ route('logout') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                }).then(() => {
                    window.location.href = '{{ route('dashboard') }}';
                }).catch(() => {
                    window.location.href = '{{ route('dashboard') }}';
                });
            }
        </script>
    @endpush
@endonce
