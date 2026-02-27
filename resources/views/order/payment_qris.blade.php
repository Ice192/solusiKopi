<x-order-layout>
    <x-slot name="title">
        Pembayaran QRIS
    </x-slot>

    <div class="card p-4 p-md-5 mb-4">
        <div class="card-body">
            <div class="app-brand justify-content-center mb-5">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        <!-- SVG Logo here from _partials/app-brand-logo.blade.php if needed -->
                        <i class="ri-cup-line text-primary fs-2"></i>
                    </span>
                    <span class="app-brand-text demo text-body fw-semibold">{{ config('app.name') }}</span>
                </a>
            </div>
            <h4 class="mb-2 text-center">Pembayaran Pesanan #{{ $order->order_number }}</h4>

            @if ($order->status === 'cancelled')
                <div class="alert alert-danger text-center mb-4">
                    <i class="ri-close-circle-line me-2"></i>
                    <strong>Pesanan Dibatalkan</strong><br>
                    Pesanan ini telah dibatalkan dan tidak dapat diproses pembayaran.
                </div>
            @else
                <p class="mb-4 text-center">Silakan scan QR code di bawah untuk menyelesaikan pembayaran sebesar <span
                        class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></p>
            @endif

            {{-- Notifikasi (akan ditangani via Toastr JS) --}}
            <div id="payment-status-message" class="alert d-none" role="alert"></div>

            <div class="text-center mb-4">
                @if ($order->status === 'cancelled')
                    <div class="alert alert-danger mb-3">
                        <i class="ri-close-circle-line me-2"></i>
                        <strong>Pesanan Dibatalkan</strong><br>
                        QR Code pembayaran tidak tersedia karena pesanan telah dibatalkan.
                    </div>
                @elseif(isset($midtransError) && $midtransError)
                    <div class="alert alert-danger mb-3">Terjadi error pada pembayaran Midtrans: {{ $midtransError }}
                    </div>
                @endif

                @if (isset($snapToken) && $snapToken)
                    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
                    <div id="snap-qris-action" class="text-center mt-3">
                        <div class="d-flex justify-content-center flex-wrap gap-3">
                            <button id="refresh-snap" class="btn btn-secondary mb-2">Tampilkan QRIS</button>

                            @if (in_array($order->status, ['pending', 'processing']) && $order->payment_status !== 'paid')
                                <button type="button" class="btn btn-outline-danger mb-2" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                                    <i class="ri-close-circle-line me-2"></i>Batalkan Pesanan
                                </button>
                            @endif
                        </div>

                        @if ($order->status === 'cancelled')
                            <div class="alert alert-danger mt-4">
                                <i class="ri-close-circle-line me-2"></i>
                                <strong>Pesanan Dibatalkan</strong><br>
                                Pesanan ini telah dibatalkan dan tidak dapat diproses kembali.
                            </div>
                        @elseif (!in_array($order->status, ['pending', 'processing']) || $order->payment_status === 'paid')
                            <div class="alert alert-info mt-4">
                                <i class="ri-information-line me-2"></i>
                                <strong>Pesanan Tidak Dapat Dibatalkan</strong><br>
                                Status pesanan saat ini tidak memungkinkan untuk dibatalkan.
                            </div>
                        @endif
                    </div>
                    <script>
                        let urlSuccess = "{{ route('order.success', $order->order_number) }}";
                    </script>
                    <script>
                        let snapPopup = null;

                        function openSnap() {
                            snapPopup = window.snap.pay('{{ $snapToken }}', {
                                onSuccess: function(result) {
                                    window.location.href = urlSuccess;
                                },
                                onPending: function(result) {
                                    toastr.info('Silakan selesaikan pembayaran QRIS di aplikasi e-wallet Anda.');
                                },
                                onError: function(result) {
                                    toastr.error('Pembayaran gagal.');
                                },
                                onClose: function() {
                                    toastr.info('Popup pembayaran ditutup.');
                                }
                            });
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            openSnap();
                            document.getElementById('refresh-snap').onclick = function() {
                                openSnap();
                            };
                            document.getElementById('cancel-snap').onclick = function() {
                                if (snapPopup && typeof snapPopup.close === 'function') {
                                    snapPopup.close();
                                } else {
                                    // Snap popup tidak expose close, reload page saja
                                    location.reload();
                                }
                            };
                        });
                    </script>
                    <div class="alert alert-info mt-2">QRIS akan muncul di popup. Jika ingin scan ulang, klik <b>Refresh
                            QRIS</b>. Untuk membatalkan, klik <b>Batalkan</b>.</div>
                    {{-- Jika backend sudah support, tampilkan QR code image url di bawah ini --}}
                    @if (isset($midtransQrImageUrl) && $midtransQrImageUrl)
                        <div class="mt-3">
                            <img src="{{ $midtransQrImageUrl }}" alt="QRIS Midtrans"
                                class="img-fluid border rounded p-2" style="max-width: 280px;">
                            <div class="mt-2">
                                <a href="{{ $midtransQrImageUrl }}"
                                    download="qris-midtrans-{{ $order->order_number }}.png"
                                    class="btn btn-success btn-sm">Download QR Code</a>
                            </div>
                            <div class="mt-2 small text-muted">QR Code Image URL: <a href="{{ $midtransQrImageUrl }}"
                                    target="_blank">{{ $midtransQrImageUrl }}</a></div>
                        </div>
                    @endif
                @endif

                @if ($order->status !== 'cancelled')
                    @if ($qr_image)
                        <img src="data:image/png;base64,{{ $qr_image }}" alt="QR Code Pembayaran"
                            class="img-fluid border rounded p-2" style="max-width: 280px;">
                        <div class="mt-2">
                            <a href="data:image/png;base64,{{ $qr_image }}"
                                download="qris-{{ $order->order_number }}.png" class="btn btn-success btn-sm">Download
                                QR Code</a>
                        </div>
                    @endif
                @endif
            </div>

            @if ($order->status !== 'cancelled')
                <h5 class="mt-4 mb-3 text-center">Instruksi Pembayaran:</h5>
                <ol class="list-group list-group-numbered mb-4 px-3">
                    <li class="list-group-item">Buka aplikasi pembayaran Anda (mobile banking/e-wallet).</li>
                    <li class="list-group-item">Pilih fitur 'Scan QRIS' atau 'Pembayaran QR'.</li>
                    <li class="list-group-item">Scan QR Code di atas.</li>
                    <li class="list-group-item">Pastikan jumlah pembayaran adalah <span class="fw-bold">Rp
                            {{ number_format($order->total_amount, 0, ',', '.') }}</span>.</li>
                    <li class="list-group-item">Konfirmasi pembayaran Anda.</li>
                    <li class="list-group-item">Halaman ini akan otomatis diperbarui setelah pembayaran berhasil.</li>
                </ol>
            @endif

            @if ($order->status !== 'cancelled')
                <div class="d-grid" id="polling-status-container">
                    <button class="btn btn-label-secondary d-flex align-items-center justify-content-center"
                        type="button" disabled>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <span id="polling-message">Menunggu pembayaran...</span>
                    </button>
                </div>
            @else
                <div class="d-grid">
                    <a href="{{ route('order.history') }}" class="btn btn-secondary d-grid w-100">Kembali ke Riwayat
                        Pesanan</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Konfirmasi Pembatalan --}}
    @if (in_array($order->status, ['pending', 'processing']) && $order->payment_status !== 'paid')
        <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelOrderModalLabel">Konfirmasi Pembatalan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin membatalkan pesanan <strong>#{{ $order->order_number }}</strong>?
                        </p>
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-2"></i>
                            <strong>Perhatian:</strong> Pembatalan pesanan tidak dapat dibatalkan kembali.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="confirmCancelOrder">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="cancelSpinner"
                                role="status"></span>
                            <span id="cancelButtonText">Ya, Batalkan Pesanan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
    </div>

    @push('scripts')
        <script>
            let checkPaymentStatusUrl = "{{ route('order.payment.status', ':id') }}";
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const orderNumber = '{{ $order->order_number }}';
                const paymentStatusMessage = document.getElementById('payment-status-message');
                const pollingMessage = document.getElementById('polling-message');
                const pollingContainer = document.getElementById('polling-status-container');
                let pollingInterval;

                function showNotification(type, message, title = '') {
                    toastr[type](message, title);
                }

                async function checkPaymentStatus() {
                    try {
                        const response = await fetch(checkPaymentStatusUrl.replace(':id', orderNumber));
                        const data = await response.json();

                        if (data.payment_status === 'paid') {
                            showNotification('success', 'Pembayaran berhasil dikonfirmasi!', 'Pembayaran Sukses');
                            clearInterval(pollingInterval);
                            pollingContainer.innerHTML =
                                `<a href="${urlSuccess}" class="btn btn-success d-grid w-100">Pembayaran Berhasil! Lihat Pesanan Anda</a>`;
                            window.location.href = `${urlSuccess}`;
                        } else if (data.payment_status === 'failed') {
                            showNotification('error', 'Pembayaran gagal. Silakan coba lagi.', 'Pembayaran Gagal');
                            clearInterval(pollingInterval);
                            pollingContainer.innerHTML =
                                '<button class="btn btn-danger d-grid w-100" disabled>Pembayaran Gagal</button>';
                        } else if (data.payment_status === 'cancelled' || data.order_status === 'cancelled') {
                            showNotification('info', 'Pesanan telah dibatalkan.', 'Pesanan Dibatalkan');
                            clearInterval(pollingInterval);
                            pollingContainer.innerHTML =
                                '<a href="{{ route('order.history') }}" class="btn btn-secondary d-grid w-100">Kembali ke Riwayat Pesanan</a>';
                        } else {
                            pollingMessage.innerText = 'Menunggu pembayaran... Status: ' + data
                                .payment_status; // Update message with current status
                        }
                    } catch (error) {
                        console.error('Error checking payment status:', error);
                        showNotification('error', 'Gagal memeriksa status pembayaran. Silakan refresh halaman.',
                            'Error');
                        clearInterval(pollingInterval);
                        pollingContainer.innerHTML =
                            '<button class="btn btn-danger d-grid w-100" disabled>Terjadi Kesalahan</button>';
                    }
                }

                // Start polling every 5 seconds (only if order is not cancelled)
                if ('{{ $order->status }}' !== 'cancelled') {
                    pollingInterval = setInterval(checkPaymentStatus, 5000);
                    // Initial check
                    checkPaymentStatus();
                }

                // Handle order cancellation (only if order is not cancelled)
                if ('{{ $order->status }}' !== 'cancelled') {
                    const cancelButton = document.getElementById('confirmCancelOrder');
                    if (cancelButton) {
                        cancelButton.addEventListener('click', function() {
                            const button = this;
                            const spinner = document.getElementById('cancelSpinner');
                            const buttonText = document.getElementById('cancelButtonText');

                            // Show loading state
                            button.disabled = true;
                            spinner.classList.remove('d-none');
                            buttonText.textContent = 'Membatalkan...';

                            // Send cancellation request
                            fetch(`{{ route('order.cancel', $order->order_number) }}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                    },
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        showNotification('success', 'Pesanan berhasil dibatalkan!',
                                            'Pembatalan Sukses');

                                        // Close modal
                                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                                            'cancelOrderModal'));
                                        modal.hide();

                                        // Redirect to order history
                                        setTimeout(() => {
                                            window.location.href = '{{ route('order.history') }}';
                                        }, 1500);
                                    } else {
                                        showNotification('error', data.message ||
                                            'Gagal membatalkan pesanan.', 'Pembatalan Gagal');

                                        // Reset button state
                                        button.disabled = false;
                                        spinner.classList.add('d-none');
                                        buttonText.textContent = 'Ya, Batalkan Pesanan';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error cancelling order:', error);
                                    showNotification('error', 'Terjadi kesalahan saat membatalkan pesanan.',
                                        'Error');

                                    // Reset button state
                                    button.disabled = false;
                                    spinner.classList.add('d-none');
                                    buttonText.textContent = 'Ya, Batalkan Pesanan';
                                });
                        });
                    }
                }
            });
        </script>
    @endpush
</x-order-layout>
