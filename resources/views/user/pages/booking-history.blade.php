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


                        $paymentStatus = strtolower(
                            $item['status_pembayaran'] ?? 'pending'
                        );

                        // NORMALIZE DATABASE STATUS
                        if ($paymentStatus === 'paid') {
                            $paymentStatus = 'confirmed';
                        }

                        if ($paymentStatus === 'menunggu_verifikasi') {
                            $paymentStatus = 'waiting_confirmation';
                        }


                        $now = \Carbon\Carbon::now('Asia/Jakarta');

                        $bookingEnd = $tanggal
                            ->copy()
                            ->setTimeFromTimeString($jamMulai);

                        if (
                            $paymentStatus === 'pending' &&
                            $now->gt($bookingEnd)
                        ) {
                            $paymentStatus = 'expired';
                        }

                        $statusMap = [
                            'pending' => [
                                'Menunggu Bayar',
                                'badge bg-warning text-dark'
                            ],

                            'waiting_confirmation' => [
                                'Menunggu Verifikasi',
                                'badge bg-primary'
                            ],

                            'confirmed' => [
                                'Sudah Terbayar',
                                'badge bg-success'
                            ],

                            'expired' => [
                                'Kadaluarsa',
                                'badge bg-danger'
                            ],

                            'cancelled' => [
                                'Dibatalkan',
                                'badge bg-secondary'
                            ],
                        ];

                        [$statusText, $badge] =
                            $statusMap[$paymentStatus]
                            ?? ['Unknown', 'badge bg-dark'];

                    @endphp

                    <tr>

                        <td>
                            {{ $tanggal->translatedFormat('d M Y') }}
                        </td>

                        <td>
                            {{ $item['lapangan']['nama_lapangan'] ?? '-' }}
                        </td>

                        <td>
                            {{ $item['jam_mulai'] }}
                            -
                            {{ $item['jam_selesai'] }}
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

                            {{-- PENDING --}}
                            @if($paymentStatus === 'pending')

                                <a href="/booking/payment/{{ $item['id_booking'] }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-credit-card"></i>
                                    Bayar Sekarang
                                </a>

                                {{-- WAITING CONFIRMATION --}}
                            @elseif($paymentStatus === 'waiting_confirmation')

                                <button class="btn btn-primary btn-sm" disabled>
                                    Menunggu Verifikasi
                                </button>

                                {{-- CONFIRMED / PAID --}}
                            @elseif($paymentStatus === 'confirmed')

                                <button class="btn btn-success btn-sm" disabled>
                                    Sudah Terbayar
                                </button>

                                {{-- EXPIRED --}}
                            @elseif($paymentStatus === 'expired')

                                <button class="btn btn-danger btn-sm" disabled>
                                    Kadaluarsa
                                </button>

                                {{-- CANCELLED --}}
                            @elseif($paymentStatus === 'cancelled')

                                <button class="btn btn-secondary btn-sm" disabled>
                                    Dibatalkan
                                </button>

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
    </div>

@endsection