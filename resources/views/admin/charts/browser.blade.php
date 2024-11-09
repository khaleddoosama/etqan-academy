<div class="col-md-6 row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Browser Usage</h3>

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
            <div class="card-body">
                <div class="chart-responsive">
                    <canvas id="browserUsagePieChart" height="150"></canvas>
                </div>

            </div>

        </div>

    </div>
    <div class="col-md-6">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">OS Usage</h3>

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
            <div class="card-body">
                <div class="chart-responsive">
                    <canvas id="osUsagePieChart" height="150"></canvas>
                </div>

            </div>

        </div>

    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Latest Members</h3>

            <div class="card-tools">
                <span class="badge badge-danger">8 New Members</span>
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
            <ul class="clearfix users-list">

                @foreach ($lastMembers as $member)
                    <li>
                        <img src="{{ asset('asset/default.png') }}" alt="User Image"
                            class="img-circle img-bordered img-fluid w-50 mx-auto">
                        <a class="users-list-name"
                            href="#">{{ $member->first_name . ' ' . $member->last_name }}</a>
                        <span class="users-list-date">{{ $member->created_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
            <!-- /.users-list -->
        </div>
        <!-- /.card-body -->
        <div class="text-center card-footer">
            <a href="javascript:">View All Users</a>
        </div>
        <!-- /.card-footer -->
    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const browsers = @json($browsers);
        const labels = browsers.map(item => item.browser);
        const data = browsers.map(item => item.count);

        var browserData = {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#f39c12', '#00c0ef', '#3c8dbc', '#f56954', '#00a65a', '#d2d6de', '#f012be',
                    '#001f3f', '#39CCCC', '#B10DC9', '#85144b', '#111111'
                ]
            }]
        };

        var pieChartCanvas = document.getElementById('browserUsagePieChart').getContext('2d');
        var pieChart = new Chart(pieChartCanvas, {
            type: 'pie',
            data: browserData,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    position: 'right'
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const os = @json($os);
        const labels = os.map(item => item.os);
        const data = os.map(item => item.count);

        var osData = {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#f39c12', '#00c0ef', '#3c8dbc', '#f56954', '#00a65a', '#d2d6de', '#f012be',
                    '#001f3f', '#39CCCC', '#B10DC9', '#85144b', '#111111'
                ]
            }]
        };

        var pieChartCanvas = document.getElementById('osUsagePieChart').getContext('2d');
        var pieChart = new Chart(pieChartCanvas, {
            type: 'pie',
            data: osData,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    position: 'right'
                }
            }
        });
    });
</script>
