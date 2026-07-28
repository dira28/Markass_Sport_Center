<div class="row g-3">

    {{-- TOTAL PENDAPATAN --}}
    <div class="col-md-3">
        <div class="kpi-card kpi-green">
            <div class="kpi-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <p>Total Pendapatan</p>
                <h4>Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h4>
                @if(isset($growthRevenue))
                    <small class="{{ $growthRevenue >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $growthRevenue >= 0 ? '↑ +' : '↓ ' }}{{ $growthRevenue }}%
                    </small>
                @else
                    <small class="text-muted">Akumulasi Seluruhnya</small>
                @endif
            </div>
        </div>
    </div>

    {{-- TOTAL BOOKING --}}
    <div class="col-md-3">
        <div class="kpi-card kpi-blue">
            <div class="kpi-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p>Total Booking</p>
                <h4>{{ $totalBooking ?? 0 }}</h4>
                @if(isset($growthBooking))
                    <small class="{{ $growthBooking >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $growthBooking >= 0 ? '↑ +' : '↓ ' }}{{ $growthBooking }}%
                    </small>
                @else
                    <small class="text-muted">Semua Transaksi</small>
                @endif
            </div>
        </div>
    </div>

    {{-- TOTAL PENGGUNA --}}
    <div class="col-md-3">
        <div class="kpi-card kpi-yellow">
            <div class="kpi-icon">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p>Total Pengguna</p>
                <h4>{{ $totalUser ?? 0 }}</h4>
                @if(isset($growthUser))
                    <small class="{{ $growthUser >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $growthUser >= 0 ? '↑ +' : '↓ ' }}{{ $growthUser }}%
                    </small>
                @else
                    <small class="text-muted">Pelanggan Terdaftar</small>
                @endif
            </div>
        </div>
    </div>

    {{-- RATA-RATA / HARI --}}
    <div class="col-md-3">
        <div class="kpi-card kpi-red">
            <div class="kpi-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <p>Rata-rata / Hari</p>
                <h4>Rp {{ number_format($averagePerDay ?? 0, 0, ',', '.') }}</h4>
                <small class="text-muted">Estimasi Harian</small>
            </div>
        </div>
    </div>

</div>