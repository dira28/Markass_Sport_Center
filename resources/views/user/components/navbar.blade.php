<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&display=swap');

    .custom-navbar {
        font-family: 'Plus Jakarta Sans', sans-serif;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        z-index: 9999;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .navbar-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 16px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Brand Logo & Title */
    .brand-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        gap: 14px;
    }

    .brand-logo {
        max-width: 46px;
        height: auto;
    }

    .brand-title {
        font-weight: 700;
        font-size: 19px;
        color: #111827;
        letter-spacing: 0.3px;
    }

    /* Navigation Links */
    .nav-menu-wrapper {
        display: flex;
        align-items: center;
        gap: 48px;
    }

    .nav-links-list {
        display: flex;
        align-items: center;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 32px;
    }

    .nav-item-link {
        text-decoration: none;
        color: #4b5563;
        font-weight: 600;
        font-size: 16px;
        transition: color 0.2s ease;
    }

    .nav-item-link:hover,
    .nav-item-link.active {
        color: #dc2626;
        font-weight: 700;
    }

    /* User Profile & Auth */
    .auth-action {
        display: flex;
        align-items: center;
    }

    .profile-img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .login-btn {
        background-color: #dc2626;
        color: #ffffff;
        padding: 9px 22px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        transition: background-color 0.2s ease;
    }

    .login-btn:hover {
        background-color: #b91c1c;
    }

    /* Hamburger Menu (Mobile) */
    .hamburger-btn {
        display: none;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 6px;
    }

    .hamburger-icon {
        width: 24px;
        height: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .hamburger-icon span {
        display: block;
        height: 2.5px;
        width: 100%;
        background-color: #111827;
        border-radius: 2px;
    }

    .navbar-hidden {
        transform: translateY(-100%);
    }

    body {
        padding-top: 90px;
    }

    /* Responsive Mobile Layout */
    @media (max-width: 991px) {
        .hamburger-btn {
            display: block;
        }

        .nav-menu-wrapper {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            flex-direction: column;
            align-items: flex-start;
            padding: 20px 28px;
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.08);
            border-top: 1px solid #f3f4f6;
            gap: 20px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.25s ease-in-out;
        }

        .nav-menu-wrapper.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .nav-links-list {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
            gap: 16px;
        }

        .nav-links-list li {
            width: 100%;
        }

        .nav-item-link {
            display: block;
            width: 100%;
        }

        .auth-action {
            width: 100%;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
        }
    }
</style>

<nav class="custom-navbar">
    <div class="navbar-container">

        <!-- Brand Logo -->
        <a class="brand-link" href="{{ url('/') }}">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTUS3cLEY3Wpkum4hAwNZAj_mFyL6q7HQbm6Q&s"
                alt="Logo Markass" class="brand-logo">
            <span class="brand-title">MARKASS SPORT CENTER</span>
        </a>

        <!-- Mobile Hamburger Toggle -->
        <button class="hamburger-btn" id="menuToggleBtn" aria-label="Toggle Menu">
            <div class="hamburger-icon">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </button>

        <!-- Navigation Links -->
        <div class="nav-menu-wrapper" id="navMenuContainer">
            <ul class="nav-links-list">
                <li>
                    <a class="nav-item-link {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">Home</a>
                </li>
                <li>
                    <a class="nav-item-link {{ request()->is('booking') ? 'active' : '' }}" href="/booking">Booking</a>
                </li>
                <li>
                    <a class="nav-item-link {{ request()->is('tentang') ? 'active' : '' }}" href="/tentang">Tentang</a>
                </li>
                <li>
                    <a class="nav-item-link {{ request()->is('my-bookings') ? 'active' : '' }}"
                        href="/my-bookings">Riwayat Booking</a>
                </li>
            </ul>

            <!-- Auth Action -->
            <div class="auth-action">
                @if(session('token'))
                    <a href="{{ route('profile') }}" style="display: flex; align-items: center;">
                        <img src="https://img.freepik.com/premium-vector/default-avatar-profile-icon-social-media-user-image-gray-avatar-icon-blank-profile-silhouette-vector-illustration_561158-3485.jpg"
                            alt="Profile" class="profile-img">
                    </a>
                @else
                    <a class="login-btn" href="{{ route('login') }}">Login</a>
                @endif
            </div>
        </div>

    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("menuToggleBtn");
        const menuContainer = document.getElementById("navMenuContainer");
        const navbar = document.querySelector(".custom-navbar");

        // Toggle mobile menu
        if (toggleBtn && menuContainer) {
            toggleBtn.addEventListener("click", function (e) {
                e.stopPropagation();
                menuContainer.classList.toggle("open");
            });

            document.addEventListener("click", function (e) {
                if (!menuContainer.contains(e.target) && !toggleBtn.contains(e.target)) {
                    menuContainer.classList.remove("open");
                }
            });
        }

        // Hide/Show navbar on scroll
        let lastScrollTop = 0;
        window.addEventListener("scroll", function () {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > 120) {
                if (scrollTop > lastScrollTop) {
                    navbar.classList.add("navbar-hidden");
                    if (menuContainer) menuContainer.classList.remove("open");
                } else {
                    navbar.classList.remove("navbar-hidden");
                }
            } else {
                navbar.classList.remove("navbar-hidden");
            }
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        });
    });
</script>