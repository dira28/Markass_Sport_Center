<div class="card-box mt-3 p-4 bg-white rounded-3 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-bold m-0 text-dark" style="font-size: 16px;">Kategori Lapangan</h6>
            <small class="text-muted" style="font-size: 12px;">Sebaran Booking per Olahraga</small>
        </div>
    </div>

    <!-- Chart Container -->
    <div style="position: relative; height: 220px; width: 100%;">
        <canvas id="categoryDonutChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const rawCategoryData = @json($categoryData ?? []);

        // Fallback jika data dari API belum ada/kosong
        const labels = (rawCategoryData.labels && rawCategoryData.labels.length > 0)
            ? rawCategoryData.labels
            : ['Futsal', 'Badminton', 'Basketball'];

        const dataValues = (rawCategoryData.data && rawCategoryData.data.length > 0)
            ? rawCategoryData.data
            : [0, 0, 0];

        const ctxDonut = document.getElementById('categoryDonutChart').getContext('2d');

        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: [
                        '#dc3545', // Red (Primary)
                        '#0d6efd', // Blue
                        '#198754', // Green
                        '#ffc107', // Yellow
                        '#6f42c1'  // Purple
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return ' ' + label + ': ' + value + ' Booking';
                            }
                        }
                    }
                },
                cutout: '70%' // Membuat efek donut ring tipis & modern
            }
        });
    });
</script>