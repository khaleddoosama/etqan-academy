@extends('admin.master')
@section('title', 'Dashboard')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="mb-2 row">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ __('main.dashboard') }}</h1>
                    </div><!-- /.col -->

                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        @can('dashboard.list')
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $activeCourseCount }}</h3>

                                <p>Active Courses</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $activeLectureCount }}</h3>

                                <p>Lectures</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $activeUserCount }}</h3>

                                <p>Active Users</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ count($uniqueIPCount) }}</h3>

                                <p>Unique Visitors</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- Main content -->
                <div class="row">
                    {{-- Event Frequency Over Time Chart --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Event Frequency Over Time</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="eventFrequencyChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Unique IP Counts Chart --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Unique IP Counts</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="uniqueIPCountsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Most Accessed Courses Chart --}}
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

                    {{-- Peak Hours of Activity Chart --}}
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

                    {{-- Activity by Day of the Week Chart --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Activity by Day of the Week</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="activityByDayOfWeekChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Most students Per Course --}}
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


                    {{-- Most students Per Course --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Number of Courses per Top 10 Students</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="topStudentsCoursesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Number of Students per Course -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Number of Students per Course</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="studentsPerCourseChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Most Accessed URLs Chart --}}
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
                </div>

            </div>
        @endcan
        <!-- /.content -->
    </div>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Event Frequency Over Time Chart
        const eventFrequencyChartCtx = document.getElementById('eventFrequencyChart').getContext('2d');
        new Chart(eventFrequencyChartCtx, {
            type: 'line',
            data: {
                labels: @json(array_column($eventFrequencyOverTime, 'date')),
                datasets: [{
                    label: 'Event Count',
                    data: @json(array_column($eventFrequencyOverTime, 'event_count')),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false
                }]
            }
        });

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

        // Unique IP Counts Chart
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

        // Most Accessed Courses Chart
        const mostAccessedCoursesChartCtx = document.getElementById('mostAccessedCoursesChart').getContext('2d');
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

        // Peak Hours of Activity Chart
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

        // Activity by Day of the Week Chart
        const activityByDayOfWeekChartCtx = document.getElementById('activityByDayOfWeekChart').getContext('2d');
        new Chart(activityByDayOfWeekChartCtx, {
            type: 'bar',
            data: {
                labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                datasets: [{
                    label: 'Activity Count',
                    data: @json(array_column($activityByDayOfWeek, 'activity_count')),
                    backgroundColor: 'rgba(255, 206, 86, 0.6)'
                }]
            }
        });

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
    </script>

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
@endsection
