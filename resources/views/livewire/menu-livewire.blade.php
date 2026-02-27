<div>
    @if ($ready)
        @if ($activeTab === 'menu')
            <div class="d-md-none text-center mb-4">
                <h4 class="mb-0">Meja {{ $table->table_number }} ({{ $outlet->name }})</h4>
            </div>
        @endif

        {{-- Toast Notifications --}}
        <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
            <!-- Toast notifications will be inserted here -->
        </div>

        {{-- Konten Utama (Tab) --}}
        <div class="tab-content mb-5 pb-5">
            {{-- Tab Menu --}}
            <div class="tab-pane fade @if($activeTab == 'menu') show active @endif" id="menu" role="tabpanel">

                {{-- Filter Kategori --}}
                @if(count($categories) > 1)
                    <div class="mb-4 overflow-auto filter-scroll category-filter-wrap" style="white-space:nowrap;">
                        <button type="button"
                            wire:click.prevent.stop="filterByCategory('all')"
                            wire:key="category-all"
                            wire:loading.class="is-loading"
                            class="btn btn-sm me-2 mb-2 rounded-pill category-filter-btn @if($selectedCategory === 'all') is-active @endif"
                            aria-pressed="{{ $selectedCategory === 'all' ? 'true' : 'false' }}">
                            <span class="category-filter-icon"><i class="ri-apps-line"></i></span>
                            <span>Semua</span>
                            <div wire:loading wire:target="selectedCategory" class="spinner-border spinner-border-sm ms-1" style="width:12px;height:12px;"></div>
                        </button>
                        @foreach($categories as $cat)
                            <button type="button"
                                wire:click.prevent.stop="filterByCategory('{{ addslashes($cat) }}')"
                                wire:key="category-{{ md5($cat) }}"
                                wire:loading.class="is-loading"
                                class="btn btn-sm me-2 mb-2 rounded-pill category-filter-btn @if($selectedCategory === $cat) is-active @endif"
                                aria-pressed="{{ $selectedCategory === $cat ? 'true' : 'false' }}">
                                <span>{{ $cat }}</span>
                                <div wire:loading wire:target="selectedCategory" class="spinner-border spinner-border-sm ms-1" style="width:12px;height:12px;"></div>
                            </button>
                        @endforeach
                    </div>

                    {{-- Status Filter Aktif --}}
                    @if($selectedCategory !== 'all' || $showNewProducts || !empty($searchTerm))
                        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="ri-filter-3-line me-2"></i>
                                <div class="flex-grow-1">
                                    <strong>Filter Aktif:</strong>
                                    @if($selectedCategory !== 'all')
                                        <span class="badge bg-primary me-1">{{ $selectedCategory }}</span>
                                    @endif
                                    @if($showNewProducts)
                                        <span class="badge bg-success me-1">Produk Baru</span>
                                    @endif
                                    @if(!empty($searchTerm))
                                        <span class="badge bg-info me-1">Pencarian: "{{ $searchTerm }}"</span>
                                    @endif
                                </div>
                                <button type="button" wire:click="resetFilters"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-75"
                                    class="btn btn-sm btn-outline-info">
                                    <span wire:loading.remove wire:target="resetFilters">
                                        <i class="ri-refresh-line me-1"></i>Reset
                                    </span>
                                    <span wire:loading wire:target="resetFilters">
                                        <div class="spinner-border spinner-border-sm me-1" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Resetting...
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif
                @endif
                {{-- Grid Produk berdasarkan Kategori --}}
                <div wire:loading.class="opacity-50" wire:target="selectedCategory,searchTerm,sortBy,sortOrder,showNewProducts">
                    <div wire:loading wire:target="selectedCategory,searchTerm,sortBy,sortOrder,showNewProducts" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat produk...</p>

                        {{-- Skeleton Loading --}}
                        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 mt-3 mb-5">
                            @for($i = 0; $i < 8; $i++)
                                <div class="col">
                                    <div class="card h-100 border-0 rounded-3" style="min-height:200px;">
                                        <div class="bg-light rounded-3" style="aspect-ratio:1/1;min-height:100px;max-height:120px;">
                                            <div class="placeholder-glow">
                                                <div class="placeholder col-12 h-100"></div>
                                            </div>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="placeholder-glow">
                                                <div class="placeholder col-8 mb-1"></div>
                                                <div class="placeholder col-6 mb-1"></div>
                                                <div class="placeholder col-10"></div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent border-0 pt-0 pb-2 px-2">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div class="placeholder-glow">
                                                    <div class="placeholder col-2 me-1"></div>
                                                    <div class="placeholder col-1 mx-1"></div>
                                                    <div class="placeholder col-2 ms-1"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div wire:loading.remove wire:target="selectedCategory,searchTerm,sortBy,sortOrder,showNewProducts">
                        @if(count($productsGrouped) > 0)
                            {{-- Info Jumlah Produk --}}
                            <div class="alert alert-light border mb-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class="ri-information-line me-2"></i>
                                        <strong>{{ collect($productsGrouped)->sum(fn($products) => count($products)) }}</strong> produk ditemukan
                                        @if($selectedCategory !== 'all')
                                            dalam kategori <strong>{{ $selectedCategory }}</strong>
                                        @endif
                                        @if(!empty($searchTerm))
                                            untuk pencarian <strong>"{{ $searchTerm }}"</strong>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        Urut berdasarkan: <strong>{{ ucfirst($sortBy) }}</strong>
                                        ({{ $sortOrder === 'asc' ? 'A-Z' : 'Z-A' }})
                                    </small>
                                </div>
                            </div>

                            @foreach ($productsGrouped as $category => $products)
                                @if($selectedCategory === 'all' || $selectedCategory === $category)
                                    <div class="mb-4 category-section">
                                        <div class="d-flex align-items-center mb-3">
                                            <h6 class="mb-0 fw-bold text-primary">
                                                <i class="ri-price-tag-3-line me-2"></i>{{ $category }}
                                            </h6>
                                            <div class="ms-auto">
                                                <small class="text-muted">{{ count($products) }} produk</small>
                                            </div>
                                        </div>
                                        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2">
                                            @foreach ($products as $product)
                                                <div class="col">
                                                    <div class="card h-100 shadow-sm border-0 rounded-3 product-card animate__animated animate__fadeInUp position-relative"
                                                         style="min-height:200px;"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"
                                                         title="{{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}">
                                                        <div class="position-relative">
                                                            @if($product->image_url)
                                                                <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" class="card-img-top rounded-3" style="aspect-ratio:1/1;object-fit:cover;min-height:100px;max-height:120px;">
                                                            @else
                                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="aspect-ratio:1/1;min-height:100px;max-height:120px;">
                                                                    <i class="ri-image-line text-muted" style="font-size:1.5rem;"></i>
                                                                </div>
                                                            @endif
                                                            @if (($cart[$product->id]['quantity'] ?? 0) > 0)
                                                                <span class="badge bg-success position-absolute top-0 end-0 m-1 animate__animated animate__bounceIn" style="font-size:0.75em;z-index:2;">{{ $cart[$product->id]['quantity'] }}</span>
                                                            @endif
                                                            @if($product->created_at->diffInDays(now()) <= 7)
                                                                <span class="badge bg-warning position-absolute top-0 start-0 m-1 animate__animated animate__pulse" style="font-size:0.7em;z-index:2;">BARU</span>
                                                            @endif
                                                        </div>
                                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                                            <div>
                                                                <div class="fw-semibold small text-truncate mb-1" title="{{ $product->name }}" style="font-size:0.85rem;">{{ $product->name }}</div>
                                                                <div class="text-success fw-bold mb-1" style="font-size:0.9rem">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                                                <div class="small text-muted" style="font-size:0.7rem;min-height:16px;">{{ \Illuminate\Support\Str::limit($product->description, 25) }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer bg-transparent border-0 pt-0 pb-2 px-2 d-flex justify-content-center align-items-center">
                                                            <button type="button" wire:click.prevent.stop="removeFromCartById({{ $product->id }})"
                                                                wire:loading.class="opacity-50"
                                                                class="btn btn-light border btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center product-btn-minus me-1"
                                                                style="width:24px;height:24px;@if(!($cart[$product->id]['quantity'] ?? 0))opacity:0.5;pointer-events:none;@endif"
                                                                @if(!($cart[$product->id]['quantity'] ?? 0)) disabled @endif>
                                                                <i class="ri-subtract-line text-danger" style="font-size:0.9rem;"></i>
                                                                <div wire:loading wire:target="removeFromCartById({{ $product->id }})" class="spinner-border spinner-border-sm position-absolute" style="width:12px;height:12px;"></div>
                                                            </button>
                                                            <span class="mx-1 small" style="font-size:0.8rem;">{{ $cart[$product->id]['quantity'] ?? 0 }}</span>
                                                            <button type="button" wire:click.prevent.stop="addToCartById({{ $product->id }})"
                                                                wire:loading.class="opacity-50"
                                                                class="btn btn-success btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center shadow product-btn-plus ms-1"
                                                                style="width:24px;height:24px;">
                                                                <i class="ri-add-line" style="font-size:0.9rem;"></i>
                                                                <div wire:loading wire:target="addToCartById({{ $product->id }})" class="spinner-border spinner-border-sm position-absolute" style="width:12px;height:12px;"></div>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="alert alert-warning text-center">
                                <i class="ri-search-line me-2"></i>
                                Tidak ada produk yang sesuai dengan filter yang dipilih.
                                <br>
                                <small class="text-muted">Coba ubah filter atau kata kunci pencarian Anda.</small>
                            </div>
                        @endif
                    </div>
                </div>

                @if (!empty($cart))
                    <div class="menu-sticky-cart-summary-wrap">
                        <div class="menu-sticky-cart-summary-btn">
                            <button type="button"
                                wire:click.prevent.stop="setActiveTab('cart')"
                                class="menu-sticky-cart-summary-link">
                                <span class="menu-sticky-cart-summary-title">
                                    <i class="ri-file-list-3-line me-2"></i>Lihat Rincian Belanja
                                </span>
                                <span class="menu-sticky-cart-summary-meta">
                                    {{ collect($cart)->sum(fn($item) => (int) ($item['quantity'] ?? 0)) }} item
                                    | Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </span>
                            </button>
                            <button type="button"
                                wire:click.prevent.stop="setActiveTab('checkout')"
                                class="menu-sticky-cart-checkout-link">
                                <i class="ri-arrow-right-up-line me-1"></i>Bayar
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tab Keranjang --}}
            <div class="tab-pane fade @if($activeTab == 'cart') show active @endif" id="cart" role="tabpanel">
                <div class="card mt-4 mb-4">
                    <h5 class="card-header">Keranjang Anda</h5>
                    <div class="card-body">
                        @if (empty($cart))
                            <div class="cart-empty-state">
                                <div class="cart-empty-card">
                                    <span class="cart-empty-highlight"></span>
                                    <div class="cart-empty-icon-wrap">
                                        <i class="ri-shopping-bag-3-line"></i>
                                    </div>
                                    <h5 class="cart-empty-title mb-2">Keranjang Masih Kosong</h5>
                                    <p class="cart-empty-subtitle mb-4">
                                        Belum ada pesanan yang ditambahkan. Pilih menu favorit Anda untuk mulai checkout.
                                    </p>

                                    <div class="cart-empty-pills mb-4">
                                        <span class="cart-empty-pill"><i class="ri-cup-line me-1"></i>Fresh Brew</span>
                                        <span class="cart-empty-pill"><i class="ri-fire-line me-1"></i>Best Seller</span>
                                        <span class="cart-empty-pill"><i class="ri-timer-line me-1"></i>Fast Service</span>
                                    </div>

                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <button type="button"
                                            wire:click.prevent.stop="setActiveTab('menu')"
                                            class="btn btn-primary px-4">
                                            <i class="ri-restaurant-line me-1"></i>Jelajahi Menu
                                        </button>
                                        <a href="{{ route('order.history') }}" class="btn btn-outline-secondary px-4">
                                            <i class="ri-history-line me-1"></i>Lihat Riwayat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <ul class="list-group list-group-flush mb-3">
                                @foreach ($cart as $item)
                                    @if(isset($item['product_id']) && isset($item['name']) && isset($item['price']) && isset($item['quantity']))
                                        <li class="list-group-item py-3"
                                            wire:key="cart-item-{{ $item['product_id'] }}">
                                            {{-- Desktop Layout --}}
                                            <div class="d-none d-md-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1 me-3">
                                                    <h6 class="mb-1">{{ $item['name'] }}</h6>
                                                    <small class="text-muted">Rp {{ number_format($item['price'], 0, ',', '.') }}</small>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <button type="button" wire:click.prevent.stop="removeFromCartById({{ $item['product_id'] }})"
                                                        wire:loading.class="opacity-50"
                                                        class="btn btn-sm btn-outline-danger me-2" style="width:32px;height:32px;padding:0;">
                                                        <i class="ri-subtract-line"></i>
                                                        <div wire:loading wire:target="removeFromCartById({{ $item['product_id'] }})" class="spinner-border spinner-border-sm position-absolute" style="width:12px;height:12px;"></div>
                                                    </button>
                                                    <input type="number"
                                                        wire:model.debounce.500ms="cart.{{ $item['product_id'] }}.quantity"
                                                        wire:change="updateQuantity({{ $item['product_id'] }}, $event.target.value)"
                                                        wire:loading.attr="disabled"
                                                        class="form-control form-control-sm text-center"
                                                        style="width:90px;height:32px;"
                                                        min="0"
                                                        max="99"
                                                        value="{{ $item['quantity'] }}">
                                                    <div wire:loading wire:target="updateQuantity" class="spinner-border spinner-border-sm position-absolute" style="width:12px;height:12px;"></div>
                                                    <button type="button" wire:click.prevent.stop="addToCartById({{ $item['product_id'] }})"
                                                        wire:loading.class="opacity-50"
                                                        class="btn btn-sm btn-outline-success ms-2" style="width:32px;height:32px;padding:0;">
                                                        <i class="ri-add-line"></i>
                                                        <div wire:loading wire:target="addToCartById({{ $item['product_id'] }})" class="spinner-border spinner-border-sm position-absolute" style="width:12px;height:12px;"></div>
                                                    </button>
                                                </div>
                                                <div class="text-end ms-3" style="min-width:80px;">
                                                    <span class="fw-bold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                                </div>
                                            </div>

                                            {{-- Mobile Layout --}}
                                            <div class="d-md-none">
                                                <div class="row align-items-center">
                                                    <div class="col-8">
                                                        <h6 class="mb-1 text-truncate">{{ $item['name'] }}</h6>
                                                        <small class="text-muted">Rp {{ number_format($item['price'], 0, ',', '.') }}</small>
                                                        <div class="mt-2">
                                                            <span class="fw-bold text-success">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex align-items-center justify-content-end">
                                                            <button type="button" wire:click.prevent.stop="removeFromCartById({{ $item['product_id'] }})"
                                                                wire:loading.class="opacity-50"
                                                                class="btn btn-sm btn-outline-danger me-1" style="width:28px;height:28px;padding:0;font-size:0.7rem;">
                                                                <i class="ri-subtract-line"></i>
                                                                <div wire:loading wire:target="removeFromCartById({{ $item['product_id'] }})" class="spinner-border spinner-border-sm position-absolute" style="width:10px;height:10px;"></div>
                                                            </button>
                                                            <input type="number"
                                                                wire:model.debounce.500ms="cart.{{ $item['product_id'] }}.quantity"
                                                                wire:change="updateQuantity({{ $item['product_id'] }}, $event.target.value)"
                                                                wire:loading.attr="disabled"
                                                                class="form-control form-control-sm text-center"
                                                                style="width:50px;height:28px;font-size:0.8rem;"
                                                                min="0"
                                                                max="99"
                                                                value="{{ $item['quantity'] }}">
                                                            <div wire:loading wire:target="updateQuantity" class="spinner-border spinner-border-sm position-absolute" style="width:10px;height:10px;"></div>
                                                            <button type="button" wire:click.prevent.stop="addToCartById({{ $item['product_id'] }})"
                                                                wire:loading.class="opacity-50"
                                                                class="btn btn-sm btn-outline-success ms-1" style="width:28px;height:28px;padding:0;font-size:0.7rem;">
                                                                <i class="ri-add-line"></i>
                                                                <div wire:loading wire:target="addToCartById({{ $item['product_id'] }})" class="spinner-border spinner-border-sm position-absolute" style="width:10px;height:10px;"></div>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            <div class="pt-3 border-top">
                                <div class="d-flex justify-content-between fw-semibold mb-1">
                                    <span>Subtotal:</span>
                                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if ($appliedPromotion)
                                    <div class="d-flex justify-content-between text-success mb-1">
                                        <span>Diskon ({{ $appliedPromotion->code }}):</span>
                                        <span>- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small mb-1">
                                        <span>Setelah Diskon:</span>
                                        <span>Rp {{ number_format(max(0, $subtotal - $discountAmount), 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Pajak (10%):</span>
                                    <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Biaya Layanan (5%):</span>
                                    <span>Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold fs-4 mt-2 border-top pt-2">
                                    <span>Total:</span>
                                    <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="button"
                                        wire:click.prevent.stop="setActiveTab('checkout')"
                                        wire:loading.class="opacity-75"
                                        class="btn btn-primary btn-lg"
                                        @if (empty($cart)) disabled @endif>
                                        <span wire:loading.remove wire:target="setActiveTab('checkout')">
                                            <i class="ri-arrow-right-line me-2"></i>
                                            Lanjutkan ke Pembayaran (Rp {{ number_format($totalAmount, 0, ',', '.') }})
                                        </span>
                                        <span wire:loading wire:target="setActiveTab('checkout')">
                                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            Memuat...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tab Checkout / Konfirmasi --}}
            <div class="tab-pane fade @if($activeTab == 'checkout') show active @endif" id="checkout" role="tabpanel">
                {{-- Ringkasan Keranjang di Tab Checkout --}}
                <div class="card mt-4 mb-4">
                    <h5 class="card-header">Ringkasan Pesanan</h5>
                    <div class="card-body">
                        @if (empty($cart))
                            <p>Keranjang kosong. Silakan kembali ke menu untuk memilih produk.</p>
                        @else
                            <ul class="list-group list-group-flush mb-3">
                                @foreach ($cart as $item)
                                    @if(isset($item['product_id']) && isset($item['name']) && isset($item['price']) && isset($item['quantity']))
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                            <div>
                                                <h6 class="mb-0">{{ $item['name'] }}</h6>
                                                <small class="text-muted">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</small>
                                            </div>
                                            <span class="fw-bold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            <div class="pt-3 border-top">
                                <div class="d-flex justify-content-between fw-semibold mb-1">
                                    <span>Subtotal:</span>
                                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if ($appliedPromotion)
                                    <div class="d-flex justify-content-between text-success mb-1">
                                        <span>Diskon ({{ $appliedPromotion->code }}):</span>
                                        <span>- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small mb-1">
                                        <span>Setelah Diskon:</span>
                                        <span>Rp {{ number_format(max(0, $subtotal - $discountAmount), 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Pajak (10%):</span>
                                    <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Biaya Layanan (5%):</span>
                                    <span>Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold fs-4 mt-2 border-top pt-2">
                                    <span>Total Pembayaran:</span>
                                    <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Form Promo --}}
                <div class="card mt-4 mb-4">
                    <h5 class="card-header">Kode Promo</h5>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text"
                                wire:model.debounce.500ms="promoCode"
                                id="promoCode"
                                class="form-control"
                                placeholder="Masukkan kode promo"
                                wire:loading.attr="disabled">
                            <button wire:click="applyPromo"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-75"
                                class="btn btn-primary">
                                <span wire:loading.remove wire:target="applyPromo">
                                    <i class="ri-check-line me-1"></i>Terapkan
                                </span>
                                <span wire:loading wire:target="applyPromo">
                                    <div class="spinner-border spinner-border-sm me-1" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Info Diri Pelanggan (hanya untuk tamu) --}}
                @guest
                    <div class="card mt-4 mb-4">
                        <h5 class="card-header">Informasi Diri</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="guestName" class="form-label">Nama Lengkap</label>
                                <input type="text"
                                    wire:model="guestName"
                                    id="guestName"
                                    class="form-control"
                                    required
                                    wire:loading.attr="disabled"
                                    placeholder="Masukkan nama lengkap">
                                @error('guestName')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="guestEmail" class="form-label">Email</label>
                                <input type="email"
                                    wire:model="guestEmail"
                                    id="guestEmail"
                                    class="form-control"
                                    required
                                    wire:loading.attr="disabled"
                                    placeholder="Masukkan email">
                                @error('guestEmail')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="guestPhone" class="form-label">No. Telepon</label>
                                <input type="text"
                                    wire:model="guestPhone"
                                    id="guestPhone"
                                    class="form-control"
                                    required
                                    wire:loading.attr="disabled"
                                    placeholder="Masukkan nomor telepon">
                                @error('guestPhone')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endguest

                {{-- Metode Pembayaran dan Catatan --}}
                <div class="card mt-4 mb-4">
                    <h5 class="card-header">Pembayaran dan Catatan</h5>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                        type="radio"
                                        wire:model="paymentMethod"
                                        id="paymentQRIS"
                                        value="QRIS"
                                        wire:loading.attr="disabled">
                                    <label class="form-check-label" for="paymentQRIS">QRIS</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                        type="radio"
                                        wire:model="paymentMethod"
                                        id="paymentCash"
                                        value="cash"
                                        wire:loading.attr="disabled">
                                    <label class="form-check-label" for="paymentCash">Bayar di Kasir</label>
                                </div>
                            </div>
                            @error('paymentMethod')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="orderNote" class="form-label">Catatan Pesanan (Opsional)</label>
                            <textarea wire:model.defer="orderNote"
                                id="orderNote"
                                rows="3"
                                class="form-control"
                                wire:loading.attr="disabled"></textarea>
                            @error('orderNote')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tombol Bayar Sticky --}}
                <div class="checkout-sticky-submit-wrap mt-4 mb-5">
                    <div class="checkout-sticky-submit">
                        <button type="button" wire:click="submitOrder"
                            wire:loading.attr="disabled"
                            wire:loading.class="is-loading"
                            class="btn btn-lg checkout-sticky-submit-btn"
                            @if (empty($cart)) disabled @endif>
                            <span wire:loading.remove wire:target="submitOrder">
                                <i class="ri-bank-card-line me-2"></i>
                                Bayar Sekarang (Rp {{ number_format($totalAmount, 0, ',', '.') }})
                            </span>
                            <span wire:loading wire:target="submitOrder">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Memproses Pembayaran...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endif
</div>

{{-- Animasi CSS tambahan --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
.product-card {
    transition: all .3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}
.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    border-color: rgba(0,0,0,0.1);
}
.product-btn-plus:active, .product-btn-minus:active {
    transform: scale(0.9);
    transition: transform .1s ease;
}

/* Premium empty state for cart */
.cart-empty-state {
    padding: 1.25rem 0.25rem;
}

.cart-empty-card {
    position: relative;
    overflow: hidden;
    max-width: 680px;
    margin: 0 auto;
    padding: 2.25rem 1.5rem;
    text-align: center;
    border-radius: 20px;
    border: 1px solid rgba(15, 111, 138, 0.16);
    background: linear-gradient(160deg, rgba(255, 255, 255, 0.95), rgba(241, 249, 252, 0.92));
    box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
}

.cart-empty-highlight {
    position: absolute;
    width: 180px;
    height: 180px;
    right: -52px;
    top: -58px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(15, 111, 138, 0.16), transparent 68%);
}

.cart-empty-icon-wrap {
    width: 78px;
    height: 78px;
    border-radius: 22px;
    margin: 0 auto 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #0f6f8a;
    font-size: 2rem;
    background: linear-gradient(135deg, #e8f6fb, #f4fbff);
    border: 1px solid #cbe7f1;
}

.cart-empty-title {
    color: #0f172a;
    font-weight: 700;
    letter-spacing: -0.2px;
}

.cart-empty-subtitle {
    color: #64748b;
    max-width: 480px;
    margin-left: auto;
    margin-right: auto;
}

.cart-empty-pills {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.5rem;
}

.cart-empty-pill {
    display: inline-flex;
    align-items: center;
    font-size: 0.78rem;
    font-weight: 600;
    color: #0f4f66;
    background: #eef7fb;
    border: 1px solid #d5e9f1;
    border-radius: 999px;
    padding: 0.35rem 0.65rem;
}

@media (max-width: 767.98px) {
    .cart-empty-card {
        padding: 1.75rem 1rem;
        border-radius: 16px;
    }

    .cart-empty-title {
        font-size: 1.05rem;
    }

    .cart-empty-subtitle {
        font-size: 0.85rem;
    }
}

/* Animasi untuk kategori section */
.category-section {
    animation: fadeInUp 0.5s ease-out;
}

/* Loading state */
.opacity-50 {
    transition: opacity 0.3s ease;
}

/* Spinner animation */
.spinner-border-sm {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Category filter chips */
.category-filter-wrap {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    position: sticky;
    top: var(--menu-filter-sticky-top, 82px);
    z-index: 1030;
    padding: 0.35rem 0.25rem 0.2rem;
    margin-left: -0.25rem;
    margin-right: -0.25rem;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(6px);
    border-radius: 14px;
}

.category-filter-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    min-width: 96px;
    min-height: 36px;
    padding: 0.4rem 0.9rem;
    border: 1px solid #b7d8e2;
    background: #fff;
    color: #0f6f8a;
    font-weight: 600;
    letter-spacing: 0.1px;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.category-filter-btn:hover {
    transform: translateY(-2px);
    border-color: #87c2d2;
    background: #f0fbff;
    color: #0b5a70;
    box-shadow: 0 8px 18px rgba(15, 111, 138, 0.15);
}

.category-filter-btn:focus-visible {
    outline: 2px solid rgba(15, 111, 138, 0.35);
    outline-offset: 2px;
}

.category-filter-btn .category-filter-icon {
    width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: rgba(15, 111, 138, 0.1);
}

.category-filter-btn.is-active {
    color: #fff;
    border-color: #0f6f8a;
    background: linear-gradient(135deg, #0f6f8a, #1696b6);
    box-shadow: 0 10px 20px rgba(15, 111, 138, 0.28);
    animation: category-active-in 0.22s ease;
}

.category-filter-btn.is-active .category-filter-icon {
    background: rgba(255, 255, 255, 0.25);
}

.category-filter-btn.is-loading {
    opacity: 0.85;
}

/* Sticky cart summary button on menu tab */
.menu-sticky-cart-summary-wrap {
    position: sticky;
    bottom: 0.75rem;
    z-index: 1038;
    margin-top: 0.85rem;
    padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 0.15rem);
}

.menu-sticky-cart-summary-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    border: 1px solid rgba(15, 111, 138, 0.2);
    border-radius: 14px;
    padding: 0.45rem;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 10px 22px rgba(15, 111, 138, 0.2);
}

.menu-sticky-cart-summary-link {
    flex: 1 1 auto;
    display: block;
    border: 0;
    border-radius: 10px;
    padding: 0.55rem 0.75rem;
    text-align: left;
    font: inherit;
    cursor: pointer;
    text-decoration: none;
    color: #fff;
    background: linear-gradient(135deg, #0f6f8a, #1b88a8);
    box-shadow: 0 8px 18px rgba(15, 111, 138, 0.26);
    transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
}

.menu-sticky-cart-summary-link:hover {
    transform: translateY(-2px);
    filter: brightness(1.02);
    box-shadow: 0 12px 24px rgba(15, 111, 138, 0.3);
    color: #fff;
}

.menu-sticky-cart-summary-link:focus-visible,
.menu-sticky-cart-checkout-link:focus-visible {
    outline: 3px solid rgba(22, 164, 195, 0.32);
    outline-offset: 2px;
}

.menu-sticky-cart-checkout-link {
    flex: 0 0 auto;
    align-self: stretch;
    min-width: 100px;
    border-radius: 10px;
    border: 1px solid #0f6f8a;
    padding: 0 0.85rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font: inherit;
    cursor: pointer;
    text-decoration: none;
    font-weight: 700;
    color: #0f6f8a;
    background: #e8f7fb;
    transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
}

.menu-sticky-cart-checkout-link:hover {
    transform: translateY(-2px);
    color: #0b5a70;
    background: #d9f1f8;
    box-shadow: 0 8px 18px rgba(15, 111, 138, 0.18);
}

.menu-sticky-cart-summary-title {
    display: block;
    font-weight: 700;
    letter-spacing: 0.1px;
    white-space: nowrap;
}

.menu-sticky-cart-summary-meta {
    display: block;
    font-size: 0.88rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.95);
    white-space: nowrap;
}

/* Sticky checkout payment button */
.checkout-sticky-submit-wrap {
    position: sticky;
    bottom: 0.75rem;
    z-index: 1040;
    padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 0.15rem);
}

.checkout-sticky-submit {
    border: 1px solid rgba(15, 111, 138, 0.18);
    border-radius: 16px;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(8px);
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.12);
}

.checkout-sticky-submit-btn {
    width: 100%;
    min-height: 52px;
    border: 0;
    border-radius: 12px;
    font-weight: 700;
    letter-spacing: 0.15px;
    color: #fff;
    background: linear-gradient(135deg, #0f6f8a, #16a4c3);
    box-shadow: 0 10px 20px rgba(15, 111, 138, 0.28);
    transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
}

.checkout-sticky-submit-btn:hover {
    transform: translateY(-2px);
    filter: brightness(1.02);
    box-shadow: 0 14px 30px rgba(15, 111, 138, 0.34);
}

.checkout-sticky-submit-btn:active {
    transform: translateY(0);
}

.checkout-sticky-submit-btn:focus-visible {
    outline: 3px solid rgba(22, 164, 195, 0.35);
    outline-offset: 2px;
}

.checkout-sticky-submit-btn.is-loading {
    opacity: 0.92;
}

.checkout-sticky-submit-btn:disabled {
    background: linear-gradient(135deg, #94a3b8, #cbd5e1);
    box-shadow: none;
    cursor: not-allowed;
}

@keyframes category-active-in {
    0% {
        transform: scale(0.96);
    }
    100% {
        transform: scale(1);
    }
}

@media (max-width: 768px) {
    /* Responsive untuk card produk */
    .product-card {
        min-height: 180px !important;
    }
    .product-card .card-img-top {
        min-height: 80px !important;
        max-height: 100px !important;
    }

    .category-filter-wrap {
        gap: 0.3rem;
        top: var(--menu-filter-sticky-top, 76px);
    }

    .category-filter-btn {
        min-width: 84px;
        min-height: 34px;
        padding: 0.35rem 0.75rem;
        font-size: 0.76rem;
    }

    .checkout-sticky-submit-wrap {
        bottom: 0.5rem;
    }

    .menu-sticky-cart-summary-wrap {
        bottom: 0.5rem;
    }

    .menu-sticky-cart-summary-btn {
        padding: 0.4rem;
        border-radius: 12px;
        gap: 0.4rem;
    }

    .menu-sticky-cart-summary-link {
        padding: 0.5rem 0.62rem;
    }

    .menu-sticky-cart-checkout-link {
        min-width: 88px;
        font-size: 0.82rem;
        padding: 0 0.55rem;
    }

    .menu-sticky-cart-summary-title {
        font-size: 0.86rem;
        white-space: normal;
    }

    .menu-sticky-cart-summary-meta {
        font-size: 0.78rem;
        white-space: normal;
    }

    .checkout-sticky-submit {
        border-radius: 14px;
        padding: 0.45rem;
    }

    .checkout-sticky-submit-btn {
        min-height: 48px;
        font-size: 0.92rem;
    }
}

/* Smooth scroll untuk filter kategori */
.filter-scroll {
    scrollbar-width: thin;
    scrollbar-color: rgba(0,0,0,0.2) transparent;
}
.filter-scroll::-webkit-scrollbar {
    height: 4px;
}
.filter-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.filter-scroll::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 2px;
}

/* Loading overlay */
.loading-overlay {
    position: relative;
}

.loading-overlay::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

/* Placeholder animation */
.placeholder-glow .placeholder {
    animation: placeholder-glow 2s ease-in-out infinite;
}

@keyframes placeholder-glow {
    50% {
        opacity: .5;
    }
}

/* Skeleton loading */
.skeleton-card {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Mobile cart responsiveness */
@media (max-width: 767.98px) {
    .list-group-item {
        padding: 0.75rem;
    }

    .list-group-item .row {
        margin: 0;
    }

    .list-group-item .col-8,
    .list-group-item .col-4 {
        padding: 0 0.25rem;
    }

    /* Prevent horizontal scroll */
    .card-body {
        overflow-x: hidden;
    }

    /* Smaller buttons for mobile */
    .btn-sm {
        font-size: 0.75rem;
    }

    /* Compact input */
    .form-control-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }

    /* Ensure text doesn't overflow */
    .text-truncate {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Cart item mobile optimization */
.cart-item-mobile {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Prevent horizontal scroll in cart */
.list-group-item .d-md-none {
    overflow-x: hidden;
    max-width: 100%;
}

/* Cart empty state is defined in premium styles section */

/* Cart item improvements */
.list-group-item {
    border-left: none;
    border-right: none;
    border-radius: 0;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

/* Cart quantity controls */
.cart-quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.cart-quantity-controls .btn {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-quantity-controls input {
    flex-shrink: 0;
    text-align: center;
    border-radius: 0.25rem;
}

    /* Optimize cart buttons for mobile */
    .list-group-item .btn {
        min-width: 28px;
        height: 28px;
        font-size: 0.7rem;
        padding: 0;
    }

    /* Optimize cart input for mobile */
    .list-group-item input[type="number"] {
        width: 50px !important;
        height: 28px !important;
        font-size: 0.8rem !important;
        text-align: center;
    }

    /* Ensure cart text doesn't break layout */
    .list-group-item h6 {
        font-size: 0.9rem;
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .list-group-item small {
        font-size: 0.75rem;
    }

}

/* Prevent horizontal scroll globally */
body {
    overflow-x: hidden;
}

.container-fluid {
    overflow-x: hidden;
}

/* Cart item mobile optimization */
.cart-item-mobile {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Additional mobile optimizations */
@media (max-width: 767.98px) {
    /* Ensure proper spacing in cart */
    .list-group-item {
        margin-bottom: 0.5rem;
    }

    /* Prevent text overflow in cart items */
    .list-group-item h6 {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Optimize cart layout for small screens */
    .list-group-item .row {
        align-items: center;
    }

    /* Ensure buttons don't cause overflow */
    .list-group-item .btn {
        flex-shrink: 0;
    }

    /* Optimize input field */
    .list-group-item input[type="number"] {
        flex-shrink: 0;
        min-width: 50px;
        max-width: 60px;
    }

    /* Ensure proper spacing between elements */
    .list-group-item .d-flex {
        gap: 0.25rem;
    }

    /* Prevent horizontal scroll in all containers */
    .container-fluid,
    .card-body,
    .list-group-item {
        overflow-x: hidden;
        max-width: 100%;
    }

    /* Ensure proper text wrapping */
    .text-truncate {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* Optimize button sizes for mobile */
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    /* Ensure proper input sizing */
    .form-control-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        height: auto;
        min-height: 28px;
    }
}

/* Loading state improvements */
.loading-overlay {
    position: relative;
}

.loading-overlay::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

/* Toast notification improvements */
.toast {
    z-index: 9999;
    max-width: 90vw;
}

@media (max-width: 767.98px) {
    .toast {
        max-width: 95vw;
        font-size: 0.9rem;
    }
}

/* Debug button improvements */
@media (max-width: 767.98px) {
    .quick-actions .btn {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }

    .quick-actions .btn i {
        font-size: 0.8rem;
    }

    .quick-actions .btn small {
        font-size: 0.6rem;
    }

    /* Ensure debug buttons don't overflow */
    .quick-actions .row {
        margin: 0;
    }

    .quick-actions .col-6 {
        padding: 0 0.25rem;
    }

    .quick-actions .btn {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Additional mobile optimizations for debug buttons */
    .quick-actions .btn {
        font-size: 0.6rem !important;
        padding: 0.2rem 0.4rem !important;
        min-height: 28px;
    }

    .quick-actions .btn i {
        font-size: 0.7rem !important;
    }

    .quick-actions .btn small {
        font-size: 0.5rem !important;
        line-height: 1;
    }

    /* Ensure proper spacing for debug buttons */
    .quick-actions .row {
        gap: 0.25rem;
    }

    .quick-actions .col-6 {
        margin-bottom: 0.25rem;
    }

    /* Additional optimizations for debug section */
    .quick-actions {
        background: rgba(0, 0, 0, 0.02);
        border-radius: 0.5rem;
        padding: 0.5rem;
        margin-bottom: 1rem;
    }

    .quick-actions .card-body {
        padding: 0.5rem;
    }

    /* Ensure debug buttons are properly sized */
    .quick-actions .btn {
        border-radius: 0.25rem;
        font-weight: 500;
    }

    /* Add hover effects for debug buttons */
    .quick-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
}

/* Additional mobile optimizations for cart */
@media (max-width: 767.98px) {
    /* Ensure cart items don't overflow */
    .list-group-item .row {
        margin: 0;
        align-items: center;
    }

    .list-group-item .col-8,
    .list-group-item .col-4 {
        padding: 0 0.25rem;
    }

    /* Optimize cart item text */
    .list-group-item h6 {
        font-size: 0.9rem;
        line-height: 1.2;
        margin-bottom: 0.25rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .list-group-item small {
        font-size: 0.75rem;
        line-height: 1.1;
    }

    /* Ensure proper button sizing */
    .list-group-item .btn {
        min-width: 28px;
        height: 28px;
        font-size: 0.7rem;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Optimize input field */
    .list-group-item input[type="number"] {
        width: 50px !important;
        height: 28px !important;
        font-size: 0.8rem !important;
        text-align: center;
        padding: 0.25rem;
        border-radius: 0.25rem;
    }

    /* Ensure proper spacing */
    .list-group-item .d-flex {
        gap: 0.25rem;
        align-items: center;
    }

    /* Prevent horizontal scroll */
    .card-body,
    .list-group-item,
    .container-fluid {
        overflow-x: hidden;
        max-width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateCategoryStickyOffset = () => {
        const root = document.documentElement;
        const topbarWrap = document.querySelector('.customer-topbar-wrap');

        if (!topbarWrap) {
            root.style.setProperty('--menu-filter-sticky-top', '82px');
            return;
        }

        const topbarStyles = window.getComputedStyle(topbarWrap);
        const topOffset = parseFloat(topbarStyles.top || '0') || 0;
        const topbarHeight = topbarWrap.getBoundingClientRect().height || topbarWrap.offsetHeight || 0;
        const stickyTop = Math.ceil(topOffset + topbarHeight + 8); // small breathing space below topbar

        root.style.setProperty('--menu-filter-sticky-top', `${stickyTop}px`);
    };

    updateCategoryStickyOffset();
    window.addEventListener('resize', updateCategoryStickyOffset);
    window.addEventListener('orientationchange', updateCategoryStickyOffset);

    // Handle Livewire notifications
    Livewire.on('show-notification', (event) => {
        const payload = event?.detail ?? event ?? {};
        const type = payload.type ?? 'info';
        const message = payload.message ?? '';

        if (!message) {
            return;
        }

        // Use toastr if available, otherwise use custom toast
        if (typeof toastr !== 'undefined' && typeof toastr[type] === 'function') {
            toastr[type](message);
        } else {
            // Create custom notification
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="ri-${type === 'success' ? 'check-line' : type === 'error' ? 'error-warning-line' : type === 'warning' ? 'alert-line' : 'information-line'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            toastContainer.appendChild(toast);

            // Initialize Bootstrap toast
            const bsToast = new bootstrap.Toast(toast, {
                autohide: true,
                delay: 5000
            });
            bsToast.show();

            // Auto remove from DOM after toast is hidden
            toast.addEventListener('hidden.bs.toast', function() {
                if (toast.parentNode) {
                    toast.remove();
                }
            });
        }
    });

    // Smooth scroll for filter buttons
    const filterButtons = document.querySelectorAll('.filter-scroll .btn');
    filterButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255,255,255,0.6);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';

            this.appendChild(ripple);

            setTimeout(() => {
                if (ripple.parentNode) {
                    ripple.remove();
                }
            }, 600);
        });
    });

    // Add ripple animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        .filter-scroll .btn {
            position: relative;
            overflow: hidden;
        }

        /* Toast animations */
        .toast {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(event) {
        // Ctrl/Cmd + F to focus search
        if ((event.ctrlKey || event.metaKey) && event.key === 'f') {
            event.preventDefault();
            const searchInput = document.querySelector('input[wire\\:model\\.debounce\\.300ms="searchTerm"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }

        // Escape to reset filters
        if (event.key === 'Escape') {
            const resetButton = document.querySelector('button[wire\\:click="resetFilters"]');
            if (resetButton) {
                resetButton.click();
            }
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Re-initialize tooltips after Livewire updates
    Livewire.hook('message.processed', (message, component) => {
        const newTooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        newTooltipTriggerList.forEach(function (tooltipTriggerEl) {
            if (!tooltipTriggerEl._tooltip) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            }
        });

        updateCategoryStickyOffset();
    });

    // Add filter debugging
    window.debugFilter = function() {
        console.log('Filter Debug Info:');
        console.log('Selected Category:', document.querySelector('select[wire\\:model="selectedCategory"]')?.value);
        console.log('Search Term:', document.querySelector('input[wire\\:model\\.debounce\\.300ms="searchTerm"]')?.value);
        console.log('Sort By:', document.querySelector('select[wire\\:model="sortBy"]')?.value);
        console.log('Sort Order:', document.querySelector('select[wire\\:model="sortOrder"]')?.value);
        console.log('Show New Products:', document.querySelector('input[wire\\:model="showNewProducts"]')?.checked);
    };

    // Add filter validation
    window.validateFilter = function() {
        const searchInput = document.querySelector('input[wire\\:model\\.debounce\\.300ms="searchTerm"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                // Trim whitespace
                this.value = this.value.trim();
            });
        }
    };

    // Initialize filter validation
    validateFilter();

    // Add filter debugging to console
    window.debugFilterConsole = function() {
        console.log('=== Filter Debug Info ===');
        console.log('Selected Category:', document.querySelector('select[wire\\:model="selectedCategory"]')?.value || 'N/A');
        console.log('Search Term:', document.querySelector('input[wire\\:model\\.debounce\\.300ms="searchTerm"]')?.value || 'N/A');
        console.log('Sort By:', document.querySelector('select[wire\\:model="sortBy"]')?.value || 'N/A');
        console.log('Sort Order:', document.querySelector('select[wire\\:model="sortOrder"]')?.value || 'N/A');
        console.log('Show New Products:', document.querySelector('input[wire\\:model="showNewProducts"]')?.checked || false);
        console.log('Categories Available:', Array.from(document.querySelectorAll('.filter-scroll .btn')).map(btn => btn.textContent.trim()));
        console.log('Products Count:', document.querySelectorAll('.product-card').length);
        console.log('Cart Items Count:', document.querySelectorAll('.list-group-item').length);
        console.log('=======================');
    };

    // Add mobile optimization
    window.optimizeMobileLayout = function() {
        if (window.innerWidth <= 768) {
            // Ensure proper spacing
            document.querySelectorAll('.list-group-item').forEach(item => {
                item.style.padding = '0.75rem';
            });

            // Optimize input fields
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.style.width = '50px';
                input.style.height = '28px';
                input.style.fontSize = '0.8rem';
            });

            // Optimize buttons
            document.querySelectorAll('.btn-sm:not(.category-filter-btn)').forEach(btn => {
                btn.style.fontSize = '0.75rem';
                btn.style.padding = '0.25rem 0.5rem';
            });
        }
    };

    // Initialize mobile optimization
    optimizeMobileLayout();
    window.addEventListener('resize', optimizeMobileLayout);

    // Add comprehensive debugging
    window.debugAll = function() {
        console.log('=== COMPREHENSIVE DEBUG ===');
        debugFilterConsole();
        console.log('Mobile Layout:', window.innerWidth <= 768 ? 'Mobile' : 'Desktop');
        console.log('Cart Items:', document.querySelectorAll('.list-group-item').length);
        console.log('Products:', document.querySelectorAll('.product-card').length);
        console.log('Categories:', document.querySelectorAll('.filter-scroll .btn').length);
        console.log('Active Tab:', document.querySelector('.tab-pane.active')?.id);
        console.log('==========================');
    };

    // Add error handling for filter issues
    window.handleFilterError = function(error) {
        console.error('Filter Error:', error);
        // You can add additional error handling here
    };

    // Add filter validation on page load
    window.validateFilterOnLoad = function() {
        setTimeout(() => {
            const searchInput = document.querySelector('input[wire\\:model\\.debounce\\.300ms="searchTerm"]');
            const categorySelect = document.querySelector('select[wire\\:model="selectedCategory"]');
            const sortBySelect = document.querySelector('select[wire\\:model="sortBy"]');
            const sortOrderSelect = document.querySelector('select[wire\\:model="sortOrder"]');
            const newProductsCheckbox = document.querySelector('input[wire\\:model="showNewProducts"]');

            if (!searchInput || !categorySelect || !sortBySelect || !sortOrderSelect || !newProductsCheckbox) {
                console.warn('Some filter elements not found');
            } else {
                console.log('All filter elements found and validated');
            }
        }, 1000);
    };

    // Initialize filter validation on load
    validateFilterOnLoad();

    // Add filter status monitoring
    window.monitorFilterStatus = function() {
        const filterElements = {
            search: document.querySelector('input[wire\\:model\\.debounce\\.300ms="searchTerm"]'),
            category: document.querySelector('select[wire\\:model="selectedCategory"]'),
            sortBy: document.querySelector('select[wire\\:model="sortBy"]'),
            sortOrder: document.querySelector('select[wire\\:model="sortOrder"]'),
            newProducts: document.querySelector('input[wire\\:model="showNewProducts"]')
        };

        const status = {
            search: filterElements.search ? filterElements.search.value : 'N/A',
            category: filterElements.category ? filterElements.category.value : 'N/A',
            sortBy: filterElements.sortBy ? filterElements.sortBy.value : 'N/A',
            sortOrder: filterElements.sortOrder ? filterElements.sortOrder.value : 'N/A',
            newProducts: filterElements.newProducts ? filterElements.newProducts.checked : false
        };

        console.log('Filter Status:', status);
        return status;
    };

    // Add filter health check
    window.checkFilterHealth = function() {
        const products = document.querySelectorAll('.product-card');
        const categories = document.querySelectorAll('.filter-scroll .btn');
        const cartItems = document.querySelectorAll('.list-group-item');

        const health = {
            products: products.length,
            categories: categories.length,
            cartItems: cartItems.length,
            mobileLayout: window.innerWidth <= 768,
            timestamp: new Date().toISOString()
        };

        console.log('Filter Health Check:', health);
        return health;
    };

    // Monitor filter status every 30 seconds
    setInterval(() => {
        if (window.innerWidth <= 768) {
            monitorFilterStatus();
            checkFilterHealth();
        }
    }, 30000);

    // Add comprehensive system monitoring
    window.monitorSystem = function() {
        const systemStatus = {
            timestamp: new Date().toISOString(),
            screenSize: {
                width: window.innerWidth,
                height: window.innerHeight,
                isMobile: window.innerWidth <= 768
            },
            filterElements: {
                search: !!document.querySelector('input[wire\\:model\\.debounce\\.300ms="searchTerm"]'),
                category: !!document.querySelector('select[wire\\:model="selectedCategory"]'),
                sortBy: !!document.querySelector('select[wire\\:model="sortBy"]'),
                sortOrder: !!document.querySelector('select[wire\\:model="sortOrder"]'),
                newProducts: !!document.querySelector('input[wire\\:model="showNewProducts"]')
            },
            contentElements: {
                products: document.querySelectorAll('.product-card').length,
                categories: document.querySelectorAll('.filter-scroll .btn').length,
                cartItems: document.querySelectorAll('.list-group-item').length,
                activeTab: document.querySelector('.tab-pane.active')?.id || 'none'
            },
            performance: {
                memoryUsage: performance.memory ? performance.memory.usedJSHeapSize : 'N/A',
                loadTime: performance.timing ? performance.timing.loadEventEnd - performance.timing.navigationStart : 'N/A'
            }
        };

        return systemStatus;
    };

    // Initialize system monitoring
    setTimeout(() => {
        monitorSystem();
    }, 2000);

    // Add filter performance monitoring
    window.monitorFilterPerformance = function() {
        const startTime = performance.now();

        // Simulate filter operation
        const filterOperation = () => {
            const endTime = performance.now();
            const duration = endTime - startTime;

            console.log('Filter Performance:', {
                duration: duration.toFixed(2) + 'ms',
                timestamp: new Date().toISOString(),
                isMobile: window.innerWidth <= 768
            });

            return duration < 100; // Consider good if under 100ms
        };

        return filterOperation();
    };

    // Add comprehensive error handling
    window.handleFilterErrors = function(error, context = {}) {
        console.error('Filter Error:', {
            error: error.message || error,
            context: context,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent,
            screenSize: {
                width: window.innerWidth,
                height: window.innerHeight
            }
        });

        // You can add additional error reporting here
        // For example, sending to an error tracking service
    };

    // Add filter recovery mechanism
    window.recoverFromFilterError = function() {
        console.log('Attempting filter recovery...');

        // Try to reset filter state
        const filterElements = {
            search: document.querySelector('input[wire\\:model\\.debounce\\.300ms="searchTerm"]'),
            category: document.querySelector('select[wire\\:model="selectedCategory"]'),
            sortBy: document.querySelector('select[wire\\:model="sortBy"]'),
            sortOrder: document.querySelector('select[wire\\:model="sortOrder"]'),
            newProducts: document.querySelector('input[wire\\:model="showNewProducts"]')
        };

        // Reset to defaults if elements exist
        if (filterElements.search) filterElements.search.value = '';
        if (filterElements.category) filterElements.category.value = 'all';
        if (filterElements.sortBy) filterElements.sortBy.value = 'name';
        if (filterElements.sortOrder) filterElements.sortOrder.value = 'asc';
        if (filterElements.newProducts) filterElements.newProducts.checked = false;

        console.log('Filter recovery completed');
    };

    // Monitor filter performance periodically
    setInterval(() => {
        if (window.innerWidth <= 768) {
            monitorFilterPerformance();
        }
    }, 60000); // Check every minute

    // Auto-fill guest info from session if available
    window.autoFillGuestInfo = function() {
        const guestNameInput = document.getElementById('guestName');
        const guestEmailInput = document.getElementById('guestEmail');
        const guestPhoneInput = document.getElementById('guestPhone');

        if (guestNameInput && guestEmailInput && guestPhoneInput) {
            // Check if inputs are empty and try to fill from session data
            if (!guestNameInput.value && !guestEmailInput.value && !guestPhoneInput.value) {
                // Trigger Livewire to check session and update inputs
                Livewire.dispatch('check-guest-session');
            }
        }
    };

    // Function to fill guest info inputs
    window.fillGuestInfo = function(name, email, phone) {
        const guestNameInput = document.getElementById('guestName');
        const guestEmailInput = document.getElementById('guestEmail');
        const guestPhoneInput = document.getElementById('guestPhone');

        if (guestNameInput && name) {
            guestNameInput.value = name;
            guestNameInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (guestEmailInput && email) {
            guestEmailInput.value = email;
            guestEmailInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (guestPhoneInput && phone) {
            guestPhoneInput.value = phone;
            guestPhoneInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        console.log('Guest info filled:', { name, email, phone });
    };

    // Initialize auto-fill when page loads
    setTimeout(() => {
        autoFillGuestInfo();
    }, 1000);

    // Auto-fill when switching to checkout tab
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('tab-changed', (event) => {
            const payload = event?.detail ?? event ?? {};
            if (payload.tab === 'checkout') {
                setTimeout(() => {
                    autoFillGuestInfo();
                }, 500);
            }
        });

                // Listen for guest info loaded event
        Livewire.on('guest-info-loaded', (event) => {
            const payload = event?.detail ?? event ?? {};
            const { name, email, phone } = payload;
            fillGuestInfo(name, email, phone);
        });

        // Listen for tab changes
        Livewire.on('tab-changed', (event) => {
            const payload = event?.detail ?? event ?? {};
            if (payload.tab === 'checkout') {
                setTimeout(() => {
                    autoFillGuestInfo();
                }, 500);
            }
        });
    });
});
</script>
@endpush

