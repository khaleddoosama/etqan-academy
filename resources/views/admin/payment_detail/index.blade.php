@extends('admin.master')
@section('title')
{{ __('attributes.payment_details') }}
@endsection
@section('styles')
<!-- remove padding and margin in table -->
<style>
    /* Custom search input styling */
    #search {
        transition: all 0.3s ease;
    }

    #search:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }

    .input-group .input-group-text {
        border-color: #ced4da;
    }

    .input-group:focus-within .input-group-text {
        border-color: #007bff;
    }

    .input-group:focus-within .btn-outline-secondary {
        border-color: #007bff;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .card-header .row>.col-md-6 {
            margin-bottom: 10px;
        }

        .card-header .input-group {
            max-width: 100% !important;
        }
    }

    /* Button hover effects */
    #clear-search:hover {
        background-color: #f8f9fa;
        border-color: #6c757d;
    }

    #reset-filters:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    /* Image styles for table */
    .payment-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .payment-image:hover {
        transform: scale(1.05);
    }

    /* Modal styles for image popup */
    .image-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        cursor: pointer;
    }

    .image-modal img {
        display: block;
        margin: auto;
        max-width: 90%;
        max-height: 90%;
        margin-top: 5%;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    .image-modal .close {
        position: absolute;
        top: 20px;
        right: 35px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .image-modal .close:hover {
        color: #ccc;
    }

    /* Service titles styling */
    .service-titles {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* SweetAlert2 custom styling for amount input */
    .swal2-input {
        margin: 10px auto !important;
        width: 80% !important;
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
        padding: 10px !important;
        font-size: 16px !important;
    }

    .swal2-input:focus {
        border-color: #ffc107 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
    }

    /* Action button spacing */
    .btn-sm {
        margin-left: 2px !important;
    }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ __('attributes.payment_details') }}" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @include('admin.payment_detail.partials.statistics')

            <div class="row">
                <div class="col-12">
                    @include('admin.payment_detail.partials.filters')

                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="#" id="download-filtered" class="btn btn-primary btn-sm">
                                            <i class="fas fa-download mr-1"></i>
                                            {{ __('buttons.download_sheets') }}
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <!-- Custom Search -->
                                        <div class="input-group" style="max-width: 350px;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-search text-muted"></i>
                                                </span>
                                            </div>
                                            <input type="search"
                                                id="search"
                                                class="form-control border-left-0"
                                                placeholder="{{ __('datatable.search') }} {{ strtolower(__('attributes.payment_details')) }}..."
                                                aria-label="{{ __('datatable.search') }}"
                                                autocomplete="off"
                                                value="{{ request('search') }}"
                                                style="box-shadow: none;">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ __('attributes.image') }}</th>
                                        <th>{{ __('attributes.name') }}</th>
                                        <th>{{ __('attributes.email') }}</th>
                                        <th>{{ __('attributes.phone') }}</th>
                                        <th>Gateway</th>
                                        <th>invoice_id</th>
                                        <th>invoice_key</th>
                                        <th>{{ __('attributes.coupon') }}</th>
                                        <th>{{ __('attributes.payment_method') }}</th>
                                        <th>{{ __('attributes.services') }}</th>
                                        <th>{{ __('attributes.amount_before_coupon') }}</th>
                                        <th>{{ __('attributes.amount_after_coupon') }}</th>
                                        <th>{{ __('attributes.confirmed_amount') }}</th>
                                        <th>{{ __('attributes.status') }}</th>
                                        <th>{{ __('attributes.created_at') }}</th>
                                        <th>{{ __('main.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>

                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

</div>

<!-- Image Modal -->
<div id="imageModal" class="image-modal">
    <span class="close">&times;</span>
    <img id="modalImage" src="" alt="Payment Image">
</div>
@endsection
@section('scripts')
<script>
    // Set global variables for the payment datatable script
    window.paymentDataUrl = "{{ route('admin.payment_details.data') }}";
    window.paymentDownloadUrl = "{{ route('admin.payment_details.download') }}";
    window.datatableLanguage = {
        emptyTable: "{{ __('datatable.no_data_available_in_table') }}",
        lengthMenu: "{{ __('datatable.show') }} _MENU_ {{ __('datatable.entries') }}",
        search: "{{ __('datatable.search') }}:",
        zeroRecords: "{{ __('datatable.no_matching_records_found') }}",
        paginate: {
            next: "{{ __('datatable.next') }}",
            previous: "{{ __('datatable.previous') }}"
        },
        info: "{{ __('datatable.showing from _START_ to _END_ of _TOTAL_ entries') }}",
        infoEmpty: "{{ __('datatable.showing 0 to 0 of 0 entries') }}",
        infoFiltered: "({{ __('datatable.filtered from _MAX_ total entries') }})",
        thousands: ",",
        loadingRecords: "{{ __('datatable.loading...') }}",
        processing: "{{ __('datatable.processing...') }}",
    };

    // Handle download with current filters
    $(document).ready(function() {
        $('#download-filtered').on('click', function(e) {
            e.preventDefault();

            const $btn = $(this);
            const originalText = $btn.html();

            // Show loading state
            $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>{{ __("buttons.downloading") }}...')
                .prop('disabled', true);

            // Get current filter values
            const filters = {
                search: $('#search').val(),
                user_id: $('#filter-user').val(),
                gateway: $('#filter-gateway').val(),
                status: $('#filter-status').val(),
                coupon_id: $('#filter-coupon').val(),
                from_created_at: $('#filter-from').val(),
                to_created_at: $('#filter-to').val()
            };

            // Build query string
            const queryParams = new URLSearchParams();
            Object.keys(filters).forEach(key => {
                if (filters[key] && filters[key].trim() !== '') {
                    queryParams.set(key, filters[key]);
                }
            });

            // Create download URL with filters
            const downloadUrl = window.paymentDownloadUrl + (queryParams.toString() ? '?' + queryParams.toString() : '');

            // Create a temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Reset button state after a short delay
            setTimeout(function() {
                $btn.html(originalText).prop('disabled', false);
            }, 2000);
        });

        // Image modal functionality
        $(document).on('click', '.payment-image', function() {
            const imageSrc = $(this).attr('src');
            $('#modalImage').attr('src', imageSrc);
            $('#imageModal').fadeIn(300);
        });

        // Close modal when clicking close button or outside image
        $('#imageModal .close, #imageModal').on('click', function(e) {
            if (e.target === this) {
                $('#imageModal').fadeOut(300);
            }
        });

        // Close modal with ESC key
        $(document).keydown(function(e) {
            if (e.keyCode === 27) { // ESC key
                $('#imageModal').fadeOut(300);
            }
        });
    });
</script>
<script src="{{ asset('js/payment-datatable-script.js') }}"></script>
@endsection
