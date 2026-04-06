@extends('layouts.app')

@section('title', 'Harga')

@section('content')

<div class="container py-4">

    <!-- IMAGE HEADER -->
    <div class="row mb-4">
        <div class="col-md-6">
            <img src="https://images.unsplash.com/photo-1521412644187-c49fa049e84d"
                 class="img-fluid rounded-4 w-100" style="height:200px; object-fit:cover;">
        </div>
        <div class="col-md-6">
            <img src="https://images.unsplash.com/photo-1600180758890-6b94519a8ba6"
                 class="img-fluid rounded-4 w-100" style="height:200px; object-fit:cover;">
        </div>
    </div>

    <!-- TITLE -->
    <h3 class="fw-bold">Markass Sport Center</h3>
    <p class="text-muted mb-1">📍 Jl. Jendral Sudirman No. 184, Kudus</p>
    <p class="text-warning">⭐ 4.8 (324 ulasan)</p>

    <!-- TAB MENU -->
    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active text-danger fw-bold"
                    data-bs-toggle="tab"
                    data-bs-target="#informasi"
                    type="button">
                Informasi
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#jadwal"
                    type="button">
                Jadwal Operasional
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#fasilitas"
                    type="button">
                Fasilitas
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#ulasan"
                    type="button">
                Ulasan
            </button>
        </li>
    </ul>

    <!-- TAB CONTENT -->
    <div class="tab-content">

        <!-- INFORMASI -->
        <div class="tab-pane fade show active" id="informasi">
            <h5 class="fw-bold">Tentang Venue</h5>
            <p class="text-muted">
                Markass Sports Center adalah fasilitas olahraga modern yang menyediakan lapangan futsal dan badminton berkualitas tinggi.
                Dengan lokasi strategis di Kudus, kami menawarkan pengalaman bermain yang nyaman dengan fasilitas lengkap dan pelayanan profesional.
            </p>
        </div>

        <!-- JADWAL -->
        <div class="tab-pane fade" id="jadwal">
            <h5 class="fw-bold">Jadwal Operasional</h5>
            <p class="text-muted">Senin - Minggu : 08.00 - 22.00</p>
        </div>

        <!-- FASILITAS -->
        <div class="tab-pane fade" id="fasilitas">
            <h5 class="fw-bold">Fasilitas</h5>
            <ul>
                <li>Lapangan Indoor</li>
                <li>Ruang Ganti</li>
                <li>Parkir Luas</li>
                <li>Kantin</li>
            </ul>
        </div>

        <!-- ULASAN -->
        <div class="tab-pane fade" id="ulasan">
            <h5 class="fw-bold">Ulasan</h5>
            <p class="text-muted">⭐ 4.8 dari 324 ulasan pengguna</p>
        </div>

    </div>

</div>

@endsection