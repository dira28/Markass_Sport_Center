@extends('layouts.app')

@section('title', 'Harga')

@section('content')

<div class="container py-5">

    <!-- TITLE -->
    <h2 class="text-center mb-5 fw-bold">Harga Sewa Lapangan</h2>

    <div class="row">

        <!-- KIRI (LIST HARGA) -->
        <div class="col-lg-8">

            <!-- BADMINTON -->
            <div class="card harga-box mb-4 p-3"
                 onclick="pilihLapangan('Badminton', '08.00 - 16.00', 50000)">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Badminton</h5>
                        <small>08.00 - 16.00</small>
                    </div>
                    <span class="text-danger fw-bold">Rp50.000 / jam</span>
                </div>
            </div>

            <!-- FUTSAL -->
            <div class="card harga-box mb-4 p-3"
                 onclick="pilihLapangan('Futsal', '16.00 - 22.00', 120000)">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Futsal</h5>
                        <small>16.00 - 22.00</small>
                    </div>
                    <span class="text-danger fw-bold">Rp120.000 / jam</span>
                </div>
            </div>

        </div>

        <!-- KANAN (RINGKASAN) -->
        <div class="col-lg-4">

            <div class="card summary-card p-4">

                <h5 class="mb-3">Ringkasan Booking</h5>

                <p class="mb-1">Lapangan: <b id="lapangan">-</b></p>
                <p class="mb-1">Tanggal: <span id="tanggal">-</span></p>
                <p class="mb-3">Jam: <span id="jam">-</span></p>

                <hr>

                <div class="d-flex justify-content-between">
                    <span>Tarif</span>
                    <span id="tarif">Rp0</span>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Extra</span>
                    <span id="extra">Rp0</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span id="total">Rp0</span>
                </div>

                <button class="btn btn-danger w-100 mt-3">
                    Lanjut Pembayaran
                </button>

            </div>

        </div>

    </div>

</div>

@endsection

<script>
function pilihLapangan(nama, jam, harga) {

    let extra = 10000;
    let total = harga + extra;

    document.getElementById('lapangan').innerText = nama;
    document.getElementById('jam').innerText = jam;
    document.getElementById('tanggal').innerText = new Date().toLocaleDateString();

    document.getElementById('tarif').innerText = 'Rp' + harga.toLocaleString();
    document.getElementById('extra').innerText = 'Rp' + extra.toLocaleString();
    document.getElementById('total').innerText = 'Rp' + total.toLocaleString();
}
</script>