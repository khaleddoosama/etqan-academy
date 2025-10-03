{{-- JavaScript for Reports --}}
<script>
    // Global variables
    window.reportChartsUrl = "{{ route('admin.accounting.reports.charts') }}";
    window.reportDataUrl = "{{ route('admin.accounting.reports.data') }}";
    window.reportStatisticsUrl = "{{ route('admin.accounting.reports.statistics') }}";
    window.reportExportUrl = "{{ route('admin.accounting.reports.export') }}";
    let combinedTable;
    let charts = {};

    // URL parameter handling functions
    function getUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        return {
            from_date: urlParams.get('from_date') || '',
            to_date: urlParams.get('to_date') || '',
            category_id: urlParams.get('category_id') || '',
            type: urlParams.get('type') || ''
        };
    }

    function updateUrl(params) {
        const urlParams = new URLSearchParams();

        // Only add non-empty parameters to URL
        Object.keys(params).forEach(key => {
            if (params[key] && params[key].trim() !== '') {
                urlParams.set(key, params[key]);
            }
        });

        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState({}, '', newUrl);
    }

    function initializeFiltersFromUrl() {
        const params = getUrlParams();

        if (params.from_date) $('#filter-from-date').val(params.from_date);
        if (params.to_date) $('#filter-to-date').val(params.to_date);
        if (params.category_id) $('#filter-category').val(params.category_id);
        if (params.type) $('#filter-type').val(params.type);
    }

    function updateUrlAndRedraw() {
        const params = {
            from_date: $('#filter-from-date').val(),
            to_date: $('#filter-to-date').val(),
            category_id: $('#filter-category').val(),
            type: $('#filter-type').val()
        };
        updateUrl(params);

        // Display filter status
        displayFilterStatus();

        // Highlight active filters
        highlightActiveFilters();

        // Show loading indicators
        showLoadingIndicators();
        showProgressBar();

        let completedTasks = 0;
        const totalTasks = 3;

        const updateProgress = () => {
            completedTasks++;
            updateProgressBar((completedTasks / totalTasks) * 100);
        };

        // Update all components with progress tracking
        Promise.all([
            updateStatistics(params).then(updateProgress),
            updateCharts(params).then(updateProgress),
            updateTable().then(updateProgress)
        ]).then(() => {
            // Hide loading indicators when all updates complete
            hideLoadingIndicators();
            hideProgressBar();
            toastr.success('Report updated successfully');
        }).catch((error) => {
            console.error('Update error:', error);
            hideLoadingIndicators();
            hideProgressBar();
            toastr.error('Error updating report components');
        });
    }

    // Handle browser back/forward navigation
    window.addEventListener('popstate', function(event) {
        initializeFiltersFromUrl();
        updateUrlAndRedraw();
    });

    $(document).ready(function() {
        // Initialize filters from URL parameters
        initializeFiltersFromUrl();

        // Initialize DataTable
        initializeCombinedTable();

        // Load charts
        loadCharts();
        // Set up event handlers
        setupEventHandlers(); // Display initial filter status
        displayFilterStatus();

        // Highlight active filters on load
        highlightActiveFilters();
    });

    function initializeCombinedTable() {
        if ($.fn.DataTable.isDataTable("#combinedTable")) {
            $('#combinedTable').DataTable().clear().destroy();
        }

        combinedTable = $("#combinedTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: window.reportDataUrl,
                type: 'GET',
                data: function(d) {
                    d.from_date = $('#filter-from-date').val();
                    d.to_date = $('#filter-to-date').val();
                    d.category_id = $('#filter-category').val();
                    d.type = $('#filter-type').val();

                    console.log('DataTable request:', d); // Debug logging
                },
                error: function(xhr, error, code) {
                    console.error('DataTable AJAX error:', error, code);
                    toastr.error('Failed to load table data');
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'amount',
                    name: 'amount',
                    render: function(data, type, row) {
                        return data + ' EGP';
                    }
                },
                {
                    data: 'date',
                    name: 'date'
                }
            ],
            order: [
                [6, 'desc']
            ],
            pageLength: 10,
            responsive: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            lengthChange: true,
            language: {
                processing: "Loading data...",
                search: "Search:",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }

    function loadCharts() {
        const params = {
            from_date: $('#filter-from-date').val(),
            to_date: $('#filter-to-date').val()
        };
        updateCharts(params);
    }

    function createIncomeExpenseChart(data) {
        const ctx = document.getElementById('incomeExpenseChart').getContext('2d');

        if (charts.incomeExpense) {
            charts.incomeExpense.destroy();
        }

        charts.incomeExpense = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Income', 'Expenses', 'Profit'],
                datasets: [{
                    data: [data.income, data.expenses, data.profit],
                    backgroundColor: ['#28a745', '#dc3545', '#17a2b8'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    function createMonthlyTrendsChart(data) {
        const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');

        if (charts.monthlyTrends) {
            charts.monthlyTrends.destroy();
        }

        const labels = data.map(item => item.month);
        const income = data.map(item => item.income);
        const expenses = data.map(item => item.expenses);
        const profit = data.map(item => item.profit);

        charts.monthlyTrends = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Income',
                        data: income,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Expenses',
                        data: expenses,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Profit',
                        data: profit,
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }

    function setupEventHandlers() {
        $('#apply-filters').on('click', function() {
            updateUrlAndRedraw();
        });
        $('#reset-filters').on('click', function() {

            $('#filter-from-date').val('');
            $('#filter-to-date').val('');
            $('#filter-category').val('');
            $('#filter-type').val('');

            // Update URL and redraw
            updateUrl({});
            updateUrlAndRedraw();
        });

        // Filter change handlers with debounce
        let filterTimeout;
        $('#filter-from-date, #filter-to-date, #filter-category, #filter-type').on('change', function() {
            const $this = $(this);

            // Add immediate visual feedback
            $this.addClass('filter-applied');

            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(function() {
                updateUrlAndRedraw();
            }, 500); // 500ms delay to avoid too many requests
        });

        // Real-time filter preview (show what will change without applying)
        $('#filter-from-date, #filter-to-date, #filter-category, #filter-type').on('input focus', function() {
            displayFilterStatus();
            highlightActiveFilters();
        });

        // Keyboard shortcuts
        $(document).keydown(function(e) {
            // Escape to reset filters
            if (e.keyCode === 27) {
                $('#reset-filters').click();
            }
        });
    }

    function updateStatistics(params) {
        // Add updating animation to statistics cards
        $('.small-box').addClass('updating');

        return $.get(window.reportStatisticsUrl, params)
            .done(function(data) {
                // Smooth number animation
                animateNumber($('#stat-total-income'), data.total_income);
                animateNumber($('#stat-total-expenses'), data.total_expenses);
                animateNumber($('#stat-net-profit'), data.net_profit);
                $('#stat-profit-margin').text(data.profit_margin);

                $('.small-box').removeClass('updating');
                toastr.info('Statistics updated', '', {
                    timeOut: 1000,
                    showMethod: 'slideDown'
                });
            })
            .fail(function() {
                $('.small-box').removeClass('updating');
                toastr.error('Failed to update statistics');
            });
    }

    function updateCharts(params) {
        $('.chart-container').parent().addClass('updating');

        return $.get(window.reportChartsUrl, params)
            .done(function(data) {
                createIncomeExpenseChart(data.income_vs_expenses);
                createMonthlyTrendsChart(data.monthly_trends);

                $('.chart-container').parent().removeClass('updating');
                toastr.info('Charts updated', '', {
                    timeOut: 1000,
                    showMethod: 'slideDown'
                });
            })
            .fail(function() {
                $('.chart-container').parent().removeClass('updating');
                toastr.error('Failed to update charts');
            });
    }

    function updateTable() {
        $('.table-combined').addClass('updating');

        return new Promise((resolve, reject) => {
            if (combinedTable) {
                // Add one-time event listener for draw completion
                combinedTable.one('draw.dt', function() {
                    $('.table-combined').removeClass('updating');
                    toastr.info('Table updated', '', {
                        timeOut: 1000,
                        showMethod: 'slideDown'
                    });
                    resolve();
                });

                // Trigger table redraw with current page preserved
                combinedTable.ajax.reload(null, false);
            } else {
                $('.table-combined').removeClass('updating');
                reject('Table not initialized');
            }
        });
    }

    function showLoadingIndicators() {
        // Add loading spinners to each component
        $('.report-card').addClass('loading updating');
        $('.table-combined').addClass('loading updating');
        $('.small-box').addClass('updating');

        // Add loading overlay to statistics
        $('.small-box').append('<div class="loading-overlay"><i class="fas fa-spinner fa-spin"></i></div>');

        // Add loading overlay to charts
        $('.chart-container').append('<div class="chart-loading-overlay"><i class="fas fa-spinner fa-spin"></i></div>');

        // Add loading to table
        $('.table-combined .card-body').append('<div class="table-loading-overlay"><i class="fas fa-spinner fa-spin"></i></div>');
    }

    function hideLoadingIndicators() {
        $('.report-card').removeClass('loading updating');
        $('.table-combined').removeClass('loading updating');
        $('.small-box').removeClass('updating');
        $('.loading-overlay').remove();
        $('.chart-loading-overlay').remove();
        $('.table-loading-overlay').remove();
    }

    function showProgressBar() {
        const progressHtml = `
            <div id="update-progress" class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
                <div class="progress-text">Updating report...</div>
            </div>
        `;
        $('body').append(progressHtml);
    }

    function updateProgressBar(percentage) {
        $('#update-progress .progress-fill').css('width', percentage + '%');
        $('#update-progress .progress-text').text(`Updating report... ${Math.round(percentage)}%`);
    }

    function hideProgressBar() {
        $('#update-progress').fadeOut(300, function() {
            $(this).remove();
        });
    }

    function exportReport(format) {
        const params = new URLSearchParams({
            format: format,
            from_date: $('#filter-from-date').val(),
            to_date: $('#filter-to-date').val(),
            category_id: $('#filter-category').val() || '',
            type: $('#filter-type').val() || ''
        });

        // Create download link
        const link = document.createElement('a');
        link.href = window.reportExportUrl + '?' + params.toString();
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        toastr.success('Report export started...');
    }

    function displayFilterStatus() {
        const params = getUrlParams();
        const hasFilters = Object.values(params).some(val => val && val.trim() !== '');

        if (hasFilters) {
            const filters = [];

            if (params.from_date) filters.push(`From: ${params.from_date}`);
            if (params.to_date) filters.push(`To: ${params.to_date}`);
            if (params.category_id) {
                const categoryName = $('#filter-category option:selected').text();
                if (categoryName && categoryName !== '{{ __("main.all_categories") }}') {
                    filters.push(`Category: ${categoryName}`);
                }
            }
            if (params.type) filters.push(`Type: ${params.type.charAt(0).toUpperCase() + params.type.slice(1)}`);

            if (filters.length > 0) {
                $('#filter-status-text').text('Active Filters: ' + filters.join(', '));
                $('#filter-status').show();
            } else {
                $('#filter-status').hide();
            }
        } else {
            $('#filter-status').hide();
        }
    }

    function highlightActiveFilters() {
        const params = getUrlParams();

        // Remove existing highlights
        $('.form-control').removeClass('filter-applied');

        // Add highlights for active filters
        if (params.from_date) $('#filter-from-date').addClass('filter-applied');
        if (params.to_date) $('#filter-to-date').addClass('filter-applied');
        if (params.category_id) $('#filter-category').addClass('filter-applied');
        if (params.type) $('#filter-type').addClass('filter-applied');
    }

    function animateNumber($element, targetValue) {
        const currentValue = parseFloat($element.text().replace(/[^\d.-]/g, '')) || 0;
        const targetNum = parseFloat(targetValue.replace(/[^\d.-]/g, '')) || 0;

        $({
            countNum: currentValue
        }).animate({
            countNum: targetNum
        }, {
            duration: 1000,
            easing: 'swing',
            step: function() {
                const formattedNum = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(this.countNum);
                $element.text(formattedNum);
            },
            complete: function() {
                $element.text(targetValue);
            }
        });
    }
</script>
