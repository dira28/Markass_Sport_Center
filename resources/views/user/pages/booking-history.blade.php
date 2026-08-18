@extends('layouts.app')

@section('content')

    <style>
        .booking-history-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #f0f0f0;
            padding: 28px;
            margin-bottom: 40px;
        }

        .page-title {
            font-weight: 700;
            font-size: 22px;
            color: #111827;
            margin-bottom: 24px;
        }

        .custom-table {
            vertical-align: middle;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0 6px;
        }

        /* HEADER TABEL HITAM & FORCE RATA TENGAH */
        .custom-table thead th {
            background-color: transparent !important;
            color: #000000 !important;
            font-weight: 700 !important;
            font-size: 14px;
            border: none !important;
            padding: 12px 20px;
            text-align: center !important;
        }

        /* ISI TABEL RATA TENGAH */
        .custom-table tbody td {
            padding: 16px 20px;
            color: #111827;
            font-size: 14px;
            border: none !important;
            text-align: center !important;
        }

        /* Warna Selang-Seling (Zebra Striping) */
        .custom-table tbody tr:nth-child(even) td {
            background-color: #f9fafb !important;
        }

        .custom-table tbody tr:nth-child(odd) td {
            background-color: #ffffff !important;
        }

        /* Efek Hover Baris */
        .custom-table tbody tr:hover td {
            background-color: #f3f4f6 !important;
            transition: background-color 0.2s ease;
        }

        /* Melengkungkan sudut baris data */
        .custom-table tbody tr td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .custom-table tbody tr td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Badge Status */
        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-waiting {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .status-confirmed {
            background-color: #d1fae5;
            color: #059669;
        }

        .status-expired {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .status-cancelled {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .btn-action {
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            padding: 7px 16px;
        }
    </style>

    <div class="container mt-4">
        <div class="booking-history-card">
            <h4 class="page-title">Riwayat Booking</h4>

            @if(isset($error))
                <div class="alert alert-danger border-0 rounded-3 mb-4">{{ $error }}</div>
            @endif

            <div class="table-responsive">
                <table class="table custom-table align-middle text-center">
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Lapangan</th>
                            <th class="text-center">Jam</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($bookings as $item)
                            @php
                                $tanggal = \Carbon\Carbon::parse($item['tanggal']);
                                $jamMulai = $item['jam_mulai'] ?? '00:00:00';

                                $paymentStatus = strtolower($item['status_pembayaran'] ?? 'pending');

                                if ($paymentStatus === 'paid') {
                                    $paymentStatus = 'confirmed';
                                }
                                if ($paymentStatus === 'menunggu_verifikasi') {
                                    $paymentStatus = 'waiting_confirmation';
                                }

                                $now = \Carbon\Carbon::now('Asia/Jakarta');
                                $bookingEnd = $tanggal->copy()->setTimeFromTimeString($jamMulai);

                                if ($paymentStatus === 'pending' && $now->gt($bookingEnd)) {
                                    $paymentStatus = 'expired';
                                }

                                $statusMap = [
                                    'pending' => ['Menunggu Bayar', 'badge-status status-pending'],
                                    'waiting_confirmation' => ['Menunggu Verifikasi', 'badge-status status-waiting'],
                                    'confirmed' => ['Sudah Terbayar', 'badge-status status-confirmed'],
                                    'expired' => ['Kadaluarsa', 'badge-status status-expired'],
                                    'cancelled' => ['Dibatalkan', 'badge-status status-cancelled'],
                                ];

                                [$statusText, $badge] = $statusMap[$paymentStatus] ?? ['Unknown', 'badge-status status-cancelled'];
                            @endphp

                            <tr>
                                <td class="fw-semibold text-center">{{ $tanggal->translatedFormat('d M Y') }}</td>
                                <td class="text-center">{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>
                                <td class="text-center">{{ $item['jam_mulai'] }} - {{ $item['jam_selesai'] }}</td>
                                <td class="text-center"><span class="{{ $badge }}">{{ $statusText }}</span></td>
                                <td class="fw-bold text-dark text-center">Rp
                                    {{ number_format($item['total_harga'], 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($paymentStatus === 'pending')
                                        <a href="/booking/payment/{{ $item['id_booking'] }}"
                                            class="btn btn-warning btn-action shadow-sm text-dark">
                                            <i class="fas fa-credit-card me-1"></i> Bayar Sekarang
                                        </a>
                                    @elseif($paymentStatus === 'waiting_confirmation')
                                        <button class="btn btn-light btn-action text-primary border-0" disabled>Menunggu
                                            Verifikasi</button>
                                    @elseif($paymentStatus === 'confirmed')
                                        <button class="btn btn-light btn-action text-success border-0" disabled>Sudah
                                            Terbayar</button>
                                    @elseif($paymentStatus === 'expired')
                                        <button class="btn btn-light btn-action text-danger border-0" disabled>Kadaluarsa</button>
                                    @elseif($paymentStatus === 'cancelled')
                                        <button class="btn btn-light btn-action text-secondary border-0"
                                            disabled>Dibatalkan</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Belum ada riwayat booking
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION UI CONTAINER -->
            @if($bookings instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div class="text-muted small">
                        Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of
                        {{ $bookings->total() }} entries
                    </div>
                    <div>
                        {{ $bookings->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection