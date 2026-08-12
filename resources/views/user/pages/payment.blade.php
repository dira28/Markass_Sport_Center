@extends('layouts.app')

@section('title', 'Payment Checkout - Markass')

@push('styles')
    @vite('resources/css/user/pages/payment.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="payment-page">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="payment-card">

                        <!-- Header -->
                        <div
                            class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary-subtle">
                            <div class="brand-title d-flex align-items-center">
                                <div class="icon-box me-3">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark">Pembayaran Booking</h4>
                                    <small class="text-muted">Selesaikan pembayaran untuk mengamankan slot Anda</small>
                                </div>
                            </div>
                            <div class="countdown" id="countdown">
                                <div class="countdown-icon">
                                    <i class="fas fa-hourglass-half fa-spin-pulse"></i>
                                </div>
                                <div class="countdown-time">
                                    <span id="timer">00:00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Row Utama -->
                        <div class="row g-4">

                            <!-- KIRI: QR & Upload -->
                            <div class="col-lg-7">
                                <div class="qr-section">
                                    <h6 class="text-center fw-bold mb-3 text-uppercase tracking-wider">Scan QR DANA</h6>
                                    <div class="qr-container">
                                        <div class="qr-card" id="qrCardTrigger" title="Klik untuk memperbesar QR">
                                            <img src="{{ asset('images/qr-dana.jpg') }}" alt="QR DANA Payment"
                                                class="qr-image" id="qrImage">
                                            <div class="qr-overlay">
                                                <i class="fas fa-search-plus"></i>
                                                <span>Klik Zoom</span>
                                            </div>
                                        </div>
                                        <div class="qr-instructions mt-2">
                                            <i class="fas fa-info-circle me-1 text-primary"></i> Scan QR di atas lalu upload
                                            bukti pembayaran di bawah ini.
                                        </div>
                                    </div>
                                </div>

                                <div id="paymentForm" class="upload-section mt-4">
                                    <h6 class="upload-title fw-bold">Upload Bukti Pembayaran</h6>
                                    <div class="upload-box" id="uploadBox" role="button" tabindex="0">
                                        <div class="upload-icon-wrapper mb-2">
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        </div>
                                        <div id="uploadPlaceholder">
                                            <strong class="text-dark d-block mb-1">Pilih atau seret foto bukti
                                                transfer</strong>
                                            <p class="text-muted small mb-0">Format JPG, PNG (maksimal 2MB)</p>
                                        </div>
                                        <input type="file" id="proofFile" accept="image/jpeg,image/png,.jpg,.jpeg,.png"
                                            class="d-none">
                                        <button class="btn btn-select-file mt-3" id="pickBtn" type="button">
                                            <i class="fas fa-image me-1"></i> Pilih File Gambar
                                        </button>
                                    </div>

                                    <div id="uploadPreview" style="display: none;" class="mt-3 text-center">
                                        <img id="previewImg" class="upload-preview mb-2">
                                        <p class="text-success small fw-bold mb-0"><i class="fas fa-check-circle"></i> File
                                            siap dikirim</p>
                                    </div>

                                    <button class="btn payment-btn mt-3 w-100" id="submitPaymentBtn" type="button" disabled>
                                        <i class="fas fa-paper-plane me-1"></i> Kirim Bukti Pembayaran
                                    </button>
                                </div>

                                <button class="btn btn-outline-danger mt-2 w-100" id="cancelBookingBtn" type="button">
                                    <i class="fas fa-times-circle me-1"></i> Batalkan Booking
                                </button>

                                <div id="statusMessage" style="display: none;" class="mt-4 text-center">
                                    <h5 id="messageTitle"></h5>
                                    <p id="messageText"></p>
                                </div>
                            </div>

                            <!-- KANAN: Ringkasan -->
                            <div class="col-lg-5">
                                <div class="info-card">
                                    <h6 class="info-card-title">RINGKASAN BOOKING</h6>
                                    <div class="info-list">
                                        <!-- ... (Isi ringkasan kamu tetap sama) ... -->
                                        <p><strong>ID Booking</strong> <span id="bookingId"
                                                class="text-break">{{ $booking['id_booking'] ?? $booking['id'] ?? 'N/A' }}</span>
                                        </p>
                                        <p><strong>Lapangan</strong> <span
                                                id="lapanganNama">{{ $booking['lapangan']['nama_lapangan'] ?? $booking['nama_lapangan'] ?? 'N/A' }}</span>
                                        </p>
                                        <p><strong>Tanggal</strong> <span
                                                id="bookingDate">{{ isset($booking['tanggal']) ? \Carbon\Carbon::parse($booking['tanggal'])->format('d M Y') : 'N/A' }}</span>
                                        </p>
                                        <p><strong>Jam Main</strong> <span
                                                id="bookingTime">{{ $booking['jam_mulai'] ?? '12:00' }} -
                                                {{ $booking['jam_selesai'] ?? '14:00' }}</span></p>

                                        @php
                                            $durasiValue = $booking['durasi'] ?? $booking['duration'] ?? null;
                                            if (!$durasiValue) {
                                                $jmMulai = $booking['jam_mulai'] ?? null;
                                                $jmSelesai = $booking['jam_selesai'] ?? null;
                                                if ($jmMulai && $jmSelesai) {
                                                    try {
                                                        $startHour = (int) substr($jmMulai, 0, 2);
                                                        $endHour = (int) substr($jmSelesai, 0, 2);
                                                        $durasiValue = max(1, $endHour - $startHour);
                                                    } catch (\Throwable $e) {
                                                        $durasiValue = '-';
                                                    }
                                                } else {
                                                    $durasiValue = '-';
                                                }
                                            }
                                        @endphp
                                        <p><strong>Durasi Main</strong> <span><span
                                                    id="bookingDuration">{{ $durasiValue }}</span> Jam</span></p>
                                        <p><strong>Status</strong>
                                            <span class="badge status-badge {{ $statusClass ?? 'bg-warning' }}"
                                                id="statusBadge"
                                                data-payment-status="{{ $booking['status_pembayaran'] ?? $booking['status'] ?? 'pending' }}">
                                                {{ $statusText ?? ucfirst(str_replace('_', ' ', $booking['status_pembayaran'] ?? $booking['status'] ?? 'pending')) }}
                                            </span>
                                        </p>
                                    </div>
                                    <input type="hidden" id="paymentDeadline" value="{{ $deadlineIso ?? '' }}">
                                    <div class="total-display text-center mt-4">
                                        <div class="text-uppercase tracking-wider small fw-bold text-muted">Total Tagihan
                                        </div>
                                        <div class="amount" id="paymentTotal">Rp
                                            {{ number_format($booking['total_harga'] ?? 40000, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold text-dark"><i class="fas fa-qrcode text-primary me-2"></i>Scan QR DANA
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="{{ asset('images/qr-dana.jpg') }}" class="img-fluid rounded-3 shadow-sm" alt="QR Enlarged">
                    <p class="small text-muted mt-3 mb-0">Perbesar kecerahan layar HP kamu untuk mempermudah scan.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.paymentStatus = "{{ $booking['status_pembayaran'] ?? $booking['status'] ?? 'pending' }}";
        window.authToken = "{{ session('token') }}";
    </script>

    @vite('resources/js/payment.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection