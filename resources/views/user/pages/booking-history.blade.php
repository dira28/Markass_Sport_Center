@extends('layouts.app')

@section('content')

    <style>
        /* Card Container Utama */
        .booking-history-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #eef2f6;
            padding: 32px;
            margin-bottom: 40px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .page-title {
            font-weight: 800;
            font-size: 24px;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.5px;
        }

        /* Tabel Modern Custom */
        .custom-table {
            vertical-align: middle;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        /* HEADER TABEL */
        .custom-table thead th {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            font-weight: 700 !important;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border: none !important;
            padding: 14px 18px;
            text-align: center !important;
        }

        .custom-table thead tr th:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .custom-table thead tr th:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        /* ISI TABEL */
        .custom-table tbody td {
            padding: 16px 18px;
            color: #1e293b;
            font-size: 14px;
            border: none !important;
            text-align: center !important;
        }

        /* Warna Selang-Seling (Zebra Striping) */
        .custom-table tbody tr:nth-child(odd) td {
            background-color: #ffffff !important;
        }

        .custom-table tbody tr:nth-child(even) td {
            background-color: #f8fafc !important;
        }

        /* Hover Effect */
        .custom-table tbody tr {
            transition: all 0.2s ease;
        }

        .custom-table tbody tr:hover td {
            background-color: #f1f5f9 !important;
            transform: translateY(-1px);
        }

        /* Radius Baris Data */
        .custom-table tbody tr td:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .custom-table tbody tr td:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        /* Badge Status Tegas & Modern */
        .badge-status {
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .status-pending {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .status-waiting {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .status-confirmed {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .status-expired {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .status-cancelled {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Tombol Aksi Jelas & Kontras High-Impact */
        .btn-action-primary {
            background: linear-gradient(135deg, #ff9800 0%, #ed6c02 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            padding: 9px 20px;
            box-shadow: 0 4px 12px rgba(237, 108, 2, 0.3);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(237, 108, 2, 0.4);
            color: #ffffff;
        }

        /* Label Status Non-Aksi (Jelas, Bukan Tombol Soft/Blur) */
        .state-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            font-size: 13px;
            padding: 8px 16px;
            border-radius: 10px;
        }

        .state-confirmed {
            background-color: #10b981;
            color: #ffffff;
        }

        .state-waiting {
            background-color: #0284c7;
            color: #ffffff;
        }

        .state-expired {
            background-color: #ef4444;
            color: #ffffff;
        }

        .state-cancelled {
            background-color: #64748b;
            color: #ffffff;
        }

        /* Badge Lapangan */
        .field-badge {
            font-weight: 700;
            color: #0f172a;
            background: #e2e8f0;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-block;
        }
    </style>

    <div class="container mt-4">
        <div class="booking-history-card">
            <div class="page-header">
                <h4 class="page-title"><i class="fas fa-history me-2 text-primary"></i>Riwayat Booking</h4>
            </div>

            @if(isset($error))
                <div class="alert alert-danger border-0 rounded-3 mb-4 shadow-sm">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ $error }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table custom-table align-middle text-center">
                    <thead>
                        <tr>
                            <th class="text-center">Tgl Transaksi</th>
                            <th class="text-center">Lapangan</th>
                            <th class="text-center">Jadwal Main</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Aksi / Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($bookings as $item)
                            @php
                                // 1. Tanggal Transaksi
                                $createdDate = \Carbon\Carbon::parse($item['created_at'] ?? $item['created_date'] ?? $item['tanggal'])->timezone('Asia/Jakarta');

                                // 2. Jadwal Main
                                $tanggalMain = \Carbon\Carbon::parse($item['tanggal'])->timezone('Asia/Jakarta');
                                $jamMulai = $item['jam_mulai'] ?? '00:00:00';

                                $paymentStatus = strtolower($item['status_pembayaran'] ?? 'pending');

                                if (in_array($paymentStatus, ['paid', 'approve', 'approved'], true)) {
                                    $paymentStatus = 'confirmed';
                                }
                                if ($paymentStatus === 'menunggu_verifikasi') {
                                    $paymentStatus = 'waiting_confirmation';
                                }

                                $now = \Carbon\Carbon::now('Asia/Jakarta');
                                $bookingEnd = $tanggalMain->copy()->setTimeFromTimeString($jamMulai);

                                if ($paymentStatus === 'pending' && $now->gt($bookingEnd)) {
                                    $paymentStatus = 'expired';
                                }

                                $statusMap = [
                                    'pending' => ['Menunggu Bayar', 'badge-status status-pending', 'fa-clock'],
                                    'waiting_confirmation' => ['Menunggu Verifikasi', 'badge-status status-waiting', 'fa-hourglass-half'],
                                    'confirmed' => ['Sudah Terbayar', 'badge-status status-confirmed', 'fa-check-circle'],
                                    'expired' => ['Kadaluarsa', 'badge-status status-expired', 'fa-times-circle'],
                                    'cancelled' => ['Dibatalkan', 'badge-status status-cancelled', 'fa-ban'],
                                ];

                                [$statusText, $badge, $icon] = $statusMap[$paymentStatus] ?? ['Unknown', 'badge-status status-cancelled', 'fa-question-circle'];
                            @endphp

                            <tr>
                                <!-- Tanggal Transaksi -->
                                <td class="text-center">
                                    <div class="fw-bold text-dark">{{ $createdDate->translatedFormat('d M Y') }}</div>
                                    <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $createdDate->format('H:i') }}
                                        WIB</small>
                                </td>

                                <!-- Nama Lapangan -->
                                <td class="text-center">
                                    <span class="field-badge">
                                        <i class="fas fa-futbol me-1 text-primary"></i>
                                        {{ $item['lapangan']['nama_lapangan'] ?? '-' }}
                                    </span>
                                </td>

                                <!-- Tanggal & Jam Main -->
                                <td class="text-center">
                                    <div class="fw-bold text-dark">{{ $tanggalMain->translatedFormat('d M Y') }}</div>
                                    <span class="badge bg-light text-dark border mt-1">
                                        {{ $item['jam_mulai'] }} - {{ $item['jam_selesai'] }}
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td class="text-center">
                                    <span class="{{ $badge }}">
                                        <i class="fas {{ $icon }}"></i> {{ $statusText }}
                                    </span>
                                </td>

                                <!-- Total Harga -->
                                <td class="text-center">
                                    <div class="fw-extrabold text-dark fs-6">
                                        Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                                    </div>
                                </td>

                                <!-- Aksi Jelas & Nyata -->
                                <td class="text-center">
                                    @if($paymentStatus === 'pending')
                                        <a href="/booking/payment/{{ $item['id_booking'] }}" class="btn-action-primary">
                                            <i class="fas fa-wallet"></i> Bayar Sekarang
                                        </a>
                                    @elseif($paymentStatus === 'waiting_confirmation')
                                        <span class="state-label state-waiting">
                                            <i class="fas fa-hourglass-half"></i> Memproses
                                        </span>
                                    @elseif($paymentStatus === 'confirmed')
                                        <span class="state-label state-confirmed">
                                            <i class="fas fa-check-circle"></i> Selesai
                                        </span>
                                    @elseif($paymentStatus === 'expired')
                                        <span class="state-label state-expired">
                                            <i class="fas fa-exclamation-triangle"></i> Expired
                                        </span>
                                    @elseif($paymentStatus === 'cancelled')
                                        <span class="state-label state-cancelled">
                                            <i class="fas fa-ban"></i> Batal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-0 fw-bold">Belum ada riwayat booking</p>
                                        <small>Pesanan atau booking Anda akan muncul di sini.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION CONTAINER -->
            @if($bookings instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div class="text-muted small">
                        Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of
                        <span class="fw-bold">{{ $bookings->total() }}</span> entries
                    </div>
                    <div>
                        {{ $bookings->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection