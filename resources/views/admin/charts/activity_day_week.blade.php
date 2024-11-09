<div class="col-md-6">
    <div class="card">
        <div class="border-0 card-header">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">Activity by Day of the Week</h3>
                {{-- <a href="javascript:void(0);">View Report</a> --}}
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex">
                {{-- <p class="d-flex flex-column">
                    <span class="text-lg text-bold">$18,230.00</span>
                    <span>Activity by Day of the Week</span>
                </p> --}}
                <p class="ml-auto text-right d-flex flex-column">
                    <span class="text-success">
                        <i class="fas fa-arrow-up"></i> {{ $activityByDayOfWeek['percentage_change'] }}%
                    </span>
                    <span class="text-muted">Since last week</span>
                </p>
            </div>
            <!-- /.d-flex -->

            <div class="mb-4 position-relative">
                <canvas id="weekly-chart" height="200"></canvas>
            </div>

            <div class="flex-row d-flex justify-content-end">
                <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> This week
                </span>
                <span>
                    <i class="fas fa-square text-gray"></i> Last week
                </span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        var ctx = document.getElementById('weekly-chart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                datasets: [{
                        label: 'This Week',
                        borderColor: '#007bff',
                        backgroundColor: '#007bff',
                        data: @json(array_column($activityByDayOfWeek['this_week'], 'activity_count'))
                    },
                    {
                        label: 'Last Week',
                        borderColor: '#6c757d',
                        backgroundColor: '#6c757d',
                        data: @json(array_column($activityByDayOfWeek['last_week'], 'activity_count'))
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: false
                },

            }
        });

    });
</script>
