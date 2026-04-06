<div class="admin-sidebar">

    <div class="logo">
        <h5>MARKASS</h5>
        <small>Sport Center</small>
    </div>

    <ul class="menu">

        <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a href="/admin/dashboard">
                <i class="bi bi-grid"></i> Dashboard
            </a>
        </li>

        <li class="{{ request()->is('admin/booking') ? 'active' : '' }}">
            <a href="/admin/booking">
                <i class="bi bi-calendar-check"></i> Booking
            </a>
        </li>

        <li class="{{ request()->is('admin/jadwal') ? 'active' : '' }}">
            <a href="/admin/jadwal">
                <i class="bi bi-clock"></i> Jadwal
            </a>
        </li>

        <li class="{{ request()->is('admin/users') ? 'active' : '' }}">
            <a href="/admin/users">
                <i class="bi bi-people"></i> Pengguna
            </a>
        </li>

        <li class="{{ request()->is('admin/laporan') ? 'active' : '' }}">
            <a href="/admin/laporan">
                <i class="bi bi-bar-chart"></i> Laporan
            </a>
        </li>

        <li class="{{ request()->is('admin/pengaturan') ? 'active' : '' }}">
            <a href="/admin/pengaturan">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
        </li>

    </ul>

</div>