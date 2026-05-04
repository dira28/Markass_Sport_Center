<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Booking {{ $fromDate }} - {{ $toDate }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #2c3e50; margin-bottom: 5px; }
        .header p { color: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .total { font-weight: bold; font-size: 14px; }
        .status-pending { color: orange; }
        .status-expired { color: red; }
        .status-paid { color: green; }
        .footer { margin-top: 50px; text-align: center; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Booking Markass Sport Center</h1>
        <p>Periode: {{ $fromDate }} s/d {{ $toDate }}</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Booking</th>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam Mulai - Selesai</th>
                <th>User ID</th>
                <th>Status Pembayaran</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
            <tr>
                <td>#{{ $booking['id_booking'] }}</td>
                <td>{{ $booking['nama_lapangan'] ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($booking['tanggal'])->translatedFormat('d F Y') }}</td>
                <td>{{ $booking['jam_mulai'] }} - {{ $booking['jam_selesai'] }}</td>
                <td>{{ $booking['id_user'] }}</td>
                <td class="status-{{ $booking['status_pembayaran'] }}">{{ ucfirst($booking['status_pembayaran']) }}</td>
                <td>Rp {{ number_format($booking['total_harga'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 50px;">Tidak ada data booking</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <table style="width: 50%; margin: 0 auto;">
            <tr>
                <td style="width: 70%; padding: 10px;">Total Revenue:</td>
                <td style="width: 30%; padding: 10px; font-weight: bold; font-size: 14px;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Markass Sport Center - {{ now()->year }}</p>
    </div>
</body>
</html>
