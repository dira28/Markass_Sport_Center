<button
    type="button"
    class="btn btn-light position-fixed d-lg-none"
    style="top: 15px; left: 15px; z-index: 1100; border-radius: 10px;"
    aria-label="Toggle sidebar"
    data-admin-sidebar-toggle
>
    <i class="bi bi-list"></i>
</button>

<div class="admin-sidebar">

    <div class="logo">

        <h5>MARKASS</h5>
        <small>Sport Center</small>
    </div>

    <ul class="menu">

        <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
            <a href="/admin/dashboard">
                <i class="bi bi-grid"></i> Dashboard
            </a>
        </li>

        <li class="{{ request()->is('admin/booking*') ? 'active' : '' }}">
            <a href="/admin/booking">
                <i class="bi bi-calendar-check"></i> Booking
            </a>
        </li>

        <li class="{{ request()->is('admin/lapangan*') ? 'active' : '' }}">
            <a href="/admin/lapangan">
                <i class="bi bi-dribbble"></i> Lapangan
            </a>
        </li>

        <li class="{{ request()->is('admin/laporan*') ? 'active' : '' }}">
            <a href="/admin/laporan">
                <i class="bi bi-bar-chart"></i> Laporan
            </a>
        </li>

        <li class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">
            <a href="{{ route('admin.profile') }}">
                <i class="bi bi-person-circle"></i> Profil
            </a>
        </li>

    </ul>

</div>