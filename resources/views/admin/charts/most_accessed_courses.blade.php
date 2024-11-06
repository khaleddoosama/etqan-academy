<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Most Accessed Courses</h3>
        </div>
        <div class="card-body">
            <canvas id="mostAccessedCoursesChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Most Accessed Courses Chart
        const mostAccessedCoursesChartCtx = document.getElementById('mostAccessedCoursesChart').getContext(
        '2d');
        new Chart(mostAccessedCoursesChartCtx, {
            type: 'bar',
            data: {
                labels: @json(array_column($mostAccessedCourses, 'course')),
                datasets: [{
                    label: 'Access Count',
                    data: @json(array_column($mostAccessedCourses, 'access_count')),
                    backgroundColor: 'rgba(255, 99, 132, 0.6)'
                }]
            }
        });
    });
</script>
