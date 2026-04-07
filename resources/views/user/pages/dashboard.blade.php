@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <section class="hero-dashboard">
        <div class="container text-center text-md-start">
            <div class="col-lg-6">
                <h1>Booking Lapangan Olahraga<br>Jadi Lebih Mudah</h1>
                <p>Pesan badminton & futsal secara online di Markass Sport Center Kudus dengan sistem real-time.</p>
                <div class="hero-btns">
                    <a href="/booking" class="btn-red-dashboard me-md-2">Booking Sekarang</a>
                    <a href="/booking" class="btn-outline-white">Lihat Jadwal</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h3 class="section-title-dashboard">Olahraga Favoritmu</h3>
                <div class="title-line"></div>
            </div>

            <div class="row g-4 justify-content-center">
                @php
                    $lapangans = [
                        ['name' => 'Badminton Lapangan 1', 'price' => '35.000', 'img' => 'badminton.jpg'],
                        ['name' => 'Badminton Lapangan 2', 'price' => '35.000', 'img' => 'badminton.jpg'],
                        ['name' => 'Futsal Lapangan 1', 'price' => '120.000', 'img' => 'futsal.jpg'],
                        ['name' => 'Futsal Lapangan 2', 'price' => '120.000', 'img' => 'futsal.jpg'],
                    ];
                @endphp

                @foreach($lapangans as $lap)
                <div class="col-md-6 col-lg-3">
                    <div class="card-dashboard">
                        <div class="card-img-wrapper">
                            <img src="{{ asset('images/' . $lap['img']) }}" alt="{{ $lap['name'] }}">
                        </div>
                        <div class="card-body">
                            <h5>{{ $lap['name'] }}</h5>
                            <p class="price">Mulai Rp{{ $lap['price'] }} <span>/ Jam</span></p>
                            <a href="/booking" class="btn-red-dashboard w-100">Booking Sekarang</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5 bg-white shadow-sm">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <img src="https://cdn-icons-png.flaticon.com/512/1048/1048953.png" width="60" class="mb-3">
                        <h5>Lapangan Bersih</h5>
                        <p class="text-muted">Lapangan selalu dirawat dan memenuhi standar nasional.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <img src="https://cdn-icons-png.flaticon.com/512/747/747310.png" width="60" class="mb-3">
                        <h5>Booking Real-Time</h5>
                        <p class="text-muted">Cek ketersediaan jadwal secara instan dari mana saja.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" width="60" class="mb-3">
                        <h5>Lokasi Strategis</h5>
                        <p class="text-muted">Berlokasi di pusat kota Kudus yang mudah diakses.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="timeline-section py-5">
        <div class="container">
            <h3 class="section-title-dashboard text-white text-center mb-5">Bagaimana Cara Pesan?</h3>
            <div class="timeline-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <h5>Pilih Lapangan</h5>
                    <p>Cari jenis olahraga yang kamu inginkan.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h5>Atur Jadwal</h5>
                    <p>Tentukan tanggal dan jam yang kosong.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h5>Isi Data</h5>
                    <p>Lengkapi informasi pemesanan kamu.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h5>Pembayaran</h5>
                    <p>Selesaikan transaksi via transfer/E-wallet.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container text-center">
            <h3 class="section-title-dashboard">Apa Kata Mereka?</h3>
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-circle mb-3">
                        <p class="fst-italic text-muted">"Proses bookingnya cepet banget, gak perlu nunggu admin bales WA!"</p>
                        <h6>Andi Pratama</h6>
                    </div>
                </div>
                </div>
        </div>
    </section>

@endsection