<div class="modal fade" id="HoursChartModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Diensten Vergelijken</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style=" overflow-y: auto;">
                    <canvas id="myChart" class="chartdiensten"></canvas>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
        </div>
    </div>
</div>
<style>
    .chartdiensten {
        width: 100% !important;
        height: 100% !important;
    }
    .chart-container {
        position: relative;
        height: 100vh; /* Full viewport height */
        width: 100%;
    }
</style>
<script>
    @php
        $output = [];
        foreach($parents as $parent) {
            $output[] = [
                'displayName' => $parent->displayName(),
                'hours' => $parent->count_hours_per_shift_category
            ];
        }
    @endphp
    const rawData = @json($output);
    const labels = rawData.map(data => data.displayName);
    const familiarColors = [
        'rgba(255, 99, 132, 1)', // Red
        'rgba(54, 162, 235, 1)', // Blue
        'rgba(255, 206, 86, 1)', // Yellow
        'rgba(75, 192, 192, 1)', // Green
        'rgba(153, 102, 255, 1)', // Purple
        'rgba(255, 159, 64, 1)'  // Orange
    ];
    const datasets = Object.keys(rawData[0].hours).map((key, index) => {
        const color = familiarColors[index % familiarColors.length];

        return {
            label: key,
            data: rawData.map(data => data.hours[key] || 0),
            backgroundColor: color,
            borderColor: color,
            borderWidth: 1
        };
    });

    const data = {
        labels: labels,
        datasets: datasets
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            indexAxis: 'y',
            plugins: {
                title: {
                    display: true,
                    text: 'Diensten'
                },
            },
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
            },
            scales: {
                x: {
                    type: 'linear',
                    title: {
                        display: true,
                        text: 'Hours'
                    },
                    stacked: true
                },
                y: {
                    type: 'category',
                    title: {
                        display: true,
                        text: 'Participants'
                    },
                    stacked: true
                }
            }
        }
    };
    const myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
</script>
