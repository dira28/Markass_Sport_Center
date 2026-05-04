@extends('layouts.app')

@section('title', 'Booking')

<script>
    window.isLoggedIn = {{ session('token') ? 'true' : 'false' }};
</script>

@push('styles')
    @vite([
        'resources/css/user/pages/booking.css',
        'resources/css/user/pages/payment.css',
        'resources/css/user/components/lapangan-list.css',
        'resources/css/user/components/booking-summary.css'
    ])
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

                <div class="col-md-8">
                    @include('user.components.lapangan-list')
                </div>

                <div class="col-md-4">
                    @include('user.components.booking-summary')
                </div>

            </div>
        </div>
    </section>

    @vite('resources/js/booking.js')

@endsection