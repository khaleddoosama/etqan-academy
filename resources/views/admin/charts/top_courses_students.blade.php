<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Number of Students per Course</h3>
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
            <canvas id="studentsPerCourseChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentsPerCourseCtx = document.getElementById('studentsPerCourseChart').getContext('2d');
        new Chart(studentsPerCourseCtx, {
            type: 'bar',
            data: {
                labels: @json(array_map(fn($course) => $course->course_title, $studentsPerCourse)),
                datasets: [{
                    label: 'Number of Students',
                    data: @json(array_map(fn($course) => $course->student_count, $studentsPerCourse)),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }]
            }
        });
    });
</script>
