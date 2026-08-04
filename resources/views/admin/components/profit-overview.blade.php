<div class="card-box profit-card">

    <!-- HEADER -->
    <div class="chart-header">
        <div>
            <h5>{{ $title ?? 'Profit Overview' }}</h5>
            <small class="text-muted">Tracking Transaksi & Performa Pendapatan</small>
        </div>

        <div class="chart-filter">
            <button type="button" class="active" onclick="updateChart('day', '{{ $id }}', this)">Harian</button>
            <button type="button" onclick="updateChart('month', '{{ $id }}', this)">Bulanan</button>
            <button type="button" onclick="updateChart('year', '{{ $id }}', this)">Tahunan</button>
        </div>
    </div>

    <!-- CHART CONTAINER -->
    <div class="chart-container">
        <canvas id="chart-{{ $id }}"></canvas>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const chartData = @json($data);
        let currentType = 'day';

        const ctx = document.getElementById('chart-{{ $id }}').getContext('2d');

        function getChartStyle(type) {
            let color;

            if (type === 'day') color = '#dc3545';     // Primary Red
            else if (type === 'month') color = '#2563eb'; // Royal Blue
            else color = '#059669';                   // Emerald Green

            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, color + '40'); // 25% opacity
            gradient.addColorStop(1, color + '00'); // Transparent

            return { color, gradient };
        }

        let style = getChartStyle(currentType);

        // Calculate average baseline for comparison line
        const currentDataset = chartData[currentType]?.data || [];
        const avgValue = currentDataset.length > 0
            ? currentDataset.reduce((a, b) => a + b, 0) / currentDataset.length
            : 0;

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData[currentType]?.labels || [],
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: currentDataset,
                        borderColor: style.color,
                        backgroundColor: style.gradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: style.color,
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Rata-Rata Acuan',
                        data: (chartData[currentType]?.labels || []).map(() => avgValue),
                        borderColor: '#cbd5e1',
                        borderDash: [6, 6],
                        fill: false,
                        tension: 0,
                        borderWidth: 2,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            usePointStyle: true,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                let val = context.raw || 0;
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 11 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { size: 11 },
                            callback: function (value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'M';
                                if (value >= 1000) return 'Rp ' + (value / 1000) + 'k';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });

        // Global registration
        window['chart_{{ $id }}'] = chart;
        window['chartData_{{ $id }}'] = chartData;
        window['getChartStyle_{{ $id }}'] = getChartStyle;

    });
</script>

<script>
    function updateChart(type, id, el) {

        const chart = window['chart_' + id];
        const data = window['chartData_' + id];
        const getStyle = window['getChartStyle_' + id];

        if (!chart || !data) return;

        const style = getStyle(type);
        const newDataset = data[type]?.data || [];
        const newLabels = data[type]?.labels || [];

        // Recalculate baseline average for new filter
        const newAvg = newDataset.length > 0
            ? newDataset.reduce((a, b) => a + b, 0) / newDataset.length
            : 0;

        chart.data.labels = newLabels;

        // Primary Dataset (Revenue)
        chart.data.datasets[0].data = newDataset;
        chart.data.datasets[0].borderColor = style.color;
        chart.data.datasets[0].backgroundColor = style.gradient;
        chart.data.datasets[0].pointBorderColor = style.color;

        // Baseline Dataset (Average)
        chart.data.datasets[1].data = newLabels.map(() => newAvg);

        chart.update();

        // Active Button Styling
        const parent = el.parentElement;
        parent.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
        el.classList.add('active');
    }
</script>