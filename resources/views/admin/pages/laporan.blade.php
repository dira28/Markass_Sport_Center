@extends('layouts.admin')

@section('title', 'Laporan Booking')

@vite(['resources/css/laporan.css', 'resources/js/app.js'])

@section('content')

    @php
        $paidBooking = collect($bookings)
            ->filter(fn($b) => in_array(strtolower($b['status_pembayaran'] ?? ''), ['paid', 'confirmed', 'lunas']))
            ->count();

        $pendingBooking = collect($bookings)
            ->filter(fn($b) => in_array(strtolower($b['status_pembayaran'] ?? ''), ['pending', 'waiting_confirmation', 'menunggu_verifikasi']))
            ->count();
    @endphp

    <div class="report-page">

        {{-- PAGE HEADER --}}
        <div class="page-title-box">
            <div>
                <h3 class="page-title">Laporan Booking</h3>
                <small class="text-muted">Kelola dan tinjau seluruh riwayat transaksi booking fasilitas.</small>
            </div>
            <div>
                <a href="{{ route('admin.laporan.export', ['from_date' => $fromDate, 'to_date' => $toDate]) }}"
                    class="btn-export">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path
                            d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                        <path
                            d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- FILTER CARD --}}
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.laporan') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="filter-label">Dari Tanggal</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $fromDate) }}"
                            class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="filter-label">Sampai Tanggal</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $toDate) }}" class="form-control">
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn-filter w-50">Filter</button>
                        <a href="{{ route('admin.laporan') }}" class="btn-reset w-50">Reset</a>
                    </div>

                </div>
            </form>
        </div>

        {{-- KPI GRID --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Total Booking</div>
                <div class="kpi-number kpi-total">{{ $totalBooking }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-number kpi-revenue">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Status Paid</div>
                <div class="kpi-number kpi-paid">{{ $paidBooking }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Status Pending</div>
                <div class="kpi-number kpi-pending">{{ $pendingBooking }}</div>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="table-card">

            <div class="table-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">{{ $totalBooking }} Data Bookings</h5>
                <small class="text-muted">Periode: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} s/d
                    {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</small>
            </div>

            <div class="table-responsive-container">
                <table class="laporan-table">
                    <thead>
                        <tr>
                            <th style="width: 120px; min-width: 120px;">ID</th>
                            <th style="width: 250px; min-width: 250px;">Lapangan</th>
                            <th style="width: 160px; min-width: 160px;">Tgl Dibuat</th>
                            <th style="width: 200px; min-width: 200px;">Jadwal Main</th>
                            <th style="width: 120px; min-width: 120px;">Durasi</th>
                            <th style="width: 180px; min-width: 180px;">User</th>
                            <th style="width: 150px; min-width: 150px;">Status</th>
                            <th style="width: 160px; min-width: 160px;" class="text-end">Total</th>
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
                                <td><strong>#{{ strtoupper(substr($booking['id_booking'] ?? $booking['id'] ?? '-', 0, 6)) }}</strong>
                                </td>
                                <td><strong>{{ $booking['lapangan']['nama_lapangan'] ?? '-' }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($booking['created_at'])->format('d M Y') }}</td>
                                <td>
                                    <div><strong>{{ \Carbon\Carbon::parse($booking['tanggal'])->format('d M Y') }}</strong>
                                    </div>
                                    <small class="text-muted">{{ substr($booking['jam_mulai'], 0, 5) }} -
                                        {{ substr($booking['jam_selesai'], 0, 5) }}</small>
                                </td>
                                <td><span class="badge-durasi">{{ $durasi }} Jam</span></td>
                                <td>{{ $booking['nama_user'] ?? $booking['user']['nama'] ?? 'User' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $st }}">
                                        {{ ucfirst($st) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong>Rp{{ number_format($booking['total_harga'] ?? 0, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    Tidak ada data booking pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER / PAGINATION LINK --}}
            @if(method_exists($bookings, 'hasPages') && $bookings->hasPages())
                <div class="table-footer-pagination p-3 border-top d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $bookings->firstItem() }} - {{ $bookings->lastItem() }} dari {{ $bookings->total() }}
                        data
                    </div>
                    <div>
                        {{ $bookings->links() }}
                    </div>
                </div>
            @endif

        </div>

    </div>

@endsection