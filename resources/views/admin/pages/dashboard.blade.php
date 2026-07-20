@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">

        {{-- HEADER --}}
        <div class="dashboard-header-v2 mb-3">
            <div>
                <h3>Halo Admin!</h3>
                <p>Selamat datang di Dashboard Markass Sport Center</p>
            </div>
        </div>

        {{-- KPI --}}
        <div class="mb-4">
            @include('admin.components.kpi-cards', [
                'totalRevenue' => $totalRevenue ?? 0,
                'totalBooking' => $totalBooking ?? 0,
                'totalUser' => $totalUser ?? 0,
                'averagePerDay' => $averagePerDay ?? 0
            ])
        </div>

        {{-- GRAFIK + SUMMARY --}}
        <div class="row g-3">

            <div class="col-lg-8">
                @include('admin.components.profit-overview', [
                    'data' => $chartData ?? [
                        'day' => ['labels' => [], 'data' => []],
                        'month' => ['labels' => [], 'data' => []],
                        'year' => ['labels' => [], 'data' => []]
                    ],
                    'id' => 'dashboard',
                    'title' => 'Grafik Pendapatan'
                ])
            </div>

            <div class="col-lg-4">
                @include('admin.components.revenue-summary', [
                    'revenueToday' => $revenueToday ?? 0,
                    'totalBooking' => $totalBooking ?? 0
                ])
            </div>

        </div>

        {{-- INSIGHT --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card-box">
                    <h6 class="mb-2">Insight Hari Ini</h6>

                    <p class="text-muted mb-0">
                        Total booking hari ini:
                        <strong>{{ $totalBooking ?? 0 }}</strong>
                        booking dengan pendapatan
                        <strong>
                            Rp {{ number_format($revenueToday ?? 0, 0, ',', '.') }}
                        </strong>
                    </p>
                </div>
            </div>
        </div>

        {{-- LATEST BOOKING --}}
        <div class="row mt-4">
            <div class="col-12">

                @include('admin.components.latest-booking', [
                    'data' => $latestBookings ?? [],
                    'title' => 'Latest Bookings',
                    'showButton' => true,
                    'url' => route('admin.laporan')
                ])

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endsection