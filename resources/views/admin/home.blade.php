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
