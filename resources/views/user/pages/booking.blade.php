@extends('layouts.app')

@section('title', 'Booking')

@push('styles')
@vite('resources/css/user/pages/booking.css')
@endpush

@section('content')

<section class="hero-booking">
    <div class="container">
        <h2 class="fw-bold">Harga Sewa Lapangan<br>Olahraga Kudus!</h2>
        <p>Lihat harga sewa lapangan favoritmu di Markass Sport Center Kudus</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row row-equal">

            <!-- KIRI -->
            <div class="col-md-8 col-left">
                
                <div class="harga-wrapper">

                    <h5 class="fw-bold mb-3">Harga Per Jam</h5>

                    <!-- BADMINTON -->
                    <div class="card-lapangan" data-nama="Badminton">
                        <img src="https://kelanakids.com/wp-content/uploads/2023/03/badminton-concept-with-racket-shuttlecock.jpg">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Badminton</h6>
                            <small class="text-muted">09.00 - 15.00 (20k) | 16.00 - 23.00 (30k)</small>
                        </div>
                        <div class="harga">Dynamic</div>
                    </div>

                    <!-- FUTSAL -->
                    <div class="card-lapangan active" data-nama="Futsal">
                        <img src="https://www.rukita.co/stories/wp-content/uploads/2020/02/futsal.jpg">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Futsal</h6>
                            <small class="text-muted">09.00 - 15.00 (85k) | 16.00 - 23.00 (135k)</small>
                        </div>
                        <div class="harga">Dynamic</div>
                    </div>

                </div>

            </div>

            <!-- KANAN -->
            <div class="col-md-4 col-right">
                <div>
                    <div class="booking-summary shadow-sm">

                        <h6 class="fw-bold mb-3">Ringkasan Booking</h6>

                        <div class="d-flex mb-3">
                            <button class="sport active btn btn-outline-danger w-50 me-2"
                                data-nama="Futsal">Futsal</button>

                            <button class="sport btn btn-outline-secondary w-50"
                                data-nama="Badminton">Badminton</button>
                        </div>

                        <input type="date" class="form-control mb-3" id="tanggal">

                        <!-- JAM -->
                        <div class="jadwal mb-3">

                            <!-- PAGI -->
                            <button class="btn btn-light btn-sm jam-btn" data-jam="09:00">09:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="10:00">10:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="11:00">11:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="12:00">12:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="13:00">13:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="14:00">14:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="15:00">15:00</button>

                            <!-- MALAM -->
                            <button class="btn btn-light btn-sm jam-btn" data-jam="16:00">16:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="17:00">17:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="18:00">18:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="19:00">19:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="20:00">20:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="21:00">21:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="22:00">22:00</button>
                            <button class="btn btn-light btn-sm jam-btn" data-jam="23:00">23:00</button>

                        </div>

                        <!-- DURASI -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button class="btn btn-light" id="minus">-</button>
                            <b><span id="durasi">1</span> Jam</b>
                            <button class="btn btn-light" id="plus">+</button>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <small>Olahraga</small>
                            <small id="summary-nama">Futsal</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <small>Tanggal</small>
                            <small id="summary-tanggal">-</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <small>Jam</small>
                            <small id="summary-jam">-</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <small>Durasi</small>
                            <small><span id="summary-durasi">1</span> jam</small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Harga</span>
                            <span id="total">Rp0</span>
                        </div>

                        <button class="btn btn-red w-100 mt-3">
                            Booking Sekarang →
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let sportButtons = document.querySelectorAll(".sport");
    let cards = document.querySelectorAll(".card-lapangan");
    let jamButtons = document.querySelectorAll(".jam-btn");

    let summaryNama = document.getElementById("summary-nama");
    let summaryJam = document.getElementById("summary-jam");
    let totalHarga = document.getElementById("total");

    let durasiEl = document.getElementById("durasi");
    let summaryDurasi = document.getElementById("summary-durasi");

    let durasi = 1;
    let olahragaAktif = "Futsal";
    let jamAktif = null;
    let hargaAktif = 0;

    function getHarga(olahraga, jam) {
        let hour = parseInt(jam.split(":")[0]);

        if (olahraga === "Futsal") {
            if (hour >= 9 && hour <= 15) return 85000;
            if (hour >= 16 && hour <= 23) return 135000;
        }

        if (olahraga === "Badminton") {
            if (hour >= 9 && hour <= 15) return 20000;
            if (hour >= 16 && hour <= 23) return 30000;
        }

        return 0;
    }

    function updateUI() {
        if (!jamAktif) return;

        hargaAktif = getHarga(olahragaAktif, jamAktif);

        summaryNama.textContent = olahragaAktif;
        summaryJam.textContent = jamAktif;

        totalHarga.textContent =
            "Rp" + (hargaAktif * durasi).toLocaleString("id-ID");
    }

    sportButtons.forEach(btn => {
        btn.addEventListener("click", function () {

            olahragaAktif = this.dataset.nama;

            sportButtons.forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            cards.forEach(c => {
                c.classList.remove("active");
                if (c.dataset.nama === olahragaAktif) {
                    c.classList.add("active");
                }
            });

            updateUI();
        });
    });

    cards.forEach(card => {
        card.addEventListener("click", function () {

            olahragaAktif = this.dataset.nama;

            cards.forEach(c => c.classList.remove("active"));
            this.classList.add("active");

            sportButtons.forEach(btn => {
                btn.classList.remove("active");
                if (btn.dataset.nama === olahragaAktif) {
                    btn.classList.add("active");
                }
            });

            updateUI();
        });
    });

    jamButtons.forEach(btn => {
        btn.addEventListener("click", function () {

            jamButtons.forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            jamAktif = this.dataset.jam;

            updateUI();
        });
    });

    document.getElementById("plus").addEventListener("click", function () {
        durasi++;
        updateDurasi();
    });

    document.getElementById("minus").addEventListener("click", function () {
        if (durasi > 1) durasi--;
        updateDurasi();
    });

    function updateDurasi() {
        durasiEl.textContent = durasi;
        summaryDurasi.textContent = durasi;
        updateUI();
    }

});
</script>

@endsection