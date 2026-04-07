@extends('layouts.admin')

@section('content')

    <!-- HEADER -->
    <div class="dashboard-header">
        <div>
            <h4>Manajemen Booking</h4>
            <small>Dashboard / Booking</small>
        </div>

        <div class="filter-wrapper">

            <!-- ROW 1 (DATE RANGE) -->
            <div class="filter-top">
                <div class="date-box" id="dateRangeBox">
                    <i class="bi bi-calendar"></i>
                    <span id="dateText">Pilih tanggal</span>
                    <i class="bi bi-chevron-down"></i>
                </div>

                <input type="text" id="dateRange" hidden>
            </div>

            <!-- ROW 2 -->
            <div class="filter-bottom">
                <div class="select-box">
                    <select>
                        <option>Semua Olahraga</option>
                        <option>Badminton</option>
                        <option>Futsal</option>
                    </select>
                    <i class="bi bi-chevron-down"></i>
                </div>

                <div class="select-box">
                    <select>
                        <option>Semua Status</option>
                        <option>Berlangsung</option>
                        <option>Menunggu</option>
                        <option>Selesai</option>
                        <option>Dibatalkan</option>
                    </select>
                    <i class="bi bi-chevron-down"></i>
                </div>

                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Cari...">
                </div>

                <button class="btn-filter">Filter</button>

            </div>

        </div>
    </div>

    <!-- CARD STATISTIK -->
    <div class="row g-3 mt-2">

        <div class="col-md-3">
            <div class="card-box total">
                <p>Total Booking</p>
                <h3>215</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-box success">
                <p>Berlangsung</p>
                <h3>38</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-box warning">
                <p>Menunggu</p>
                <h3>12</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-box danger">
                <p>Dibatalkan</p>
                <h3>6</h3>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card-box mt-4">

        <h5 class="mb-3">Data Booking</h5>

        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>ID</th>
                    <th>Pengguna</th>
                    <th>Lapangan</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>22 Apr 2024</td>
                    <td>BK-001</td>
                    <td>Andi</td>
                    <td>Lapangan 1</td>
                    <td><span class="badge bg-success">Berlangsung</span></td>
                </tr>
            </tbody>
        </table>

    </div>

@endsection