@extends('admin.master')
@section('title', 'Dashboard')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

                    {{-- Browser Usage Chart --}}
                    @include('admin.charts.browser')

                    {{-- OS Usage Chart --}}
                    {{-- @include('admin.charts.os') --}}

                    {{-- Event Frequency Over Time Chart --}}
                    @include('admin.charts.event_frequency')


                    {{-- Unique IP Counts Chart --}}
                    @include('admin.charts.unique_ip')

                    {{-- Most Accessed Courses Chart --}}
                    @include('admin.charts.most_accessed_courses')

                    {{-- Peak Hours of Activity Chart --}}
                    @include('admin.charts.peak_hours')

                    {{-- Activity by Day of the Week Chart --}}
                    @include('admin.charts.activity_day_week')


                    {{-- Activity by City Chart --}}
                    @include('admin.charts.activity_city')

                    {{-- Most students Per Course --}}
                    @include('admin.charts.top_students_courses')

                    {{-- Most students Per Course --}}
                    @include('admin.charts.top_courses_students')


                    {{-- Most Accessed URLs Chart --}}
                    @include('admin.charts.most_accessed_urls')

                    {{-- Heat Map --}}
                    @include('admin.charts.heat_map')


                </div>
            </div>
        @endcan
        <!-- /.content -->
    </div>
@endsection

@section('scripts')

@endsection
