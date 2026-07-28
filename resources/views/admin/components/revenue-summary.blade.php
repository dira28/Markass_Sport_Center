<div class="card-box summary-box">

    <h4>
        Rp {{ number_format($revenueToday ?? 0, 0, ',', '.') }}
    </h4>

    <p class="{{ ($totalBooking ?? 0) > 0 ? 'text-success' : 'text-muted' }}">
        {{ $totalBooking ?? 0 }} Booking Hari Ini
    </p>

    <small class="text-muted">Realtime Data Hari Ini</small>

</div>