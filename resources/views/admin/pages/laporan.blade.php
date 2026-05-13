@extends('layouts.admin')

@section('title', 'Laporan')

@push('styles')
@vite(['resources/css/admin/pages/report.css', 'resources/css/admin/pages/laporan.css'])

@endpush

@section('content')
<div class="container-fluid report-page">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan</li>
                    </ol>
                </div>
                <h4 class="page-title">Laporan Booking</h4>
            </div>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="filter-card mt-4">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label filter-label">Dari Tanggal</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label filter-label">Sampai Tanggal</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
                </div>

                <div class="col-md-4 d-grid">
                    <button type="submit" class="btn btn-export btn-lg">
                        <i class="fas fa-filter"></i>
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            <div class="mt-3">
                <h5 class="text-muted mb-0">Showing {{ $totalBooking }} bookings from {{ $fromDate }} to {{ $toDate }}</h5>
            </div>
        </form>
    </div>

    {{-- KPI CARDS --}}
    <div class="kpi-grid row mt-4 g-4">
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card kpi-booking h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#dbeafe; color:#1d4ed8;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Total Booking</div>
                            <div class="kpi-number">{{ number_format($totalBooking, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card kpi-revenue h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#d1fae5; color:#065f46;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Total Revenue</div>
                            <div class="kpi-number">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $paidBooking = collect($bookings)->filter(function($b){
                return ($b['status_pembayaran'] ?? '') === 'paid' || ($b['status_pembayaran'] ?? '') === 'confirmed';
            })->count();
            $pendingBooking = collect($bookings)->filter(function($b){
                return ($b['status_pembayaran'] ?? '') === 'pending' || ($b['status_pembayaran'] ?? '') === 'waiting_confirmation' || ($b['status_pembayaran'] ?? '') === 'menunggu_verifikasi';
            })->count();
        @endphp

        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card h-100" style="border-left-color:#10b981;">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#d1fae5; color:#065f46;">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Paid Booking</div>
                            <div class="kpi-number">{{ number_format($paidBooking, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card h-100" style="border-left-color:#f59e0b;">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#fef3c7; color:#92400e;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Pending Booking</div>
                            <div class="kpi-number">{{ number_format($pendingBooking, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- EXPORT BUTTON --}}
    <div class="export-section text-end mt-4">
        <a href="{{ route('admin.laporan.export', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" class="btn btn-export">
            <i class="fas fa-download"></i>
            <span>Export PDF</span>
        </a>
    </div>

    {{-- TABLE --}}
    <div class="table-card mt-4">
        <div class="table-header">
            <div>
                <h5 class="table-title mb-0">{{ $totalBooking }} Bookings</h5>
                <p class="table-subtitle mb-0">Periode {{ $fromDate }} - {{ $toDate }}</p>
            </div>
        </div>

        <div class="table-responsive p-0">
            <table class="table laporan-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th class="text-center">User</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $st = $booking['status_pembayaran'] ?? 'pending';
                        @endphp
                        <tr>
                            <td><strong class="text-nowrap">#{{ $booking['id_booking'] }}</strong></td>
                            <td>{{ $booking['nama_lapangan'] ?? 'N/A' }}</td>
                            <td>
                                @php
                                    try {
                                        echo \Carbon\Carbon::parse($booking['tanggal'])->translatedFormat('d M Y');
                                    } catch (\Throwable $e) {
                                        echo e($booking['tanggal']);
                                    }
                                @endphp
                            </td>
                            <td>{{ $booking['jam_mulai'] }} - {{ $booking['jam_selesai'] }}</td>
                            <td class="text-center">{{ $booking['nama_user'] ?? $booking['id_user'] }}</td>
                            <td class="text-center">
                                @if($st === 'pending')
                                    <span class="badge status-badge status-pending">Pending</span>
                                @elseif($st === 'expired')
                                    <span class="badge status-badge status-expired">Expired</span>
                                @elseif($st === 'paid' || $st === 'confirmed')
                                    <span class="badge status-badge status-paid">Paid</span>
                                @elseif($st === 'cancelled')
                                    <span class="badge status-badge status-cancelled">Cancelled</span>
                                @else
                                    <span class="badge status-badge status-info">{{ ucfirst($st) }}</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold">Rp{{ number_format($booking['total_harga'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-icon"><i class="fas fa-chart-bar"></i></div>
                                <div class="empty-title">No bookings found</div>
                                <div class="empty-subtitle text-muted">Try adjusting your date range</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection


