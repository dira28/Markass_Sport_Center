<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Booking</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h3 { margin: 0 0 6px; }
        .meta { margin-bottom: 12px; }
        .meta div { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        th { background: #f5f5f5; }
        .nowrap { white-space: nowrap; }
        .text-right { text-align: right; }
        .muted { color: #666; }
        .badge { display:inline-block; padding:2px 6px; border-radius: 4px; font-size: 11px; }
    </style>
</head>
<body>
    <h3>Laporan</h3>

    <div class="meta">
        <div><strong>Export Date:</strong> {{ $exportDate }}</div>
        <div>
            <strong>Periode:</strong>
            {{ $tanggalAwal ? $tanggalAwal : '-' }} s/d {{ $tanggalAkhir ? $tanggalAkhir : '-' }}
        </div>
        <div class="muted">
            <strong>Revenue:</strong> Rp {{ number_format($revenueToday, 0, ',', '.') }}
            &nbsp;|&nbsp;
            <strong>Total Booking:</strong> {{ $totalBooking }}
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="width: 45px;">No</th>
            <th style="width: 120px;">Tanggal</th>
            <th style="width: 90px;">ID</th>
            <th>Pengguna</th>
            <th>Lapangan</th>
            <th>Status</th>
            <th style="width: 140px;" class="text-right">Harga</th>
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
                    $badge = '#28a745';
                } elseif ($status == 'available') {
                    $statusText = 'Tersedia';
                    $badge = '#6c757d';
                } else {
                    $statusText = ucfirst($status);
                    $badge = '#343a40';
                }

                if ($pembayaran == 'pending') {
                    $statusText .= ' (Menunggu Bayar)';
                    $badge = '#ffc107';
                } elseif ($pembayaran == 'expired') {
                    $statusText = 'Expired';
                    $badge = '#dc3545';
                } elseif ($pembayaran == 'paid') {
                    $statusText .= ' (Lunas)';
                }

                $rawNumericId = $item['id'] ?? (isset($item['id_booking']) ? preg_replace('/\D/', '', (string)$item['id_booking']) : null);
                $displayBookingId = 'BK-' . str_pad((int) ($rawNumericId ?: 0), 6, '0', STR_PAD_LEFT);
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="nowrap">{{ $tanggal->translatedFormat('d M Y') }}</td>
                <td class="nowrap">{{ $displayBookingId }}</td>
                <td>{{ $item['user']['nama'] ?? '-' }}</td>
                <td>{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>
                <td>
                    <span class="badge" style="background: {{ $badge }}; color: #fff;">{{ $statusText }}</span>
                </td>
                <td class="text-right">Rp {{ number_format($item['total_harga'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="muted">Tidak ada data booking.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>

