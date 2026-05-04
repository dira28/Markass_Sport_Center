@extends('layouts.app')

@section('title', 'Payment - Markass')

@push('styles')
@vite('resources/css/user/pages/payment.css')
@endpush

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="payment-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Pembayaran Booking</h4>
                    <div class="countdown" id="countdown">
                        <i class="fas fa-clock"></i>
                        <span id="timer">30:00</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h6>Detail Booking</h6>
                            <p><strong>ID:</strong> <span id="bookingId">{{ $booking['id_booking'] ?? 'N/A' }}</span></p>
                            <p><strong>Lapangan:</strong> <span id="lapanganNama">{{ $booking['lapangan']['nama_lapangan'] ?? 'N/A' }}</span></p>
                            <p><strong>Tanggal:</strong> <span id="bookingDate">{{ $booking['tanggal'] ?? 'N/A' }}</span></p>
                            <p><strong>Jam:</strong> <span id="bookingTime">{{ $booking['jam_mulai'] }} - {{ $booking['jam_selesai'] }}</span></p>
                            <p><strong>Status:</strong> 
                                <span class="badge {{ $statusClass ?? 'bg-warning' }}" id="statusBadge">
                                    {{ $statusText ?? 'Pending' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="total-display text-center">
                            <div class="h6 text-muted">Total Pembayaran</div>
                            <div class="amount" id="paymentTotal">Rp {{ number_format($booking['total_harga'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="qr-section mt-4">
                    <h6 class="text-center mb-3">Scan QR DANA</h6>
                    <div class="qr-container">
                        <img src="/images/qr-dana.png" alt="QR DANA Payment" class="qr-image" />
                    </div>
                </div>

                <div id="paymentForm" class="upload-section mt-4">
                    <h6>Upload Bukti Pembayaran</h6>
                    <div class="upload-box" id="uploadBox">
                        <i class="fas fa-cloud-upload-alt text-muted" style="font-size: 2.5rem; margin-bottom: 1rem;"></i>
                        <div>
                            <strong>Pilih atau drag gambar bukti transfer</strong>
                            <p class="text-muted small mb-0">JPG, PNG, PDF (max 2MB)</p>
                        </div>
                        <input type="file" id="proofFile" accept="image/*,.pdf" class="d-none">
                        <button class="btn payment-btn mt-3" id="uploadBtn">
                            <i class="fas fa-upload"></i> Upload & Bayar
                        </button>
                    </div>

                    <div id="uploadPreview" style="display: none;" class="mt-4 text-center">
                        <img id="previewImg" class="upload-preview mb-3" style="max-width: 200px; border-radius: 12px;">
                        <div class="payment-badge menunggu-verifikasi">
                            <i class="fas fa-clock"></i> Menunggu Verifikasi Admin
                        </div>
                    </div>
                </div>

                <div id="statusMessage" style="display: none;" class="mt-4 text-center">
                    <h5 id="messageTitle"></h5>
                    <p id="messageText"></p>
                </div>
            </div>
        </div>
    </div>
</div>

@vite('resources/js/payment.js')

@endsection
