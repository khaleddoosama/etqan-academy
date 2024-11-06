<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Most Accessed URLs</h3>
        </div>
        <div class="card-body">
            <canvas id="mostAccessedURLsChart"></canvas>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Most Accessed URLs Chart
        const mostAccessedURLsChartCtx = document.getElementById('mostAccessedURLsChart').getContext('2d');
        new Chart(mostAccessedURLsChartCtx, {
            type: 'bar',
            data: {
                labels: @json(array_column($mostAccessedURLs, 'url')),
                datasets: [{
                    label: 'Access Count',
                    data: @json(array_column($mostAccessedURLs, 'access_count')),
                    backgroundColor: 'rgba(153, 102, 255, 0.6)'
                }]
            }
        });
    });
</script>
