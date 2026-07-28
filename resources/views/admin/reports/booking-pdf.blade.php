<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Booking</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .header p {
            margin: 4px 0 0 0;
            color: #64748b;
            font-size: 12px;
        }

        /* KPI TABLE FOR DOMPDF */
        .kpi-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .kpi-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 10px;
            text-align: center;
            border-radius: 6px;
            width: 25%;
        }

        .kpi-title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }

        .kpi-value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 4px;
            color: #0f172a;
        }

        /* DATA TABLE */
        .pdf-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .pdf-table th {
            background: #1e293b;
            color: #ffffff;
            padding: 10px 12px;
            font-size: 11px;
            text-align: left;
            text-transform: uppercase;
        }

        .pdf-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* BADGES */
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
            display: inline-block;
        }

        .badge-paid {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-pending {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-expired {
            background: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>

<body>

    @php
        $paidCount = collect($bookings)->filter(fn($b) => in_array(strtolower($b['status_pembayaran'] ?? ''), ['paid', 'confirmed', 'lunas']))->count();
        $pendingCount = collect($bookings)->filter(fn($b) => in_array(strtolower($b['status_pembayaran'] ?? ''), ['pending', 'waiting_confirmation', 'menunggu_verifikasi']))->count();
    @endphp

    <div class="header">
        <h2>Laporan Booking Fasilitas</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} s/d
            {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
    </div>

    {{-- KPI GRID --}}
    <table class="kpi-table">
        <tr>
            <td class="kpi-box">
                <div class="kpi-title">Total Booking</div>
                <div class="kpi-value">{{ $totalBooking }}</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-title">Total Revenue</div>
                <div class="kpi-value" style="color: #2563eb;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-title">Status Paid</div>
                <div class="kpi-value" style="color: #16a34a;">{{ $paidCount }}</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-title">Status Pending</div>
                <div class="kpi-value" style="color: #d97706;">{{ $pendingCount }}</div>
            </td>
        </tr>
    </table>

    {{-- DATA TABLE --}}
    <table class="pdf-table">
        <thead>
            <tr>
                <th width="12%">ID</th>
                <th width="20%">LAPANGAN</th>
                <th width="14%">TGL DIBUAT</th>
                <th width="22%">JADWAL MAIN</th>
                <th width="12%">USER</th>
                <th width="10%" class="text-center">STATUS</th>
                <th width="10%" class="text-center">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $b)
                @php
                    $st = strtolower($b['status_pembayaran'] ?? 'pending');
                    $badgeClass = match (true) {
                        in_array($st, ['paid', 'confirmed', 'lunas']) => 'badge-paid',
                        in_array($st, ['pending', 'waiting_confirmation', 'menunggu_verifikasi']) => 'badge-pending',
                        default => 'badge-expired'
                    };
                @endphp
                <tr>
                    <td><strong>#{{ strtoupper(substr($b['id_booking'] ?? $b['id'] ?? '-', 0, 6)) }}</strong></td>
                    <td>{{ $b['lapangan']['nama_lapangan'] ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($b['created_at'])->format('d M Y') }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($b['tanggal'])->format('d M Y') }}<br>
                        <small style="color: #666;">({{ substr($b['jam_mulai'], 0, 5) }} -
                            {{ substr($b['jam_selesai'], 0, 5) }})</small>
                    </td>
                    <td>{{ $b['nama_user'] ?? 'User' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">
                            {{ ucfirst($st) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <strong>Rp {{ number_format($b['total_harga'] ?? 0, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data booking pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>