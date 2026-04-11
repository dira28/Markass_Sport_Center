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
    const chartData_{{ $id }} = @json($data);

    let currentType_{{ $id }} = 'day';

    const ctx_{{ $id }} = document.getElementById('chart-{{ $id }}').getContext('2d');

    const gradient_{{ $id }} = ctx_{{ $id }}.createLinearGradient(0, 0, 0, 300);
    gradient_{{ $id }}.addColorStop(0, "rgba(220,53,69,0.4)");
    gradient_{{ $id }}.addColorStop(1, "rgba(220,53,69,0)");

    let chart_{{ $id }} = new Chart(ctx_{{ $id }}, {
        type: 'line',
        data: {
            labels: chartData_{{ $id }}[currentType_{{ $id }}].labels,
            datasets: [{
                datasets: [{
                    label: 'Pendapatan',
                    data: chartData_{{ $id }}[currentType_{{ $id }}].data,
                    borderColor: '#dc3545',
                    backgroundColor: gradient_{{ $id }},
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#dc3545',
                    pointRadius: 4
                }]
            }]
        },
        options: {
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false },
                    x: { display: true }
                },
                elements: {
                    line: { borderWidth: 2 }
                }
            }
        }
    });

    function updateChart(type, id, el) {
        const chartVar = window['chart_' + id];
        const dataVar = window['chartData_' + id];

        chartVar.data.labels = dataVar[type].labels;
        chartVar.data.datasets[0].data = dataVar[type].data;
        chartVar.update();

        document.querySelectorAll('.chart-filter button').forEach(btn => {
            btn.classList.remove('active');
        });

        el.classList.add('active');
    }

    // global access
    window['chart_' + '{{ $id }}'] = chart_{{ $id }};
    window['chartData_' + '{{ $id }}'] = chartData_{{ $id }};
</script>