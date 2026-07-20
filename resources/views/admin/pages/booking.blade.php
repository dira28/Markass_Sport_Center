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

    <div class="table-responsive-admin">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>ID</th>
                    <th>Pengguna</th>
                    <th>Lapangan</th>
                    <th>Status</th>
                    <th>Harga</th>
                </tr>
            </thead>

            <tbody>

@forelse ($bookings as $item)

    @php
        $tanggal = \Carbon\Carbon::parse($item['tanggal'])->timezone('Asia/Jakarta');

        $status = $item['status'] ?? '-';
        $pembayaran = $item['status_pembayaran'] ?? '-';

        if ($status == 'booked') {
            $statusText = 'Berlangsung';
            $badge = 'success';
        } elseif ($status == 'available') {
            $statusText = 'Tersedia';
            $badge = 'secondary';
        } else {
            $statusText = ucfirst($status);
            $badge = 'dark';
        }

        if ($pembayaran == 'pending') {
            $statusText .= ' (Menunggu Bayar)';
            $badge = 'warning';
        } elseif ($pembayaran == 'expired') {
            $statusText = 'Expired';
            $badge = 'danger';
        } elseif ($pembayaran == 'paid') {
            $statusText .= ' (Lunas)';
        }
    @endphp

    <tr>
        <td>
            {{ $tanggal->translatedFormat('d M Y') }} <br>
        </td>

        @php
            $rawNumericId = $item['id'] ?? (isset($item['id_booking']) ? preg_replace('/\D/', '', (string)$item['id_booking']) : null);
            $displayBookingId = 'BK-' . str_pad((int) ($rawNumericId ?: 0), 6, '0', STR_PAD_LEFT);
        @endphp
        <td>{{ $displayBookingId }}</td>

        <td>{{ $item['user']['nama'] ?? '-' }}</td>

        <td>{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>

        <td>
            <span class="badge bg-{{ $badge }}">
                {{ $statusText }}
            </span>
        </td>

        <td>
            Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
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

</div>

@endsection