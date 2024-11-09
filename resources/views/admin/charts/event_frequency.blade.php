<div class="col-md-6">
    <div class="card">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">Event Frequency</h3>
                {{-- <a href="javascript:void(0);">View Report</a> --}}
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex">
                <p class="d-flex flex-column">
                    <span class="text-lg text-bold">{{ $countEventsFrequencyOverTime }}</span>
                    <span>Event Frequency Over Time</span>
                </p>
                <p class="ml-auto text-right d-flex flex-column">
                    <span class="{{ $eventFrequencyOverTime['percentage_change'] < 0 ? 'text-danger' : 'text-success' }}">
                        <i class="{{ $eventFrequencyOverTime['percentage_change'] < 0 ? 'fas fa-arrow-down' : 'fas fa-arrow-up' }}"></i> {{ $eventFrequencyOverTime['percentage_change'] }}%
                    </span>
                    <span class="text-muted">Since last 10 days</span>
                </p>
            </div>
            <!-- /.d-flex -->

            <div class="position-relative mb-4">
                <canvas id="visitors-chart" height="200"></canvas>
            </div>

            <div class="d-flex flex-row justify-content-end">
                <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> This Week
                </span>

                <span>
                    <i class="fas fa-square text-gray"></i> Last Week
                </span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the canvas element for the visitors chart
        const ctx = document.getElementById('visitors-chart').getContext('2d');

        // Create a new Chart
        const visitorsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json(array_column($eventFrequencyOverTime['last_10_days'], 'date')),
                datasets: [{
                        label: 'This Week',
                        data: @json(array_column($eventFrequencyOverTime['last_10_days'], 'event_count')),
                        borderColor: '#007bff',
                        fill: false,
                        tension: 0.4,
                    },
                    {
                        label: 'Last Week',
                        data: @json(array_column($eventFrequencyOverTime['same_days_last_month'], 'event_count')),
                        borderColor: '#ced4da',
                        fill: true,
                        tension: 0.4,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    });
</script>
