@extends('layouts.admin')

@vite('resources/css/admin/components/lates-booking.css')

@section('content')

    <div class="content-wrapper">

        {{-- HEADER --}}
        <div class="dashboard-header-v2 mb-3">
            <div>
                <h3 class="fw-bold mb-1">Halo Admin!</h3>
                <p class="text-muted mb-0">Selamat datang di Dashboard Markass Sport Center</p>
            </div>
        </div>

        {{-- KPI (TOTAL KESELURUHAN) --}}
        <div class="mb-3">
            @include('admin.components.kpi-cards', [
                'totalRevenue'  => $totalRevenue ?? 0,
                'totalBooking'  => $totalBooking ?? 0,
                'totalUser'     => $totalUser ?? 0,
                'averagePerDay' => $averagePerDay ?? 0
            ])
        </div>

        {{-- CHART + SUMMARY --}}
        <div class="row g-3 align-items-stretch">

            {{-- GRAFIK PENDAPATAN (KIRI) --}}
            <div class="col-lg-8 col-md-12">
                @include('admin.components.profit-overview', [
                    'data'  => $chartData ?? [],
                    'id'    => 'dashboard',
                    'title' => 'Grafik Pendapatan'
                ])
            </div>

            {{-- REVENUE SUMMARY & DONUT CHART (KANAN) --}}
            <div class="col-lg-4 col-md-12 d-flex flex-column gap-3">
                @include('admin.components.revenue-summary', [
                    'revenueToday' => $revenueToday ?? 0,
                    'totalBooking' => $totalBookingToday ?? 0 
                ])

                @include('admin.components.category-chart', [
                    'categoryData' => $categoryData ?? []
                ])
            </div>

        </div>

        {{-- INSIGHT --}}
        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card-box p-3 bg-white rounded-3 border-0 shadow-sm">
                    <h6 class="fw-bold mb-2">Insight Hari Ini</h6>
                    <p class="text-muted mb-0" style="font-size: 14px;">Belum ada data insight hari ini</p>
                </div>
            </div>
        </div>

        {{-- LATEST BOOKING --}}
        <div class="row g-3 mt-1">
            <div class="col-12">
                @include('admin.components.latest-booking', [
                    'data'       => $latestBookings ?? [],
                    'title'      => 'Latest Bookings',
                    'showButton' => true,
                    'url'        => route('admin.laporan')
                ])
            </div>
        </div>

    </div>

    {{-- SCRIPT CHART JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endsection