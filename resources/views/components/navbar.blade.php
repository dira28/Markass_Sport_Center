<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTUS3cLEY3Wpkum4hAwNZAj_mFyL6q7HQbm6Q&s"
                 alt="Logo Markass"
                 style="max-width:45px;">

            <span class="ms-2">
                MARKASS SPORT CENTER
            </span>

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

                <!-- ✅ SUDAH DIPERBAIKI -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('harga') ? 'active' : '' }}" href="/harga">
                        Tentang
                    </a>
                </li>

            </ul>

            <button class="btn btn-danger btn-sm">Login</button>

        </div>
    </div>
</nav>