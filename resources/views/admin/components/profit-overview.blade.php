<div class="card-box">

    <!-- HEADER -->
    <div class="chart-header">
        <h5>{{ $title ?? 'Profit Overview' }}</h5>

        <div class="chart-filter">
            <button type="button" class="active" onclick="updateChart('day', '{{ $id }}', this)">Harian</button>
            <button type="button" onclick="updateChart('month', '{{ $id }}', this)">Bulanan</button>
            <button type="button" onclick="updateChart('year', '{{ $id }}', this)">Tahunan</button>
        </div>
    </div>

    <!-- CHART -->
    <canvas id="chart-{{ $id }}"></canvas>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const chartData = @json($data);
        let currentType = 'day';

        const ctx = document.getElementById('chart-{{ $id }}').getContext('2d');

        function getChartStyle(type) {
            let color;

            if (type === 'day') color = '#dc3545';      
            else if (type === 'month') color = '#0d6efd'; 
            else color = '#198754'; 

            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, color + '66');
            gradient.addColorStop(1, color + '00');

            return { color, gradient };
        }

        let style = getChartStyle(currentType);

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData[currentType]?.labels || [],
                datasets: [{
                    label: 'Pendapatan',
                    data: chartData[currentType]?.data || [],
                    borderColor: style.color,
                    backgroundColor: style.gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: style.color,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });

        // global
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

        chart.data.labels = data[type]?.labels || [];
        chart.data.datasets[0].data = data[type]?.data || [];

        chart.data.datasets[0].borderColor = style.color;
        chart.data.datasets[0].backgroundColor = style.gradient;
        chart.data.datasets[0].pointBackgroundColor = style.color;

        chart.update();

        // active button
        const parent = el.parentElement;
        parent.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
        el.classList.add('active');
    }
</script>