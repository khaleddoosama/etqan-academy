<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Activity Heatmap</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <canvas id="heatmapChart" height="800"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-chart-matrix@2"></script>
<script>
    const data = @json($heatMap);

    const labels = Object.keys(data);
    const hours = Array.from({
        length: 24
    }, (_, i) => i);
    // sort hours


    // Prepare the data in a format suitable for the heatmap
    const heatmapData = [];
    hours.forEach((hour, hourIndex) => {
        labels.forEach((day, dayIndex) => {
            heatmapData.push({
                x: day,
                y: hour,
                v: data[day][hourIndex]
            });
        });
    });

    const maxValue = Math.max(...heatmapData.map(item => item.v));

    const ctx = document.getElementById('heatmapChart').getContext('2d');
    const config = {
        type: 'matrix',
        data: {
            datasets: [{
                label: 'Activity Heatmap',
                data: heatmapData,
                backgroundColor(context) {
                    const value = context.dataset.data[context.dataIndex].v;
                    const alpha = value / maxValue; // Normalize value for alpha transparency
                    return `rgba(0, 123, 255, ${alpha})`;
                },
                borderWidth: 1,
                hoverBorderColor: 'rgba(0,0,0,0.5)',
                width: function(context) {
                    const chartArea = context.chart.chartArea;
                    if (!chartArea) {
                        return 0; // Or any default width value
                    }
                    return (chartArea.right - chartArea.left) / labels.length;
                },
                height: function(context) {
                    const chartArea = context.chart.chartArea;
                    if (!chartArea) {
                        return 0; // Or any default height value
                    }
                    return (chartArea.bottom - chartArea.top) / hours.length;
                },

            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title() {
                            return '';
                        },
                        label(context) {
                            const value = context.raw.v;
                            const day = context.raw.x;
                            const hour = context.raw.y;
                            return `${day} at ${hour}:00 - ${value} activities`;
                        }
                    }
                },
                matrixLabel: {
                    enabled: true,
                }
            },
            scales: {
                x: {
                    type: 'category',
                    labels: labels,
                    title: {
                        display: true,
                        text: 'Day of the Week'
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        padding: 12, // Add padding to separate the labels
                    }
                },
                y: {
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Hour of the Day'
                    },
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        display: false
                    }
                }
            },

        },
        plugins: [{
            id: 'matrixLabel',
            beforeDraw(chart) {
                const ctx = chart.ctx;
                ctx.save();
                chart.data.datasets.forEach(dataset => {
                    dataset.data.forEach((dataPoint, index) => {
                        const meta = chart.getDatasetMeta(0);
                        const rect = meta.data[index];
                        ctx.fillStyle = 'black';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.font = 'bold 12px Arial';
                        ctx.fillText(dataPoint.v, rect.x + rect.width / 2, rect.y + rect
                            .height / 2);
                    });
                });
                ctx.restore();
            }
        }]
    };
    var myChart = new Chart(ctx, config);
</script>
