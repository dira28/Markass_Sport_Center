@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <section class="hero-dashboard" style="position: relative;">

        <!-- TAMBAHAN: area klik seluruh hero -->
        <a href="{{ url('/booking') }}" style="position:absolute; top:0; left:0; width:100%; height:100%; z-index:1;"></a>

        <div class="container text-center text-md-start" style="position: relative; z-index:2;">
            <div class="col-lg-6">
                <h1>Booking Lapangan Olahraga<br>Jadi Lebih Mudah</h1>
                <p>Pesan badminton & futsal secara online di Markass Sport Center Kudus dengan sistem real-time.</p>
                <div class="hero-btns">
                    <a href="{{ url('/booking') }}" class="btn-red-dashboard me-md-2">Booking Sekarang</a>
                    <a href="{{ url('/booking') }}" class="btn-outline-white">Lihat Jadwal</a>
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
                                <a href="{{ url('/booking') }}" class="btn-red-dashboard w-100">Booking Sekarang</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- DIUBAH DI SINI (bg-white ➝ bg-light) -->
    <section class="py-5 bg-light shadow-sm">
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
            <h3 class="section-title-dashboard text-black text-center mb-5">Bagaimana Cara Pesan?</h3>
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

            <div class="row mt-4 testimonial-row">

                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="rounded-circle mb-3">
                        <p class="fst-italic text-muted">"Tempatnya nyaman banget, lapangannya juga bersih dan terawat."</p>
                        <h6>Siti Rahma</h6>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-circle mb-3">
                        <p class="fst-italic text-muted">"Proses bookingnya cepet banget, gak perlu nunggu admin bales WA!"
                        </p>
                        <h6>Andi Pratama</h6>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <img src="https://randomuser.me/api/portraits/men/65.jpg" class="rounded-circle mb-3">
                        <p class="fst-italic text-muted">"Sistemnya mudah dipakai, sangat membantu untuk booking cepat."</p>
                        <h6>Budi Santoso</h6>
                    </div>
                </div>

            </div </div>
            <<<<<<< Updated upstream </section>

                <button class="add-comment-btn" onclick="openCommentModal()">
                    + Tambah Komentar
                </button>

                <div class="comment-modal" id="commentModal">
                    <div class="comment-box">
                        <h5 class="mb-3" style="font-weight: 800;">Tambah Komentar</h5>

                        <form onsubmit="addComment(event)">
                            <input type="text" id="namaKomentar" class="form-control mb-3" placeholder="Nama Anda" required>

                            <textarea id="isiKomentar" class="form-control" rows="4" placeholder="Tulis komentar..."
                                required></textarea>

                            <div class="mt-3 d-flex gap-2">
                                <button type="submit" class="btn btn-danger w-100">
                                    Kirim
                                </button>

                                <button type="button" class="btn btn-secondary w-100" onclick="closeCommentModal()">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function openCommentModal() {
                        document.getElementById('commentModal').style.display = 'block';
                    }

                    function closeCommentModal() {
                        document.getElementById('commentModal').style.display = 'none';
                    }

                    function addComment(event) {
                        event.preventDefault();

                        const nama = document.getElementById('namaKomentar').value.trim();
                        const komentar = document.getElementById('isiKomentar').value.trim();

                        if (!nama || !komentar) {
                            return;
                        }

                        const container = document.querySelector('.testimonial-row');

                        container.insertAdjacentHTML('beforeend', `
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <img src="https://randomuser.me/api/portraits/lego/1.jpg" class="rounded-circle mb-3">
                    <p class="fst-italic text-muted">"${komentar}"</p>
                    <h6>${nama}</h6>
                </div>
            </div>
        `);

                        document.getElementById('namaKomentar').value = '';
                        document.getElementById('isiKomentar').value = '';

                        closeCommentModal();
                    }

                    window.onclick = function (event) {
                        const modal = document.getElementById('commentModal');

                        if (event.target === modal) {
                            closeCommentModal();
                        }
                    }
                </script>

@endsection