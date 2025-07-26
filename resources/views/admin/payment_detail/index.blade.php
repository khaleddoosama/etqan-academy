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
                                        <th>{{ __('attributes.name') }}</th>
                                        <th>{{ __('attributes.email') }}</th>
                                        <th>{{ __('attributes.phone') }}</th>
                                        <th>Gateway</th>
                                        <th>invoice_id</th>
                                        <th>invoice_key</th>
                                        <th>{{ __('attributes.coupon') }}</th>
                                        <th>{{ __('attributes.payment_method') }}</th>
                                        <th>{{ __('attributes.amount') }}</th>
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
    });
</script>
<script src="{{ asset('js/payment-datatable-script.js') }}"></script>
@endsection
