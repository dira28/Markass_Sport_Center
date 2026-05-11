@extends('layouts.admin')

@section('content')

    <div class="booking-header">
        <div>
            <h4>Manajemen Booking</h4>
            <small>Admin / Booking</small>
        </div>
    </div>

    <div class="card-box mt-4">

        <h5 class="mb-3">Data Booking</h5>

        {{-- ERROR ALERT --}}
        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

{{-- Proof modal (View Proof) --}}
@include('admin.components.proof-modal')

<table class="table align-middle admin-booking-table payment-admin-table">
            <thead>
                <tr>

                    <th>Tanggal</th>
                    <th>ID</th>
                    <th>Pengguna</th>
                    <th>Lapangan</th>
                    <th>Jam</th>
                    <th>Status Pembayaran</th>
                    <th>Harga</th>
                    <th>Bukti Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>


            <tbody>
                @forelse ($bookings as $item)

                    @php
                        $tanggal = \Carbon\Carbon::parse($item['tanggal'])->timezone('Asia/Jakarta');

                        // ===== Normalize payment status to ONLY these values =====
                        // pending | waiting_confirmation | confirmed | expired | cancelled
                        $rawPaymentStatus = $item['status_pembayaran'] ?? $item['status'] ?? 'pending';
                        $paymentStatus = $rawPaymentStatus;

                        // Safe normalization (old API tokens)
                        if ($paymentStatus === 'menunggu_verifikasi') {
                            $paymentStatus = 'waiting_confirmation';
                        }

                        // If backend still sends other legacy Indonesian tokens, map them safely.
                        if (is_string($paymentStatus)) {
                            $paymentStatus = strtolower(trim($paymentStatus));
                            if ($paymentStatus === 'menunggu_verifikasi') $paymentStatus = 'waiting_confirmation';
                        }


                        // Normalize any accidental legacy values
                        if (isset($paymentStatus) && is_string($paymentStatus)) {
                            $paymentStatus = strtolower(trim($paymentStatus));
                            if ($paymentStatus === 'menunggu_verifikasi') $paymentStatus = 'waiting_confirmation';
                        }


                        // Defensive: normalize to allowed set only
                        if (!in_array($paymentStatus, ['pending','waiting_confirmation','confirmed','expired','cancelled'], true)) {
                            $paymentStatus = 'pending';
                        }


                        $statusMap = [
                            'pending' => ['Pending', 'warning'],
                            'waiting_confirmation' => ['Waiting confirmation', 'primary'],
                            'confirmed' => ['Confirmed', 'success'],
                            'expired' => ['Expired', 'secondary'],
                            'cancelled' => ['Cancelled', 'dark'],
                        ];

                        $statusText = $statusMap[$paymentStatus][0] ?? ucfirst(str_replace('_', ' ', $paymentStatus));
                        $badge = $statusMap[$paymentStatus][1] ?? 'secondary';

                    @endphp

                    <tr>
                        <td>
                            {{ $tanggal->translatedFormat('d M Y') }}
                        </td>

                        <td>
                            #{{ strtoupper(substr($item['id_booking'], 0, 6)) }}
                        </td>

                        <td>{{ $item['user']['nama'] ?? '-' }}</td>

                        <td>{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>

                        {{-- JAM --}}
                        <td>
                            {{ $item['jam_mulai'] }} - {{ $item['jam_selesai'] }}
                        </td>

                        <td>
                            <span class="badge bg-{{ $badge }}">
                                {{ $statusText }}
                            </span>
                        </td>

                        {{-- HARGA --}}
                        <td>
                            Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                        </td>

                        {{-- BUKTI --}}
                        <td>
                            @if(isset($item['bukti_path']))
                                <img id="bookingProofThumb-{{ $item['id_booking'] }}" class="d-none" data-proof-url="{{ $item['bukti_path'] }}" alt="Proof" />
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="openProofModal('{{ $item['id_booking'] }}')">
                                    <i class="fas fa-eye"></i> View Proof
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td>
                            @if($paymentStatus === 'waiting_confirmation')
                                <form method="POST" action="/booking/{{ $item['id_booking'] }}/confirm-payment" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve Payment?')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Tidak ada data booking
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

@endsection