<div class="row g-3 mt-2">

    <div class="col-md-6">
        <div class="revenue-card">
            <p>Pendapatan</p>
            <h3 class="text-danger">
                Rp {{ number_format($revenueToday, 0, ',', '.') }}
            </h3>
            <span class="badge bg-success">
                {{ $totalBooking }} Booking
            </span>
        </div>
    </div>

    <div class="col-md-6">
        <div class="revenue-card">
            <p>Total Booking</p>
            <h3>{{ $totalBooking }}</h3>
        </div>
    </div>

</div>