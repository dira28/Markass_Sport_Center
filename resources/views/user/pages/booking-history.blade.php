@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <h4>Riwayat Booking</h4>

    @if(isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Lapangan</th>
                <th>Jam</th>
                <th>Status</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($bookings as $item)

                @php
                    $tanggal = \Carbon\Carbon::parse($item['tanggal']);
                    $jamMulai = $item['jam_mulai'] ?? '00:00:00';
                    
                    $status = $item['status'] ?? 'pending';
                    
                    // Check if expired by date/time
                    $paymentStatus = $item['status_pembayaran'] ?? $status;

                    $now = \Carbon\Carbon::now('Asia/Jakarta');
                    $bookingEnd = $tanggal->copy()->setTimeFromTimeString($jamMulai);
                    if ($status !== 'cancelled' && $now->gt($bookingEnd)) {
                        $status = 'expired';
                    }
                    
                    $statusMap = [
                        'pending' => ['Menunggu Bayar', 'payment-badge pending'],
                        'waiting_confirmation' => ['Menunggu Verifikasi Admin', 'payment-badge menunggu-verifikasi'],
                        'confirmed' => ['Konfirmasi Berhasil', 'payment-badge confirmed'],
                        'expired' => ['Kadaluarsa', 'payment-badge expired'],
                        'cancelled' => ['Dibatalkan', 'payment-badge cancelled'],
                    ];
                    $default = ['Unknown', 'bg-light text-dark'];
                    
                    [$statusText, $badge] = $statusMap[$paymentStatus] ?? $default;
                @endphp

                <tr>
                    <td>
                        {{ $tanggal->translatedFormat('d M Y') }}
                    </td>

                    <td>
                        {{ $item['lapangan']['nama_lapangan'] ?? '-' }}
                    </td>

                    <td>
                        {{ $item['jam_mulai'] }} - {{ $item['jam_selesai'] }}
                    </td>

                    <td>
                        <span class="{{ $badge }}">
                            {{ $statusText }}
                        </span>
                    </td>

                    <td>
                        Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                    </td>
                    <td>
                        @if($paymentStatus === 'pending')
                            <a href="/booking/payment/{{ $item['id_booking'] }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-credit-card"></i> Bayar Sekarang
                            </a>
                        @elseif($paymentStatus === 'waiting_confirmation')
                            Menunggu Verifikasi Admin

                            @if(isset($item['bukti_path']))
                                <br><small><a href="{{ $item['bukti_path'] }}" target="_blank">Lihat Bukti</a></small>
                            @endif
                        @elseif($paymentStatus === 'confirmed')
                            <span class="badge bg-success">Lunas</span>
                        @elseif($paymentStatus === 'expired')
                            <span class="badge bg-danger">Expired</span>
                        @elseif($paymentStatus === 'cancelled')
                            <span class="badge bg-dark text-white">Dibatalkan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        Belum ada booking
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection