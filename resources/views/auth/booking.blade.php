@extends('auth.app')

@section('title', 'Booking')

@section('content')

<style>
.booking-hero {
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
    url('https://images.unsplash.com/photo-1556056504-5c7696c4c28d');
    background-size: cover;
    background-position: center;
    height: 300px;
    display: flex;
    align-items: center;
    color: white;
}

.booking-card img {
    height: 200px;
    object-fit: cover;
}

.booking-card {
    border-radius: 15px;
    overflow: hidden;
    transition: 0.3s;
}

.booking-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.booking-form-section {
    background: #c62828;
    padding: 60px 0;
}

.booking-form-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    max-width: 800px;
    margin: auto;
}

input, select {
    border-radius: 8px !important;
}
</style>

<!-- HERO -->
<section class="booking-hero">
<div class="container text-center">
<h1 class="fw-bold">Booking Lapangan</h1>
<p>Pilih lapangan, tanggal, dan jam bermain dengan mudah.</p>
</div>
</section>

<!-- PILIH LAPANGAN -->
<section class="py-5 bg-light">
<div class="container text-center">

<h3 class="section-title">Pilih Lapangan</h3>

<div class="row justify-content-center">

<!-- FUTSAL -->
<div class="col-md-5 mb-4">
<div class="card booking-card shadow-sm">
<img src="https://www.rukita.co/stories/wp-content/uploads/2020/02/futsal.jpg">
<div class="card-body">
<h5>Lapangan Futsal</h5>
<p class="text-muted">Mulai Rp120.000 / Jam</p>
<a href="#" class="btn btn-red px-4">Pilih</a>
</div>
</div>
</div>

<!-- BADMINTON -->
<div class="col-md-5 mb-4">
<div class="card booking-card shadow-sm">
<img src="https://kelanakids.com/wp-content/uploads/2023/03/badminton-concept-with-racket-shuttlecock.jpg">
<div class="card-body">
<h5>Lapangan Badminton</h5>
<p class="text-muted">Mulai Rp35.000 / Jam</p>
<a href="#" class="btn btn-red px-4">Pilih</a>
</div>
</div>
</div>

</div>
</div>
</section>

<!-- FORM BOOKING -->
<section class="booking-form-section">
<div class="container">

<h3 class="text-center text-white mb-4 fw-bold">Form Booking</h3>

<div class="booking-form-card shadow">

<form>

<div class="row">

<div class="col-md-6 mb-3">
<label class="fw-semibold">Nama</label>
<input type="text" class="form-control" placeholder="Masukkan nama">
</div>

<div class="col-md-6 mb-3">
<label class="fw-semibold">Tanggal</label>
<input type="date" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="fw-semibold">Jam</label>
<select class="form-control">
<option>08.00 - 09.00</option>
<option>09.00 - 10.00</option>
<option>10.00 - 11.00</option>
</select>
</div>

<div class="col-md-6 mb-3">
<label class="fw-semibold">Pilih Lapangan</label>
<select class="form-control">
<option>Futsal</option>
<option>Badminton</option>
</select>
</div>

</div>

<button class="btn btn-red w-100 mt-3">Booking Sekarang</button>

</form>

</div>
</div>
</section>

<!-- TAMBAHAN DESIGN -->
<section class="py-5 bg-light">
<div class="container text-center">

<h3 class="section-title">Kenapa Booking di Sini?</h3>

<div class="row">

<div class="col-md-4 mb-4">
<h5>⚡ Cepat & Mudah</h5>
<p>Booking hanya dalam hitungan detik tanpa ribet.</p>
</div>

<div class="col-md-4 mb-4">
<h5>📍 Lokasi Strategis</h5>
<p>Mudah dijangkau dan dekat pusat kota.</p>
</div>

<div class="col-md-4 mb-4">
<h5>💯 Lapangan Berkualitas</h5>
<p>Standar terbaik untuk kenyamanan bermain.</p>
</div>

</div>
</div>
</section>

@endsection