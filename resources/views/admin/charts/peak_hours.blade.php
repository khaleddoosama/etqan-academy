<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Peak Hours of Activity</h3>
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
