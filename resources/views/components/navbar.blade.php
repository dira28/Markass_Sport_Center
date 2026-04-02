<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand" href="{{ url('/') }}">
            MARKASS SPORT CENTER
        </a>

        <!-- TOGGLER -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- MENU -->
        <div class="navbar-collapse justify-content-end show">

            <ul class="navbar-nav me-3">

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('booking') ? 'active' : '' }}" href="/booking">
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