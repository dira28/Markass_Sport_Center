@extends('layouts.app')

@section('title', 'Booking')

@section('content')

<style>
    body {
        background: #f5f5f5;
    }

    .hero-booking {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
        url('https://images.unsplash.com/photo-1556056504-5c7696c4c28d');
        background-size: cover;
        background-position: center;
        height: 300px;
        display: flex;
        align-items: center;
        color: white;
    }

    .row-equal {
        display: flex;
        align-items: stretch;
    }

    .col-left, .col-right {
        display: flex;
    }

    .col-left > div,
    .col-right > div {
        width: 100%;
    }

    .card-lapangan {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 12px;
        background: white;
        margin-bottom: 20px;
        cursor: pointer;
        border: 2px solid transparent;
        min-height: 110px;
        flex: 1;
    }

    .card-lapangan.active {
        border: 2px solid #c62828;
    }

    .card-lapangan img {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
        margin-right: 20px;
    }

    .card-lapangan h6 {
        font-size: 18px;
    }

    .card-lapangan small {
        font-size: 14px;
    }

    .harga {
        color: #c62828;
        font-weight: bold;
        font-size: 16px;
    }

    .booking-summary {
        background: white;
        padding: 20px;
        border-radius: 12px;
        position: sticky;
        top: 20px;
    }

    .btn-red {
        background: #c62828;
        color: white;
    }

    .jadwal button.active {
        background: #c62828;
        color: white;
    }
</style>

<section class="hero-booking">
    <div class="container">
        <h2 class="fw-bold">Harga Sewa Lapangan<br>Olahraga Kudus!</h2>
        <p>Lihat harga sewa lapangan favoritmu di Markass Sport Center Kudus</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row row-equal">

            <div class="col-md-8 col-left">
                <div>

                    <h5 class="fw-bold mb-3">Harga Per Jam</h5>

                    <div class="card-lapangan"
                        data-nama="Badminton"
                        data-harga="60000">

                        <img src="https://kelanakids.com/wp-content/uploads/2023/03/badminton-concept-with-racket-shuttlecock.jpg">

                        <div class="flex-grow-1">
                            <h6 class="mb-1">Badminton</h6>
                            <small class="text-muted">Senin - Jumat 08.00 - 16.00</small>
                        </div>

                        <div class="harga">Rp60.000 / jam</div>
                    </div>

                    <div class="card-lapangan active"
                        data-nama="Futsal"
                        data-harga="80000">

                        <img src="https://www.rukita.co/stories/wp-content/uploads/2020/02/futsal.jpg">

                        <div class="flex-grow-1">
                            <h6 class="mb-1">Futsal</h6>
                            <small class="text-muted">Senin - Jumat 16.00 - 22.00</small>
                        </div>

                        <div class="harga">Rp80.000 / jam</div>
                    </div>

                </div>
            </div>

            <div class="col-md-4 col-right">
                <div>

                    <div class="booking-summary shadow-sm">

                        <h6 class="fw-bold mb-3">Ringkasan Booking</h6>

                        <div class="d-flex mb-3">
                            <button class="sport active btn btn-outline-danger w-50 me-2"
                                data-harga="80000" data-nama="Futsal">Futsal</button>

                            <button class="sport btn btn-outline-secondary w-50"
                                data-harga="60000" data-nama="Badminton">Badminton</button>
                        </div>

                        <input type="date" class="form-control mb-3" id="tanggal">

                        <div class="jadwal mb-3">
                            <button class="btn btn-light btn-sm">10:00</button>
                            <button class="btn btn-light btn-sm">11:00</button>
                            <button class="btn btn-light btn-sm">12:00</button>
                        </div>

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
                            <span id="total">Rp80.000</span>
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
    let harga = 80000;
    let durasi = 1;

    const format = new Intl.NumberFormat('id-ID');

    const cards = document.querySelectorAll('.card-lapangan');

    cards.forEach(card => {
        card.onclick = function () {
            cards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            harga = parseInt(this.dataset.harga);
            document.getElementById('summary-nama').innerText = this.dataset.nama;

            document.querySelectorAll('.sport').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.nama === this.dataset.nama) {
                    btn.classList.add('active');
                }
            });

            hitungTotal();
        }
    });

    document.querySelectorAll('.sport').forEach(btn => {
        btn.onclick = function () {
            document.querySelectorAll('.sport').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            harga = parseInt(this.dataset.harga);
            document.getElementById('summary-nama').innerText = this.dataset.nama;

            hitungTotal();
        }
    });

    document.querySelectorAll('.jadwal button').forEach(btn => {
        btn.onclick = function () {
            document.querySelectorAll('.jadwal button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.getElementById('summary-jam').innerText = this.innerText;
        }
    });

    document.getElementById('plus').onclick = function () {
        durasi++;
        updateDurasi();
    }

    document.getElementById('minus').onclick = function () {
        if (durasi > 1) {
            durasi--;
            updateDurasi();
        }
    }

    function updateDurasi() {
        document.getElementById('durasi').innerText = durasi;
        document.getElementById('summary-durasi').innerText = durasi;
        hitungTotal();
    }

    document.getElementById('tanggal').onchange = function () {
        document.getElementById('summary-tanggal').innerText = this.value;
    }

    function hitungTotal() {
        let total = harga * durasi;
        document.getElementById('total').innerText = 'Rp' + format.format(total);
    }
</script>

@endsection