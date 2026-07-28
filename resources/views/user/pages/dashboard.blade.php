@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- HERO SECTION MODERN --}}
    <section class="hero-dashboard py-5">
        <div class="container py-md-5 position-relative z-2">
            <div class="row align-items-center">
                <div class="col-lg-7 text-center text-lg-start">
                    <span
                        class="badge bg-danger text-white mb-3 px-3 py-2 rounded-pill fw-semibold text-uppercase tracking-wider">
                        ⚡ Markass Sport Center Kudus
                    </span>
                    <h1 class="hero-title text-white fw-extrabold mb-3">
                        BOOKING LAPANGAN <br>
                        <span class="text-danger-gradient">OLAHRAGA DENGAN EASY</span>
                    </h1>
                    <p class="hero-subtitle text-light opacity-75 mb-4 pe-lg-5">
                        Pesan lapangan badminton & futsal favoritmu secara online di Kudus dengan sistem cek jadwal
                        real-time. Fast, clear, no hassle.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ url('/booking') }}" class="btn btn-red-dashboard btn-lg rounded-pill px-4 fw-bold">
                            Booking Sekarang <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ url('/booking') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 fw-bold">
                            Lihat Jadwal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FLOATING STATS BAR --}}
    <div class="container position-relative z-3" style="margin-top: -40px;">
        <div class="stats-card-wrapper p-4 rounded-4 shadow-lg">
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <h2 class="fw-extrabold text-danger mb-0">4+</h2>
                        <span class="text-muted small fw-semibold">LAPANGAN PREMIUM</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <h2 class="fw-extrabold text-danger mb-0">100%</h2>
                        <span class="text-muted small fw-semibold">REAL-TIME SCHEDULE</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <h2 class="fw-extrabold text-danger mb-0">24/7</h2>
                        <span class="text-muted small fw-semibold">SISTEM BOOKING</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <h2 class="fw-extrabold text-danger mb-0">TOP</h2>
                        <span class="text-muted small fw-semibold">KUALITAS STANDAR</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION OLAHRAGA FAVORITE --}}
    <section class="py-5 mt-4">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-danger fw-bold text-uppercase tracking-wider small">Pilihan Terbaik</span>
                <h2 class="fw-extrabold text-dark mt-1">Olahraga Favorite</h2>
                <div class="title-line-centered"></div>
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
                        <div class="card-sports-modern">
                            <div class="card-img-container">
                                <img src="{{ asset('images/' . $lap['img']) }}" alt="{{ $lap['name'] }}">
                                <div class="card-overlay">
                                    <span class="badge bg-danger mb-2">Popular</span>
                                    <h5 class="fw-bold text-white mb-1">{{ $lap['name'] }}</h5>
                                    <p class="text-white-50 small mb-3">Mulai <strong
                                            class="text-white fw-bold">Rp{{ $lap['price'] }}</strong> / Jam</p>
                                    <a href="{{ url('/booking') }}" class="btn btn-sm btn-light rounded-pill fw-bold w-100">
                                        Booking Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FEATURES SECTION --}}
    <section class="py-5 bg-features">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="feature-box-modern">
                        <div class="icon-circle mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/1048/1048953.png" width="40" alt="Bersih">
                        </div>
                        <h5 class="fw-bold">Lapangan Bersih</h5>
                        <p class="text-muted small mb-0">Lapangan selalu dirawat secara rutin dan memenuhi standar mutu
                            nasional.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box-modern">
                        <div class="icon-circle mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/747/747310.png" width="40" alt="Realtime">
                        </div>
                        <h5 class="fw-bold">Booking Real-Time</h5>
                        <p class="text-muted small mb-0">Cek ketersediaan jam secara langsung tanpa harus menunggu respon
                            konfirmasi manual.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box-modern">
                        <div class="icon-circle mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" width="40" alt="Strategis">
                        </div>
                        <h5 class="fw-bold">Lokasi Strategis</h5>
                        <p class="text-muted small mb-0">Berlokasi tepat di kawasan pusat kota Kudus yang sangat mudah
                            dijangkau.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CARA PESAN / TIMELINE --}}
    <section class="py-5 bg-dark text-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-danger fw-bold text-uppercase tracking-wider small">Langkah Mudah</span>
                <h2 class="fw-extrabold text-white mt-1">Bagaimana Cara Pesan?</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="step-card-modern">
                        <div class="step-num">01</div>
                        <h5 class="fw-bold mt-3">Pilih Lapangan</h5>
                        <p class="text-white-50 small mb-0">Cari jenis olahraga & lapangan yang ingin kamu sewa.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card-modern">
                        <div class="step-num">02</div>
                        <h5 class="fw-bold mt-3">Atur Jadwal</h5>
                        <p class="text-white-50 small mb-0">Pilih tanggal dan slot jam kosong yang sesuai jam kamu.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card-modern">
                        <div class="step-num">03</div>
                        <h5 class="fw-bold mt-3">Isi Data</h5>
                        <p class="text-white-50 small mb-0">Lengkapi identitas serta kontak penerima pesanan.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card-modern">
                        <div class="step-num">04</div>
                        <h5 class="fw-bold mt-3">Pembayaran</h5>
                        <p class="text-white-50 small mb-0">Selesaikan transaksi instan melalui e-wallet / transfer.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TESTIMONIALS SECTION --}}
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-danger fw-bold text-uppercase tracking-wider small">Ulasan Pengguna</span>
                <h2 class="fw-extrabold text-dark mt-1">Apa Kata Mereka?</h2>
                <div class="title-line-centered"></div>
            </div>

            <div class="row g-4 testimonial-row">
                <div class="col-md-4">
                    <div class="testimonial-card-modern">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="rounded-circle me-3"
                                width="50" height="50">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Siti Rahma</h6>
                                <span class="text-warning small">★★★★★</span>
                            </div>
                        </div>
                        <p class="text-muted small fst-italic mb-0">"Tempatnya nyaman banget, pencahayaan terang dan
                            lapangannya bersih banget!"</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="testimonial-card-modern">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-circle me-3" width="50"
                                height="50">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Andi Pratama</h6>
                                <span class="text-warning small">★★★★★</span>
                            </div>
                        </div>
                        <p class="text-muted small fst-italic mb-0">"Proses bookingnya secepat itu, gak usah ribet nunggu
                            balasan WhatsApp admin!"</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="testimonial-card-modern">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/men/65.jpg" class="rounded-circle me-3" width="50"
                                height="50">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Budi Santoso</h6>
                                <span class="text-warning small">★★★★★</span>
                            </div>
                        </div>
                        <p class="text-muted small fst-italic mb-0">"Sistem jadwalnya akurat banget, recommended buat tim
                            yang suka main malam."</p>
                    </div>
                </div>
            </div>

            {{-- LOGIKA TOMBOL KOMENTAR SESUAI SESSION --}}
            <div class="text-center mt-5">
                @if(session('token'))
                    @if($hasBooked ?? false)
                        {{-- KONDISI 1: User Login & Sudah Pernah Booking --}}
                        <button class="btn btn-outline-danger rounded-pill px-4 fw-bold" onclick="openCommentModal()">
                            + Tambah Komentar
                        </button>
                    @else
                        {{-- KONDISI 2: User Login Tapi Belum Pernah Booking --}}
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i> Kamu harus melakukan minimal 1x booking untuk dapat memberikan
                            ulasan.
                        </p>
                    @endif
                @else
                    {{-- KONDISI 3: User Belum Login --}}
                    <p class="text-muted small mb-0">
                        Ingin memberikan ulasan? <a href="{{ route('login') }}"
                            class="text-danger fw-bold text-decoration-none">Login sekarang</a>
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- MODAL KOMENTAR (AKTIF JIKA ADA TOKEN & SUDAH BOOKING) --}}
    @if(session('token') && ($hasBooked ?? false))
        <div class="comment-modal-backdrop" id="commentModal" style="display: none;">
            <div class="comment-box-card">
                <h5 class="fw-bold mb-3 text-dark">Tambah Komentar Kamu</h5>
                <form onsubmit="addComment(event)">
                    <div class="mb-3">
                        {{-- Nama diambil dari session user --}}
                        <input type="text" id="namaKomentar" class="form-control rounded-3"
                            value="{{ session('user.name') ?? session('user_name') ?? 'Pengguna' }}" readonly required>
                    </div>
                    <div class="mb-3">
                        <textarea id="isiKomentar" class="form-control rounded-3" rows="4"
                            placeholder="Tuliskan pengalaman kamu..." required></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">Kirim</button>
                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold"
                            onclick="closeCommentModal()">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush