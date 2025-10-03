$(function () {
    // URL parameter handling functions
    function getUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        return {
            search: urlParams.get('search') || '',
            category_id: urlParams.get('category_id') || '',
            type: urlParams.get('type') || '',
            from_date: urlParams.get('from_date') || '',
            to_date: urlParams.get('to_date') || ''
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

        $('#search').val(params.search);
        $('#filter-category').val(params.category_id);
        $('#filter-type').val(params.type);
        $('#filter-from').val(params.from_date);
        $('#filter-to').val(params.to_date);
    }

    // Initialize filters from URL parameters
    initializeFiltersFromUrl();

    // Handle browser back/forward navigation
    window.addEventListener('popstate', function (event) {
        initializeFiltersFromUrl();
        table.draw();
    });

    if ($.fn.DataTable.isDataTable("#table")) {
        $('#table').DataTable().clear().destroy();
    }

    let table = $("#table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.entryDataUrl,
            data: function (d) {
                d.search = $('#search').val();
                d.category_id = $('#filter-category').val();
                d.type = $('#filter-type').val();
                d.from_date = $('#filter-from').val();
                d.to_date = $('#filter-to').val();
            }
        },
        columns: [
            {
                data: 'id',
                name: 'id',
            },
            {
                data: 'title',
                name: 'title',
            },
            {
                data: 'description',
                name: 'description',
                render: function (data, type, row) {
                    if (data && data.length > 50) {
                        return '<span title="' + data + '">' + data.substring(0, 50) + '...</span>';
                    }
                    return data || '-';
                }
            },
            {
                data: 'category_name',
                name: 'category.name',
                orderable: false,
            },
            {
                data: 'category_type',
                name: 'category.type',
                orderable: false,
                render: function (data, type, row) {
                    if (data === 'income') {
                        return '<span class="badge badge-success">Income</span>';
                    } else if (data === 'expense') {
                        return '<span class="badge badge-danger">Expense</span>';
                    }
                    return '<span class="badge badge-secondary">Unknown</span>';
                }
            },
            {
                data: 'amount',
                name: 'amount'
            },
            {
                data: 'signed_amount',
                name: 'signed_amount',
                render: function (data, type, row) {
                    const amount = parseFloat(data);
                    const className = amount > 0 ? 'amount-positive' : (amount < 0 ? 'amount-negative' : 'amount-neutral');
                    const prefix = amount > 0 ? '+' : '';
                    return '<span class="' + className + '">' + prefix + amount.toFixed(2) + ' EGP</span>';
                }
            },
            {
                data: 'transaction_date',
                name: 'transaction_date'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        responsive: false,
        lengthChange: false,
        autoWidth: false,
        searching: false, // Disable default search
        language: window.datatableLanguage || {},
        order: [[0, 'desc']],
    });    // Function to update URL and redraw table
    function updateUrlAndRedraw() {
        const params = {
            search: $('#search').val(),
            category_id: $('#filter-category').val(),
            type: $('#filter-type').val(),
            from_date: $('#filter-from').val(),
            to_date: $('#filter-to').val()
        };

        updateUrl(params);
        table.draw();
        updateStatistics(params);
    }

    // Function to update statistics
    function updateStatistics(params) {
        $.ajax({
            url: window.entryStatisticsUrl,
            method: 'GET',
            data: params,
            success: function (data) {
                $('#stat-total-income-entries').text(data.total_income_entries);
                $('#stat-total-payments').text(data.total_payments);
                $('#stat-total-income').text(data.total_income);
                $('#stat-total-expenses').text(data.total_expenses);
                $('#stat-net-profit').text(data.net_profit);
            },
            error: function (xhr) {
                console.error('Error updating statistics:', xhr);
            }
        });
    }

    // Global function for onclick handlers
    window.updateFiltersAndTable = function () {
        updateUrlAndRedraw();
    };

    // Filter change handlers
    $('#filter-category, #filter-type, #filter-from, #filter-to').on('change', function () {
        updateUrlAndRedraw();
    });

    // Custom search handlers with debounce
    let searchTimeout;
    $('#search').on('keyup input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            updateUrlAndRedraw();
        }, 500); // 500ms delay
    });

    // Clear search button
    $('#clear-search').on('click', function () {
        $('#search').val('').trigger('input');
    });    // Reset filters button
    $('#reset-filters').on('click', function () {
        $('#search').val('');
        $('#filter-category').val('');
        $('#filter-type').val('');
        $('#filter-from').val('');
        $('#filter-to').val('');

        // Update URL and redraw
        updateUrl({});
        table.draw();
        updateStatistics({});

        // Optional: Show a brief message if toastr is available
        if (typeof toastr !== 'undefined') {
            toastr.success('Filters cleared successfully');
        }
    });

    // Keyboard shortcuts
    $(document).keydown(function (e) {
        // Ctrl+F or Cmd+F to focus search
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 70) {
            e.preventDefault();
            $('#search').focus();
        }
        // Escape to clear search
        if (e.keyCode === 27) {
            $('#search').val('').trigger('input');
        }
    });

    // Auto-refresh functionality (optional, every 5 minutes)
    if (typeof window.enableAutoRefresh !== 'undefined' && window.enableAutoRefresh) {
        setInterval(function () {
            table.draw(false); // false to keep current page
        }, 300000); // 5 minutes
    }
});
