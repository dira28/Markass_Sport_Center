<nav class="admin-navbar">

    <div class="nav-left">
        <img src="/images/logo-markass.png" class="logo-img" alt="Markass Logo">
        <h5>Markass Sport Center</h5>
    </div>

    <div class="nav-right">
        <a href="{{ route('admin.profile') }}" class="profile-box text-decoration-none">

            <div class="avatar-circle-sm">
                {{ strtoupper(substr(session('user.nama') ?? 'A', 0, 1)) }}
            </div>

            <span class="text-dark">
                {{ session('user.nama') ?? 'Admin' }}
            </span>

        </a>
    </div>

</nav>