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
                data: 'image',
                name: 'image',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    if (data && data !== '') {
                        return '<img src="' + data + '" class="payment-image" alt="Payment Image" title="Click to enlarge">';
                    }
                    return '<span class="text-muted">No Image</span>';
                }
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
                data: 'coupon_info',
                name: 'coupon.code',
                orderable: false,
                render: function(data, type, row) {
                    if (data && data.formatted) {
                        return '<span>' +
                               data.formatted + '</span>';
                    }
                    return '<span class="text-muted">No Coupon</span>';
                }
            },
            {
                data: 'payment_method',
                name: 'payment_method'
            },
            {
                data: 'service_titles',
                name: 'service_titles',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    if (data && data.length > 0) {
                        const titles = data.join('<br>');
                        return '<span class="service-titles" title="' + titles + '">' + titles + '</span>';
                    }
                    return '<span class="text-muted">No Services</span>';
                }
            },
            {
                data: 'amount_before_coupon',
                name: 'amount_before_coupon',
                render: function(data, type, row) {
                    return data ? data + ' EGP' : '-';
                }
            },
            {
                data: 'amount_after_coupon',
                name: 'amount_after_coupon',
                render: function(data, type, row) {
                    return data ? data + ' EGP' : '-';
                }
            },
            {
                data: 'amount_confirmed',
                name: 'amount_confirmed',
                render: function(data, type, row) {
                    return data ? data + ' EGP' : '-';
                }
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

    // Update Amount Button Handler
    $(document).on('click', '.update-amount-btn', function (e) {
        e.preventDefault();
        const paymentId = $(this).data('payment-id');
        const currentAmount = $(this).data('current-amount');
        const expectedAmount = $(this).data('expected-amount');

        Swal.fire({
            title: '<i class="fas fa-edit text-warning"></i> Update Amount',
            html: `
                <p><strong>Update Confirmed Amount for Payment #${paymentId}</strong></p>
                <div class="form-group mt-3">
                    <label for="swal-amount-input" class="form-label">Confirmed Amount (EGP)</label>
                    <input type="number"
                           step="0.01"
                           id="swal-amount-input"
                           class="swal2-input"
                           value="${currentAmount}"
                           placeholder="Enter amount">
                    <div class="mt-2 text-muted">
                        <small><strong>Current:</strong> ${currentAmount} EGP | <strong>Expected:</strong> ${expectedAmount} EGP</small>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save"></i> Update Amount',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            preConfirm: () => {
                const amount = document.getElementById('swal-amount-input').value;
                if (!amount || amount <= 0) {
                    Swal.showValidationMessage('Please enter a valid amount');
                    return false;
                }
                return amount;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const newAmount = result.value;

                Swal.fire({
                    title: 'Updating...',
                    text: 'Updating payment amount...',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/en/admin/payment-details/${paymentId}/update-amount`;

                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken.getAttribute('content');
                    form.appendChild(csrfInput);
                } else {
                    console.error('CSRF token not found');
                    Swal.fire('Error', 'Security token not found. Please refresh the page.', 'error');
                    return;
                }

                // Add method field for PUT
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);

                // Add amount input
                const amountInput = document.createElement('input');
                amountInput.type = 'hidden';
                amountInput.name = 'amount';
                amountInput.value = newAmount;
                form.appendChild(amountInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Update Coupon SweetAlert2 handler
    $(document).on('click', '.update-coupon-btn', function (e) {
        e.preventDefault();
        const paymentId = $(this).data('payment-id');
        const currentCouponId = $(this).data('current-coupon-id');
        const currentCouponCode = $(this).data('current-coupon-code');
        const amountBeforeCoupon = $(this).data('amount-before-coupon');

        // Get available coupons (you may need to make an AJAX call to get this data)
        // For now, I'll use the coupons from the filter dropdown
        const couponOptions = $('#filter-coupon option').map(function() {
            return {
                value: $(this).val(),
                text: $(this).text()
            };
        }).get();

        let couponOptionsHtml = '<option value="">No Coupon</option>';
        couponOptions.forEach(option => {
            if (option.value) {
                const selected = option.value == currentCouponId ? 'selected' : '';
                couponOptionsHtml += `<option value="${option.value}" ${selected}>${option.text}</option>`;
            }
        });

        Swal.fire({
            title: '<i class="fas fa-tag text-info"></i> Update Coupon',
            html: `
                <p><strong>Update Coupon for Payment #${paymentId}</strong></p>
                <div class="form-group mt-3">
                    <label for="swal-coupon-select" class="form-label">Select Coupon</label>
                    <select id="swal-coupon-select" class="swal2-input" style="width: 80%; height: auto; padding: 10px;">
                        ${couponOptionsHtml}
                    </select>
                    <div class="mt-2 text-muted">
                        <small><strong>Current:</strong> ${currentCouponCode} | <strong>Amount Before Coupon:</strong> ${amountBeforeCoupon} EGP</small>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save"></i> Update Coupon',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            preConfirm: () => {
                const couponId = document.getElementById('swal-coupon-select').value;
                return couponId;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const newCouponId = result.value;

                Swal.fire({
                    title: 'Updating...',
                    text: 'Updating payment coupon...',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/en/admin/payment-details/${paymentId}/update-coupon`;

                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken.getAttribute('content');
                    form.appendChild(csrfInput);
                } else {
                    console.error('CSRF token not found');
                    Swal.fire('Error', 'Security token not found. Please refresh the page.', 'error');
                    return;
                }

                // Add method field for PUT
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);

                // Add coupon_id input
                const couponInput = document.createElement('input');
                couponInput.type = 'hidden';
                couponInput.name = 'coupon_id';
                couponInput.value = newCouponId;
                form.appendChild(couponInput);

                document.body.appendChild(form);
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
