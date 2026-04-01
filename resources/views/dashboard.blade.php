@extends('auth.app')

@section('title', 'Dashboard')

@section('content')

<!-- HERO -->
<section class="hero">
<div class="container">

<h1>Booking Lapangan Olahraga<br>Jadi Lebih Mudah</h1>

<p>Pesan badminton & futsal secara online di Markass Sport Center Kudus.</p>

<a href="#" class="btn btn-red me-2">Booking Sekarang</a>
<a href="#" class="btn btn-light">Lihat Jadwal</a>

</div>
</section>

<!-- OLAHRAGA -->
<section class="py-5">
<div class="container text-center">

<h3 class="section-title">Olahraga Favoritmu</h3>

<div class="row justify-content-center">

<!-- BADMINTON 1 -->
<div class="col-md-3 mb-4">
<div class="card">
<img src="https://kelanakids.com/wp-content/uploads/2023/03/badminton-concept-with-racket-shuttlecock.jpg" class="img-fluid">
<div class="card-body">
<h5>Badminton Lapangan 1</h5>
<p class="price">Mulai Rp35.000 / Jam</p>
<a href="#" class="btn btn-red w-100">Booking</a>
</div>
</div>
</div>

<!-- BADMINTON 2 -->
<div class="col-md-3 mb-4">
<div class="card">
<img src="https://kelanakids.com/wp-content/uploads/2023/03/badminton-concept-with-racket-shuttlecock.jpg" class="img-fluid">
<div class="card-body">
<h5>Badminton Lapangan 2</h5>
<p class="price">Mulai Rp35.000 / Jam</p>
<a href="#" class="btn btn-red w-100">Booking</a>
</div>
</div>
</div>

<!-- FUTSAL 1 -->
<div class="col-md-3 mb-4">
<div class="card">
<img src="https://www.rukita.co/stories/wp-content/uploads/2020/02/futsal.jpg" class="img-fluid">
<div class="card-body">
<h5>Futsal Lapangan 1</h5>
<p class="price">Mulai Rp120.000 / Jam</p>
<a href="#" class="btn btn-red w-100">Booking</a>
</div>
</div>
</div>

<!-- FUTSAL 2 -->
<div class="col-md-3 mb-4">
<div class="card">
<img src="https://www.rukita.co/stories/wp-content/uploads/2020/02/futsal.jpg" class="img-fluid">
<div class="card-body">
<h5>Futsal Lapangan 2</h5>
<p class="price">Mulai Rp120.000 / Jam</p>
<a href="#" class="btn btn-red w-100">Booking</a>
</div>
</div>
</div>

</div>
</div>
</section>

<!-- KENAPA PILIH KAMI -->
<section class="py-5 bg-light">
<div class="container text-center">

<h3 class="section-title">Kenapa Pilih Kami</h3>

<div class="row">

<div class="col-md-4 mb-4">
<img src="https://cdn-icons-png.flaticon.com/512/1048/1048953.png" width="60">
<h5 class="mt-3">Lapangan Bersih</h5>
<p>Lapangan selalu dirawat dan standar nasional.</p>
</div>

<div class="col-md-4 mb-4">
<img src="https://cdn-icons-png.flaticon.com/512/747/747310.png" width="60">
<h5 class="mt-3">Booking Real-Time</h5>
<p>Booking online tanpa harus datang ke lokasi.</p>
</div>

<div class="col-md-4 mb-4">
<img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" width="60">
<h5 class="mt-3">Lokasi Strategis</h5>
<p>Berlokasi di Kudus dan mudah dijangkau.</p>
</div>

</div>
</div>
</section>

<!-- CARA BOOKING -->
<section class="py-5">
<div class="container text-center">

<h3 class="section-title">Cara Booking</h3>

<div class="row">

<div class="col-md-3 mb-3">
<img src="https://chatpajak.com/uploads/gambar/1685070801_e9e7637194821b138ec0.jpg" class="img-fluid rounded">
</div>

<div class="col-md-3 mb-3">
<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRfxXY20_x8rmexyKZDUjvUxOCAvCjjXUEwlA&s" class="img-fluid rounded">
</div>

<div class="col-md-3 mb-3">
<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSk1_hOPsjtoX6vVVOWJqJe5i04mpHkMHiI4w&s" class="img-fluid rounded">
</div>

<div class="col-md-3 mb-3">
<img src="https://media.istockphoto.com/id/1761333789/photo/badminton-shuttlecocks-and-racket-placed-in-the-corner-of-a-synthetic-field.jpg" class="img-fluid rounded">
</div>

</div>
</div>
</section>

<!-- TESTIMONI -->
<section class="py-5 bg-light">
<div class="container text-center">

<h3 class="section-title">Testimoni</h3>

<div class="row">

<div class="col-md-4 mb-4">
<img src="https://randomuser.me/api/portraits/men/32.jpg" width="70" class="rounded-circle mb-2">
<h6>Andi Pratama</h6>
<p>Lapangan sangat bagus dan bersih.</p>
</div>

<div class="col-md-4 mb-4">
<img src="https://randomuser.me/api/portraits/women/44.jpg" width="70" class="rounded-circle mb-2">
<h6>Siti Rahmawati</h6>
<p>Booking gampang dan cepat.</p>
</div>

<div class="col-md-4 mb-4">
<img src="https://randomuser.me/api/portraits/men/65.jpg" width="70" class="rounded-circle mb-2">
<h6>Budi Santoso</h6>
<p>Tempat olahraga terbaik di Kudus.</p>
</div>

</div>

<a href="#" class="btn btn-red mt-3">Booking Sekarang</a>

</div>
</section>

@endsection