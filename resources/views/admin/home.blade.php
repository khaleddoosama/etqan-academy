@extends('admin.master')
@section('title', 'Dashboard')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="mb-2 row">
                    <div class="col-sm-6">
                        <h1 class="m-0">الرئيسية</h1>
                    </div><!-- /.col -->

                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        @can('dashboard.list')
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Info boxes -->
                    <div class="row">

                        <!-- fix for small devices only -->
                        <div class="clearfix hidden-md-up"></div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="mb-3 info-box">
                                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">المتخصصيين المعلقين</span>
                                    {{-- <span class="info-box-number">{{ $pendingSpecialistsCount }}</span> --}}
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="mb-3 info-box">
                                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">المتعافيين المعلقين</span>
                                    {{-- <span class="info-box-number">{{ $pendingPatientsCount }}</span> --}}
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"> المنشورات المعلقه</span>
                                    <span class="info-box-number">
                                        {{-- {{ $pendingPostsCount }} --}}
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="mb-3 info-box">
                                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">الاعجابات</span>
                                    {{-- <span class="info-box-number">{{ $likesCount }}</span> --}}
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        {{-- <div class="col-12 col-sm-6 col-lg col-md-3">
                            <div class="mb-3 info-box">
                                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">الاعجابات</span>
                                    <span class="info-box-number">{{ $likesCount }}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div> --}}
                        <!-- /.col -->


                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-md-6">

                            <!-- Donut chart -->
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="far fa-chart-bar"></i>
                                         اعداد المستخدمين المفعله
                                    </h3>

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
                                    <div id="donut-chart" style="height: 300px;"></div>
                                </div>
                                <!-- /.card-body-->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col (LEFT) -->
                        <div class="col-md-6">

                            <!-- Donut chart -->
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="far fa-chart-bar"></i>
                                        اعداد المنشورات المفعله
                                    </h3>

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
                                    <div id="donut-chart2" style="height: 300px;"></div>
                                </div>
                                <!-- /.card-body-->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <!-- STACKED BAR CHART -->
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">تقييمات المتخصصيين</h3>

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
                                    <div class="chart">
                                        <canvas id="stackedBarChart"
                                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->

                        </div>
                        <!-- /.col (LEFT) -->
                        <!-- Left col -->
                        <div class="col-md-6">
                            <!-- TABLE: LATEST ORDERS -->
                            <div class="card">
                                <div class="border-transparent card-header">
                                    <h3 class="card-title">اخر المنشورات المفعله</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="p-0 card-body">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>صاحب المنشور</th>
                                                    <th>عنوان المنشور</th>
                                                    <th>عدد الاعجابات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- @foreach ($posts as $post)
                                                    <tr>
                                                        <td><a
                                                                href="{{ route('posts.show', $post->slug) }}">{{ $post->id }}</a>
                                                        </td>
                                                        <td>{{ $post->user->name }}</td>
                                                        <td>{{ $post->title }}</td>
                                                        <td>{{ $post->countLikes() }}</td>
                                                    </tr>
                                                @endforeach --}}
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->
                                </div>

                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        @endcan
        <!-- /.content -->
    </div>
@endsection


@section('scripts')
    {{-- <script>
        $(function() {
            var donutData = [{
                    label: 'المتعافين',
                    data: {{ $patientsCount }},
                    color: '#f56954'
                },
                {
                    label: 'المتخصصيين',
                    data: {{ $specialistsCount }},
                    color: '#00a65a'
                },
            ]
            $.plot('#donut-chart', donutData, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        innerRadius: 0.5,
                        label: {
                            show: true,
                            radius: 2 / 3,
                            formatter: labelFormatter,
                            threshold: 0.1
                        }
                    }
                },
                legend: {
                    show: false
                }
            })


            // var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
            // var donutData = {
            //     labels: [
            //         'عدد المتعافين',
            //         'عدد المتخصصيين',

            //     ],
            //     datasets: [{
            //         data: [{{ $patientsCount }}, {{ $specialistsCount }}],
            //         backgroundColor: ['#f56954', '#00a65a'],
            //     }]
            // }

            // var donutOptions = {
            //     maintainAspectRatio: false,
            //     responsive: true,
            // }
            // //Create pie or douhnut chart
            // // You can switch between pie and douhnut using the method below.
            // new Chart(donutChartCanvas, {
            //     type: 'doughnut',
            //     data: donutData,
            //     options: donutOptions
            // })

            ////////////////////////////////////////
            var donutData = [{
                    label: 'منشورات المتعافين',
                    data: {{ $postsPatientsCount }},
                    color: '#f56954'
                },
                {
                    label: 'منشورات المتخصصيين',
                    data: {{ $postsSpecialistsCount }},
                    color: '#00a65a'
                },
            ]
            $.plot('#donut-chart2', donutData, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        innerRadius: 0.5,
                        label: {
                            show: true,
                            radius: 2 / 3,
                            formatter: labelFormatter,
                            threshold: 0.1
                        }
                    }
                },
                legend: {
                    show: false
                }
            })
            // var donutChartCanvas = $('#donutChart2').get(0).getContext('2d')
            // var donutData = {
            //     labels: [
            //         'منشورات المتعافين',
            //         'منشورات المتخصصيين',

            //     ],
            //     datasets: [{
            //         data: [{{ $postsPatientsCount }}, {{ $postsSpecialistsCount }}],
            //         backgroundColor: ['#f56954', '#00a65a'],
            //     }]
            // }

            // var donutOptions = {
            //     maintainAspectRatio: false,
            //     responsive: true,
            // }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            // new Chart(donutChartCanvas, {
            //     type: 'doughnut',
            //     data: donutData,
            //     options: donutOptions
            // })

            //---------------------
            //- STACKED BAR CHART -
            //---------------------
            var areaChartData = {
                labels: ['1', '2', '3', '4', '5'],
                datasets: [{
                        label: 'تقييمات المتخصصيين',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: [{{ $specialistsRates[1] }}, {{ $specialistsRates[2] }},
                            {{ $specialistsRates[3] }}, {{ $specialistsRates[4] }},
                            {{ $specialistsRates[5] }}
                        ]
                    },

                ]
            }
            var barChartData = $.extend(true, {}, areaChartData)

            var stackedBarChartCanvas = $('#stackedBarChart').get(0).getContext('2d')
            var stackedBarChartData = $.extend(true, {}, barChartData)
            var stackedBarChartData;

            var stackedBarChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        stacked: true,
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            }

            new Chart(stackedBarChartCanvas, {
                type: 'bar',
                data: stackedBarChartData,
                options: stackedBarChartOptions
            })

            /////////////////////////////


            /*
             * DONUT CHART
             * -----------
             */


        })

        function labelFormatter(label, series) {
            return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">' +
                label +
                '<br>' +
                Math.round(series.percent) + '%</div>'
        }
    </script> --}}
@endsection
