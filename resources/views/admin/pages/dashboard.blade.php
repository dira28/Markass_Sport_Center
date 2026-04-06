@extends('layouts.admin')

@section('content')

    <!-- ===================== -->
    <!-- HEADER -->
    <!-- ===================== -->
    <div class="dashboard-header-v2">

        <div>
            <h3>Halo Admin!</h3>
            <p>Selamat datang di Dashboard Markass Sport Center</p>
        </div>

        <button class="btn-download">
            <i class="bi bi-download"></i> Unduh Laporan
        </button>

    </div>

    <!-- DATE RANGE -->
    <div class="date-range-box mt-3">
        <i class="bi bi-calendar"></i>
        <span>18 Apr 2024 - 22 Apr 2024</span>
        <i class="bi bi-chevron-down"></i>
    </div>

    <!-- ===================== -->
    <!-- REVENUE CARD -->
    <!-- ===================== -->
    <div class="row g-3 mt-2">

        <div class="col-md-6">
            <div class="revenue-card big">
                <p>Pendapatan Hari Ini</p>
                <h3 class="text-danger">Rp750.000</h3>
                <span class="badge bg-success">+20%</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="revenue-card big">
                <p>Pendapatan Bulan Ini</p>
                <h3>Rp12.600.000</h3>
                <span class="badge bg-success">+15%</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="revenue-card small">
                <p>Pendapatan Bulan Ini</p>
                <span class="badge bg-success">+15%</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="revenue-card small">
                <p>Total Pengguna</p>
                <h4>1.254</h4>
            </div>
        </div>

    </div>

    <!-- ===================== -->
    <!-- CHART + SUMMARY -->
    <!-- ===================== -->
    <div class="row mt-4">

        <div class="col-md-8">
            <div class="card-box">

                <div class="chart-header">
                    <h5>Profit Overview</h5>

                    <div class="chart-filter">
                        <button onclick="loadChart('daily')">Harian</button>
                        <button onclick="loadChart('weekly')">Mingguan</button>
                        <button onclick="loadChart('monthly')">Bulanan</button>
                        <button onclick="loadChart('yearly')">Tahunan</button>
                    </div>
                </div>

                <canvas id="chart"></canvas>

            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box summary-box">
                <h4>Rp14.875.000</h4>
                <p class="text-success">+15.3%</p>

                <small>+ 2.5% Booking</small><br>
                <small>235 Booking</small>
            </div>
        </div>

    </div>

    <!-- ===================== -->
    <!-- TABLE + FASILITAS -->
    <!-- ===================== -->
    <div class="row mt-4">

        <!-- TABLE -->
        <div class="col-md-7">
            <div class="card-box">
                <h5>Latest Bookings</h5>

                <table class="table">
                    <tr>
                        <td>BK-00012</td>
                        <td>Andi</td>
                        <td>Badminton</td>
                        <td>22 Apr</td>
                    </tr>
                </table>

                <button class="btn-show">Show All</button>
            </div>
        </div>

        <!-- FASILITAS -->
        <div class="col-md-5">
            <div class="card-box">
                <h5>Manage Fasilitas</h5>

                <div class="facility-item">
                    <img src="/images/futsal.jpg">
                    <div>
                        <p>Futsal</p>
                        <small>Rp160.000 / jam</small>
                    </div>
                </div>

                <button class="btn-filter w-100 mt-2">
                    Kelola Fasilitas
                </button>

            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let chart;

        function createGradient(ctx) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, "rgba(220,53,69,0.4)");
            gradient.addColorStop(1, "rgba(220,53,69,0)");
            return gradient;
        }

        const dataChart = {
            daily: [5, 10, 8, 15, 12],
            weekly: [20, 30, 25, 40, 35],
            monthly: [200, 300, 250, 400, 350],
            yearly: [2000, 3000, 2500, 4000, 3500]
        };

        function loadChart(type) {
            const ctx = document.getElementById('chart').getContext('2d');

            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
                    datasets: [{
                        data: dataChart[type],
                        borderColor: '#dc3545',
                        backgroundColor: createGradient(ctx),
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#dc3545',
                        pointRadius: 4
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

            // ACTIVE BUTTON
            document.querySelectorAll('.chart-filter button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        loadChart('monthly');
    </script>

@endsection