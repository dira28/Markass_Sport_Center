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

    <!-- FILTER & STATS -->

    <!-- FILTER -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <div class="filter-form">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Dari Tanggal</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sampai Tanggal</label>
                            <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="report-stats">
                <h5 class="text-muted mb-3">Showing {{ $totalBooking }} bookings from {{ $fromDate }} to {{ $toDate }}</h5>
            </div>
        </div>
    </div>

    <!-- KPI CARDS -->
    <div class="row mb-5 g-4">
        <div class="col-md-6">
            <div class="card stat-card text-white text-center h-100">
                <div class="card-body">
                    <h3>{{ number_format($totalBooking, 0, ',', '.') }}</h3>
                    <p>Total Booking</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card revenue-card text-white text-center h-100">
                <div class="card-body">
                    <h3>Rp{{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- EXPORT BUTTON -->
    <div class="text-end mb-4">
        <a href="{{ route('admin.laporan.export', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" 
           class="btn btn-success btn-lg px-4">
            <i class="fas fa-download me-2"></i> Export PDF
        </a>
    </div>

    <!-- TABLE -->

    <!-- TABLE -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0 fw-bold">{{ $totalBooking }} Bookings</h5>
                <small class="text-muted">Periode {{ $fromDate }} - {{ $toDate }}</small>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="border-0">ID</th>
                            <th class="border-0">Lapangan</th>
                            <th class="border-0">Tanggal</th>
                            <th class="border-0">Jam</th>
                            <th class="border-0 text-center">User</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td><strong>#{{ $booking['id_booking'] }}</strong></td>
                            <td>{{ $booking['nama_lapangan'] ?? 'N/A' }}</td>
                            <td>
                                <strong>{{ $booking['tanggal'] }}</strong>
                            </td>
                            <td>{{ $booking['jam_mulai'] }} - {{ $booking['jam_selesai'] }}</td>
                            <td class="text-center">{{ $booking['nama_user'] ?? $booking['id_user'] }}</td>
                            <td class="text-center">
                                @if($booking['status_pembayaran'] == 'pending')
                                    <span class="badge badge-warning px-3 py-2">Pending</span>
                                @elseif($booking['status_pembayaran'] == 'expired')
                                    <span class="badge badge-secondary px-3 py-2">Expired</span>
                                @elseif($booking['status_pembayaran'] == 'paid')
                                    <span class="badge badge-success px-3 py-2">Paid</span>
                                @else
                                    <span class="badge badge-info px-3 py-2">{{ ucfirst($booking['status_pembayaran']) }}</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-success">
                                Rp{{ number_format($booking['total_harga'] ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-chart-bar fa-4x text-muted mb-4 d-block"></i>
                                <h5 class="text-muted mb-2">No bookings found</h5>
                                <p class="text-muted">Try adjusting your date range</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
