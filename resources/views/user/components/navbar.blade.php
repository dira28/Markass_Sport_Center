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
                    <a class="nav-link {{ request()->is('tentang') ? 'active' : '' }}" href="/tentang">
                        Tentang
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('my-bookings') ? 'active' : '' }}" href="/my-bookings">
                        Riwayat Booking
                    </a>
                </li>

            </ul>

            @if(session('token'))
                <a href="{{ route('profile') }}" class="ms-3">
                    <img src="https://img.freepik.com/premium-vector/default-avatar-profile-icon-social-media-user-image-gray-avatar-icon-blank-profile-silhouette-vector-illustration_561158-3485.jpg" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%;">
                </a>
            @else
                <a class="btn btn-danger btn-sm ms-3" href="{{ route('login') }}">Login</a>
            @endif

        </div>
    </div>
</nav>