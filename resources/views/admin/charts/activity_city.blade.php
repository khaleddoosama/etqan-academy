<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Total Activity Count by City</h3>
        </div>
        <div class="card-body">
            <canvas id="activityChart"></canvas>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cityActivity = @json($cityActivity);
        const labels = cityActivity.map(item => item.City);
        const data = cityActivity.map(item => item.Activity_Count);

        const ctx = document.getElementById('activityChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Activity Count',
                    data: data,
                    backgroundColor: 'skyblue'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
