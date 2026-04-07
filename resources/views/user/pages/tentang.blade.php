@extends('layouts.app')

@section('title', 'Harga')

@section('content')
<div class="container py-5">

    <!-- IMAGE HEADER -->
    <div class="row mb-5 g-3">
        <div class="col-md-6">
            <div class="position-relative overflow-hidden rounded-4 shadow-sm" style="height:220px;">
                <img src="https://images.unsplash.com/photo-1521412644187-c49fa049e84d"
                     class="w-100 h-100 object-fit-cover">
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative overflow-hidden rounded-4 shadow-sm" style="height:220px;">
                <img src="https://images.unsplash.com/photo-1600180758890-6b94519a8ba6"
                     class="w-100 h-100 object-fit-cover">
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25"></div>
            </div>
        </div>
    </div>

    <!-- TITLE -->
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Markass Sport Center</h2>
        <p class="text-muted mb-1 fs-6">📍 Jl. Jendral Sudirman No. 184, Kudus</p>
        <p class="text-warning fw-semibold fs-6">⭐ 4.8 (324 ulasan)</p>
    </div>

    <!-- TAB MENU -->
    <ul class="nav nav-tabs mb-4 border-0" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active text-danger fw-bold px-4 py-2 rounded-top"
                id="informasi-tab"
                data-bs-toggle="tab"
                data-bs-target="#informasi"
                type="button"
                role="tab">
                Informasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 rounded-top"
                id="jadwal-tab"
                data-bs-toggle="tab"
                data-bs-target="#jadwal"
                type="button"
                role="tab">
                Jadwal Operasional
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 rounded-top"
                id="fasilitas-tab"
                data-bs-toggle="tab"
                data-bs-target="#fasilitas"
                type="button"
                role="tab">
                Fasilitas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 rounded-top"
                id="ulasan-tab"
                data-bs-toggle="tab"
                data-bs-target="#ulasan"
                type="button"
                role="tab">
                Ulasan
            </button>
        </li>
    </ul>

    <!-- TAB CONTENT -->
    <div class="tab-content p-4 bg-light rounded-4 shadow-sm">
        <!-- INFORMASI -->
        <div class="tab-pane fade show active" id="informasi" role="tabpanel">
            <h5 class="fw-bold mb-3">Tentang Venue</h5>
            <p class="text-muted fs-6">
                Markass Sports Center adalah fasilitas olahraga modern yang menyediakan lapangan futsal dan badminton berkualitas tinggi.
                Dengan lokasi strategis di Kudus, kami menawarkan pengalaman bermain yang nyaman dengan fasilitas lengkap dan pelayanan profesional.
            </p>
        </div>

        <!-- JADWAL -->
        <div class="tab-pane fade" id="jadwal" role="tabpanel">
            <h5 class="fw-bold mb-3">Jadwal Operasional</h5>
            <p class="text-muted fs-6">Senin - Minggu : 08.00 - 22.00</p>
        </div>

        <!-- FASILITAS -->
        <div class="tab-pane fade" id="fasilitas" role="tabpanel">
            <h5 class="fw-bold mb-3">Fasilitas</h5>
            <ul class="list-group list-group-flush fs-6">
                <li class="list-group-item border-0 p-1">🏟️ Lapangan Indoor</li>
                <li class="list-group-item border-0 p-1">🚻 Ruang Ganti</li>
                <li class="list-group-item border-0 p-1">🅿️ Parkir Luas</li>
                <li class="list-group-item border-0 p-1">☕ Kantin</li>
            </ul>
        </div>

        <!-- ULASAN -->
        <div class="tab-pane fade" id="ulasan" role="tabpanel">
            <h5 class="fw-bold mb-3">Ulasan</h5>
            <p class="text-muted fs-6">⭐ 4.8 dari 324 ulasan pengguna</p>

        </div>
<div class="mb-4 rounded-4 shadow-sm overflow-hidden">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.123456789!2d110.8613472!3d-6.8043076!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70c525fd6001b1%3A0xe28fff5d78f8ce5f!2sMarkass%20Sport%20Center!5e0!3m2!1sid!2sid!4v1680078901234!5m2!1sid!2sid"
        width="100%"
        height="300"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>
    </div>

</div>
@endsection