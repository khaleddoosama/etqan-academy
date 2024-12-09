<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Peak Hours of Activity</h3>
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
            <canvas id="peakHoursChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const peakHoursChartCtx = document.getElementById('peakHoursChart').getContext('2d');
        new Chart(peakHoursChartCtx, {
            type: 'bar',
            data: {
                labels: @json(array_column($peakHours, 'hour')),
                datasets: [{
                    label: 'Activity Count',
                    data: @json(array_column($peakHours, 'activity_count')),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            }
        });
    });
</script>
