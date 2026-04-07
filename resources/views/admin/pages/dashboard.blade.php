@extends('layouts.admin')

@section('content')

<div class="dashboard-header-v2">
    <div>
        <h3>Halo Admin!</h3>
        <p>Selamat datang di Dashboard Markass Sport Center</p>
    </div>
</div>

<!-- REVENUE -->
<div class="row g-3 mt-2">

    <div class="col-md-6">
        <div class="revenue-card big">
            <p>Pendapatan Hari Ini</p>
            <h3 class="text-danger">
                Rp {{ number_format($revenueToday, 0, ',', '.') }}
            </h3>
            <span class="badge bg-success">
                {{ $totalBooking }} Booking
            </span>
        </div>
    </div>

    <div class="col-md-6">
        <div class="revenue-card big">
            <p>Total Booking Hari Ini</p>
            <h3>{{ $totalBooking }}</h3>
        </div>
    </div>

</div>

<!-- CHART -->
<div class="row mt-4">

    <div class="col-md-8">
        <div class="card-box">
            <h5>Profit Overview</h5>
            <canvas id="chart"></canvas>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-box summary-box">
            <h4>Rp {{ number_format($revenueToday, 0, ',', '.') }}</h4>
            <p class="text-success">{{ $totalBooking }} Booking Hari Ini</p>
            <small>Realtime Data</small>
        </div>
    </div>

</div>

<!-- LATEST BOOKING -->
<div class="row mt-4">

    <div class="col-md-12">
        <div class="card-box">
            <h5>Latest Bookings</h5>

            <table class="table">
                @forelse($latestBookings as $item)
                    <tr>
                        <td>{{ $item['id_booking'] }}</td>
                        <td>{{ $item['user']['nama'] ?? '-' }}</td>
                        <td>{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Tidak ada data</td>
                    </tr>
                @endforelse
            </table>

        </div>
    </div>

</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = @json($chartLabels);
    const dataValues = @json($chartValues);

    const ctx = document.getElementById('chart').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, "rgba(220,53,69,0.4)");
    gradient.addColorStop(1, "rgba(220,53,69,0)");

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: dataValues,
                borderColor: '#dc3545',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false },
                x: { display: true }
            }
        }
    });
</script>

@endsection