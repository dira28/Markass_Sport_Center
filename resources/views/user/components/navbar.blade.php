<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Mengatur style dasar navigasi agar kontras */
    .navbar {
        background-color: #ffffff !important;
        transition: transform 0.3s ease-in-out;
    }


    /* Animasi menu mobile lebih halus */
.navbar-collapse {
    transition: all 0.5s ease-in-out;
}
    
    /* CSS efek scroll hide bawaan kamu */
    .navbar-hide {
        transform: translateY(-100%);
    }

    .navbar-nav .nav-link {
        color: #222222 !important;
        font-weight: 600;
        padding: 8px 16px !important;
        transition: 0.2s;
    }
    
    .navbar-nav .nav-link:hover, 
    .navbar-nav .nav-link.active {
        color: #e53935 !important;
    }
    
    .brand-text {
        color: #111111;
        font-weight: 700;
        margin-bottom: 0;
        display: inline-block;
        vertical-align: middle;
    }

    /* 🔥 SUPER FORCE FIX: Paksa menu kanan jebol keluar di layar Komputer/Laptop */
    @media (min-width: 992px) {
        .collapse.navbar-collapse {
            display: flex !important;
            height: auto !important;
            position: static !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    }
</style>

<nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTUS3cLEY3Wpkum4hAwNZAj_mFyL6q7HQbm6Q&s"
                 alt="Logo Markass"
                 style="max-width: 45px; height: auto;">
            <span class="ms-2 brand-text">
                MARKASS SPORT CENTER
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav me-3 align-items-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}" href="/dashboard">
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
            
            <a href="/login" class="btn btn-danger px-4" style="background: linear-gradient(135deg, #e53935, #c62828); border: none; font-weight: 700; border-radius: 50px; padding-top: 8px; padding-bottom: 8px;">
                Login
            </a>
        </div>

    </div>
</nav>

<div style="margin-top: 0px;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let lastScrollTop = 0;
    const navbar = document.querySelector(".navbar");

    // Delay hide navbar saat scroll
    let hideTimeout;

    window.addEventListener("scroll", function () {

        clearTimeout(hideTimeout);

        hideTimeout = setTimeout(() => {

            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > lastScrollTop && scrollTop > 90) {
                navbar.classList.add("navbar-hide");
            } else {
                navbar.classList.remove("navbar-hide");
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 

        }, 500); // navbar baru hilang setelah 0.5 detik

    });

    // Delay menu mobile sebelum menutup
    document.addEventListener('DOMContentLoaded', function () {

        const navLinks = document.querySelectorAll('#navbarNav .nav-link');
        const navbarCollapse = document.getElementById('navbarNav');

        navLinks.forEach(link => {

            link.addEventListener('click', function (e) {

                // hanya berlaku di mobile
                if (window.innerWidth < 992) {

                    e.preventDefault();

                    const href = this.getAttribute('href');

                    // menu tetap tampil 1 detik
                    setTimeout(() => {

                        const bsCollapse =
                            bootstrap.Collapse.getOrCreateInstance(navbarCollapse);

                        bsCollapse.hide();
                        
                        // tunggu animasi selesai lalu pindah halaman
                        setTimeout(() => {
                            window.location.href = href;
                        }, 300);

                    }, 3000);

                }

            });

        });

    });
</script>