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
                <th>ID</th>
                <th>Tanggal</th>
                <th>Lapangan</th>
                <th>Jam</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($bookings as $item)

                @php
                    $tanggal = \Carbon\Carbon::parse($item['tanggal']);

                    $status = $item['status_pembayaran'];

                    if ($status == 'confirmed') {
                        $statusText = 'Lunas';
                        $badge = 'success';
                    } elseif ($status == 'pending') {
                        $statusText = 'Menunggu';
                        $badge = 'warning';
                    } elseif ($status == 'expired') {
                        $statusText = 'Expired';
                        $badge = 'danger';
                    } else {
                        $statusText = ucfirst($status);
                        $badge = 'secondary';
                    }

                    // Booking ID display:
                    // - Prefer numeric DB PK: $item['id'] if API includes it
                    // - Otherwise, fallback using id_booking by stripping non-digits
                    $rawNumericId = $item['id'] ?? (isset($item['id_booking']) ? preg_replace('/\D/', '', (string) $item['id_booking']) : null);
                    $displayBookingId = 'BK-' . str_pad((int) ($rawNumericId ?: 0), 6, '0', STR_PAD_LEFT);
                @endphp

                <tr>
                    <td>
                        {{ $displayBookingId }}
                    </td>

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
                        Belum ada booking
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

