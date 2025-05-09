<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Unique IP Counts</h3>
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
            <canvas id="uniqueIPCountsChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const uniqueIPCountsChartCtx = document.getElementById('uniqueIPCountsChart').getContext('2d');
        new Chart(uniqueIPCountsChartCtx, {
            type: 'bar',
            data: {
                labels: @json(array_column($uniqueIPCounts, 'ip_address')),
                datasets: [{
                    label: 'Access Count',
                    data: @json(array_column($uniqueIPCounts, 'access_count')),
                    backgroundColor: 'rgba(255, 159, 64, 0.6)'
                }]
            }
        });
    });
</script>
