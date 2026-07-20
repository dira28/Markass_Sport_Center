<div class="row g-3">

    <div class="col-md-3">
        <div class="kpi-card kpi-green">
            <div class="kpi-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <p>Total Pendapatan</p>
                <h4>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                <small class="text-success">↑ +12%</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-card kpi-blue">
            <div class="kpi-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p>Total Booking</p>
                <h4>{{ $totalBooking }}</h4>
                <small class="text-success">↑ +8%</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-card kpi-yellow">
            <div class="kpi-icon">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p>Total Pengguna</p>
                <h4>{{ $totalUser }}</h4>
                <small class="text-success">↑ +10%</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-card kpi-red">
            <div class="kpi-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <p>Rata-rata / Hari</p>
                <h4>Rp {{ number_format($averagePerDay, 0, ',', '.') }}</h4>
                <small class="text-success">↑ +7%</small>
            </div>
        </div>
    </div>

</div>