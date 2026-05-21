<style>
    .navbar {
        padding-top: 18px;
        padding-bottom: 18px;
        transition: top 0.3s;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 999;
    }

    .navbar-hide {
        top: -100px;
    }

    body {
        padding-top: 90px;
    }

    /* TAMBAHAN FONT */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap');

    .brand-text {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 18px;
    }
</style>

<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">

            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTUS3cLEY3Wpkum4hAwNZAj_mFyL6q7HQbm6Q&s"
                alt="Logo Markass" style="max-width:45px;">

            <!-- HANYA TAMBAH CLASS -->
            <span class="ms-2 brand-text">
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

                <div class="dropdown ms-3">

                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown">

                        <img src="https://ui-avatars.com/api/?name={{ urlencode(session('user.name') ?? 'User') }}&background=0D8ABC&color=fff"
                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;">

                        <span class="ms-2 d-none d-md-inline">
                            {{ session('user.name') ?? 'User' }}
                        </span>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fa fa-user me-2"></i> Profile
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                <i class="fa fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>

                    </ul>
                </div>

            @else

                <a class="btn btn-danger btn-sm ms-3" href="{{ route('login') }}">
                    Login
                </a>

            @endif

        </div>
    </div>
</nav>

<script>
    let lastScrollTop = 0;
    const navbar = document.querySelector(".navbar");

    window.addEventListener("scroll", function () {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop) {
            navbar.classList.add("navbar-hide");
        } else {
            navbar.classList.remove("navbar-hide");
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    });
</script>