<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Markass Sport Center')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background: #f4f6f9;
        font-family: 'Segoe UI', sans-serif;
    }

    /* NAVBAR */
    .navbar-brand {
        font-weight: 700;
        color: #b71c1c !important;
    }

    .nav-link {
        font-weight: 600;
    }

    .nav-link:hover {
        color: #b71c1c !important;
    }

    .nav-link.active {
        color: #b71c1c !important;
    }

    /* HERO */
    .hero {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
        url('https://images.unsplash.com/photo-1574629810360-7efbbe195018');
        background-size: cover;
        background-position: center;
        height: 500px;
        display: flex;
        align-items: center;
        color: white;
    }

    .hero h1 {
        font-size: 42px;
        font-weight: 700;
    }

    /* BUTTON */
    .btn-red {
        background: #c62828;
        color: white;
        border-radius: 8px;
        padding: 10px 20px;
    }

    .btn-red:hover {
        background: #8e0000;
    }

    /* SECTION */
    .section-title {
        font-weight: 700;
        margin-bottom: 40px;
    }

    /* CARD */
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: 0.3s;
    }

    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .price {
        color: #c62828;
        font-weight: 600;
    }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">

        <a class="navbar-brand" href="{{ url('/') }}">MARKASS SPORT CENTER</a>

        <!-- TOGGLER (HP) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- MENU -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav me-3">

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('booking') ? 'active' : '' }}" href="{{ url('/booking') }}">
                        Booking
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Harga</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Tentang</a>
                </li>

            </ul>

            <span class="me-3">Hi, Admin</span>
            <button class="btn btn-danger btn-sm">Logout</button>
        </div>

    </div>
</nav>

<!-- HEADER -->
<div class="bg-white shadow-sm py-3">
    <div class="container">
        <h4 class="mb-0">@yield('title')</h4>
    </div>
</div>

<!-- CONTENT -->
@yield('content')

<!-- Bootstrap JS (WAJIB untuk toggle) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>