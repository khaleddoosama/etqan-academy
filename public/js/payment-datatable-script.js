$(function () {
    // URL parameter handling functions
    function getUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        return {
            search: urlParams.get('search') || '',
            user_id: urlParams.get('user_id') || '',
            gateway: urlParams.get('gateway') || '',
            status: urlParams.get('status') || '',
            coupon_id: urlParams.get('coupon_id') || '',
            from_created_at: urlParams.get('from_created_at') || '',
            to_created_at: urlParams.get('to_created_at') || ''
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
        $('#filter-user').val(params.user_id);
        $('#filter-gateway').val(params.gateway);
        $('#filter-status').val(params.status);
        $('#filter-coupon').val(params.coupon_id);
        $('#filter-from').val(params.from_created_at);
        $('#filter-to').val(params.to_created_at);
    }

    // Initialize filters from URL parameters
    initializeFiltersFromUrl();

    // Handle browser back/forward navigation
    window.addEventListener('popstate', function(event) {
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
            url: window.paymentDataUrl,
            data: function (d) {
                d.search = $('#search').val();
                d.user_id = $('#filter-user').val();
                d.gateway = $('#filter-gateway').val();
                d.status = $('#filter-status').val();
                d.coupon_id = $('#filter-coupon').val();
                d.from_created_at = $('#filter-from').val();
                d.to_created_at = $('#filter-to').val();
            }
        },
        columns: [
            {
                data: 'id',
                name: 'id',
            },
            {
                data: 'user_name',
                name: 'user.name',
                orderable: false,
            },
            {
                data: 'user_email',
                name: 'user.email',
                orderable: false,
            },
            {
                data: 'user_phone',
                name: 'user.phone',
                orderable: false,
            },
            {
                data: 'gateway',
                name: 'gateway'
            },
            {
                data: 'invoice_id',
                name: 'invoice_id'
            },
            {
                data: 'invoice_key',
                name: 'invoice_key'
            },
            {
                data: 'coupon_code',
                name: 'coupon.code',
                orderable: false,
            },
            {
                data: 'payment_method',
                name: 'payment_method'
            },
            {
                data: 'amount_confirmed',
                name: 'amount_confirmed',
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'created_at',
                name: 'created_at'
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
    });

    // Filter change handlers
    $('#filter-user, #filter-gateway, #filter-status, #filter-coupon, #filter-from, #filter-to').on('change', function () {
        updateUrlAndRedraw();
    });

    // Custom search handlers with debounce
    let searchTimeout;
    $('#search').on('keyup input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            updateUrlAndRedraw();
        }, 500); // 500ms delay
    });

    // Function to update URL and redraw table
    function updateUrlAndRedraw() {
        const params = {
            search: $('#search').val(),
            user_id: $('#filter-user').val(),
            gateway: $('#filter-gateway').val(),
            status: $('#filter-status').val(),
            coupon_id: $('#filter-coupon').val(),
            from_created_at: $('#filter-from').val(),
            to_created_at: $('#filter-to').val()
        };

        updateUrl(params);
        table.draw();
    }

    // Clear search button handler
    $('#clear-search').on('click', function () {
        $('#search').val('');
        clearTimeout(searchTimeout);
        updateUrlAndRedraw();
    });

    // Quick Action SweetAlert2 handlers
    $(document).on('click', '.quick-approve-btn', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const paymentId = form.data('payment-id');

        Swal.fire({
            title: '<i class="fas fa-check-circle text-success"></i> Quick Approve',
            html: `
                <p><strong>Approve Instapay Payment #${paymentId}?</strong></p>
                <p class="text-muted">This will instantly approve the payment and grant course access.</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check"></i> Approve',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Approving payment...',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });

    $(document).on('click', '.quick-reject-btn', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const paymentId = form.data('payment-id');

        Swal.fire({
            title: '<i class="fas fa-times-circle text-danger"></i> Quick Reject',
            html: `
                <p><strong>Reject Instapay Payment #${paymentId}?</strong></p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-times"></i> Reject',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Rejecting payment...',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });

    $(document).on('click', '#reset-filters', function (e) {
        e.preventDefault();

        // Clear all filters
        $('#filter-user').val('');
        $('#filter-gateway').val('');
        $('#filter-status').val('');
        $('#filter-coupon').val('');
        $('#filter-from').val('');
        $('#filter-to').val('');

        // Clear search input
        $('#search').val('');

        // Clear URL parameters and redraw table
        window.history.replaceState({}, '', window.location.pathname);
        table.draw();
    });
});
