@extends('layouts.admin')

@section('title', 'Laporan')


@vite(['resources/js/app.js'])
@section('content')

    @php
        $statusMap = [
            'paid' => 'Paid',
            'confirmed' => 'Paid',
            'pending' => 'Pending',
            'waiting_confirmation' => 'Pending',
            'menunggu_verifikasi' => 'Pending',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
        ];

        $paidBooking = collect($bookings)
            ->whereIn('status_pembayaran', ['paid', 'confirmed'])
            ->count();

        $pendingBooking = collect($bookings)
            ->whereIn('status_pembayaran', ['pending', 'waiting_confirmation', 'menunggu_verifikasi'])
            ->count();
    @endphp

    <div class="container-fluid report-page">

        {{-- TITLE --}}
        <div class="page-title-box">
            <h4 class="page-title">Laporan Booking</h4>
        </div>

        {{-- FILTER --}}
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.laporan') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="filter-label">Dari</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="filter-label">Sampai</label>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
                    </div>

                    {{-- BUTTON FILTER + RESET --}}
                    <div class="col-md-4 d-flex gap-2">

                        <button type="submit" class="btn-filter w-50">
                            Filter
                        </button>

                        <a href="{{ route('admin.laporan') }}" class="btn-reset w-50">
                            Reset
                        </a>

                    </div>

                </div>

                <p class="filter-info mt-3 mb-0">
                    {{ $totalBooking }} booking dari {{ $fromDate }} - {{ $toDate }}
                </p>
            </form>
        </div>

        {{-- KPI --}}
        <div class="kpi-grid">

            <div class="kpi-card booking">
                <div class="kpi-label">Total Booking</div>
                <div class="kpi-number">{{ $totalBooking }}</div>
            </div>

            <div class="kpi-card revenue">
                <div class="kpi-label">Revenue</div>
                <div class="kpi-number">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Paid</div>
                <div class="kpi-number">{{ $paidBooking }}</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Pending</div>
                <div class="kpi-number">{{ $pendingBooking }}</div>
            </div>

        </div>

        {{-- EXPORT --}}
        <div class="export">
            <a href="{{ route('admin.laporan.export', ['from_date' => $fromDate, 'to_date' => $toDate]) }}"
                class="btn-export">
                Export PDF
            </a>
        </div>

        {{-- TABLE --}}
        <div class="table-card">

            <div class="table-header">
                <h5>{{ $totalBooking }} Bookings</h5>
                <small>{{ $fromDate }} - {{ $toDate }}</small>
            </div>

            <div class="table-responsive">

                <table class="table laporan-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Lapangan</th>
                            <th>Tanggal</th>
                            <th>Jadwal</th>
                            <th>Durasi</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($bookings as $booking)

                            @php
                                $st = strtolower($booking['status_pembayaran'] ?? 'pending');

                                $start = \Carbon\Carbon::parse($booking['jam_mulai']);
                                $end = \Carbon\Carbon::parse($booking['jam_selesai']);
                                $durasi = $start->diffInHours($end);
                            @endphp

                            <tr>

                                {{-- ID --}}
                                <td>
                                    <strong>#{{ strtoupper(substr($booking['id_booking'], 0, 6)) }}</strong>
                                </td>

                                {{-- LAPANGAN --}}
                                <td>
                                    {{ $booking['lapangan']['nama_lapangan'] ?? '-' }}
                                </td>

                                {{-- TANGGAL BOOKING (CREATED_AT) --}}
                                <td>
                                    <div style="font-weight:600;">
                                        {{ \Carbon\Carbon::parse($booking['created_at'])->format('d M Y') }}
                                    </div>
                                    <small style="color:#6b7280;">Booking dibuat</small>
                                </td>

                                {{-- JADWAL MAIN (TANGGAL + JAM) --}}
                                <td>
                                    <div style="font-weight:600;">
                                        {{ \Carbon\Carbon::parse($booking['tanggal'])->format('d M Y') }}
                                    </div>
                                    <small style="color:#6b7280;">
                                        {{ substr($booking['jam_mulai'], 0, 5) }} - {{ substr($booking['jam_selesai'], 0, 5) }}
                                    </small>
                                </td>

                                {{-- DURASI --}}
                                <td>
                                    <span class="badge-duration">{{ $durasi }} Jam</span>
                                </td>

                                {{-- USER --}}
                                <td class="text-center">
                                    {{ $booking['nama_user'] ?? 'User' }}
                                </td>

                                {{-- STATUS --}}
                                <td class="text-center">
                                    <span class="status-badge status-{{ $st }}">
                                        {{ ucfirst($st) }}
                                    </span>
                                </td>

                                {{-- TOTAL --}}
                                <td class="text-end">
                                    Rp{{ number_format($booking['total_harga'] ?? 0, 0, ',', '.') }}
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="empty">No bookings found</td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection