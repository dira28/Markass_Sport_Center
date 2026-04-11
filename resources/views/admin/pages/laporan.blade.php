@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">

        {{-- HEADER --}}
        <div class="dashboard-header-v2 mb-4">
            <div>
                <h3>Laporan</h3>
                <p>Ringkasan pendapatan & booking</p>
            </div>
        </div>

        {{-- 🔥 FILTER (UI DOANG DULU) --}}
        <div class="card-box mb-4">
            <div class="row g-3">

                <div class="col-md-4">
                    <label>Dari Tanggal</label>
                    <input type="date" class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Sampai Tanggal</label>
                    <input type="date" class="form-control">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn-filter w-100">
                        Filter
                    </button>
                </div>

            </div>
        </div>

        {{-- 🔥 STATISTIK (PAKE COMPONENT) --}}
        <div class="mb-4">
            <h5 class="mb-3">Statistik Hari Ini</h5>

            @include('admin.components.revenue-cards', [
                'revenueToday' => $revenueToday ?? 0,
                'totalBooking' => $totalBooking ?? 0
            ])
        </div>

        {{-- 🔥 TABLE PLACEHOLDER --}}
        <div class="card-box">
            <h5 class="mb-3">Data Booking</h5>

            <div class="text-center text-muted py-5">
                Data booking akan tampil di sini
            </div>
        </div>

    </div>

@endsection