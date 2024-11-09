<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Number of Courses per Top 10 Students</h3>
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
            <canvas id="topStudentsCoursesChart"></canvas>
        </div>
    </div>
</div>


<script>
    // Most Students per Course Chart
    const topStudentsCoursesCtx = document.getElementById('topStudentsCoursesChart').getContext('2d');
    new Chart(topStudentsCoursesCtx, {
        type: 'bar',
        data: {
            labels: @json(array_map(fn($student) => $student->first_name . ' ' . $student->last_name, $topStudentsCourses)),
            datasets: [{
                label: 'Number of Courses',
                data: @json(array_map(fn($student) => $student->course_count, $topStudentsCourses)),
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        }
    });
</script>
