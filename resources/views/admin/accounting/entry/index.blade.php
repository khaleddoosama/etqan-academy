@extends('admin.master')
@section('title')
{{ __('Accounting Entries') }}
@endsection
@section('styles')
<!-- Custom styling for accounting entries -->
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

    /* Action button spacing */
    .btn-sm {
        margin-left: 2px !important;
    }

    /* Amount styling */
    .amount-positive {
        color: #28a745;
        font-weight: bold;
    }

    .amount-negative {
        color: #dc3545;
        font-weight: bold;
    }

    .amount-neutral {
        color: #6c757d;
        font-weight: bold;
    }

    /* Type badge styling */
    .type-income {
        background-color: #28a745;
    }

    .type-expense {
        background-color: #dc3545;
    }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ __('Accounting Entries') }}" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @include('admin.accounting.entry.partials.statistics')

            <div class="row">
                <div class="col-12">
                    @include('admin.accounting.entry.partials.filters')

                    <div class="card row col-12">
                        @can('accounting_entry.create')
                        <x-custom.create-button route="admin.accounting.entries.create"
                            title="{{ __('Create New Entry') }}" />
                        @endcan
                        <div class="card-header">
                            <div class="row align-items-center">


                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end">
                                        <!-- Custom Search -->
                                        <div class="input-group " style="max-width: 350px;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-search text-muted"></i>
                                                </span>
                                            </div>
                                            <input type="search"
                                                id="search"
                                                class="form-control border-left-0  "
                                                placeholder="{{ __('datatable.search') }} entries..."
                                                aria-label="{{ __('datatable.search') }}"
                                                autocomplete="off"
                                                value="{{ request('search') }}"
                                                style="box-shadow: none;">
                                            <div class="input-group-append ml-2">
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
                                        <th>{{ __('attributes.title') }}</th>
                                        <th>{{ __('attributes.description') }}</th>
                                        <th>{{ __('attributes.category') }}</th>
                                        <th>{{ __('attributes.type') }}</th>
                                        <th>{{ __('attributes.amount') }}</th>
                                        <th>{{ __('attributes.signed_amount') }}</th>
                                        <th>{{ __('attributes.transaction_date') }}</th>
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
    // Set global variables for the accounting entry datatable script
    window.entryDataUrl = "{{ route('admin.accounting.entries.data') }}";
    window.entryStatisticsUrl = "{{ route('admin.accounting.entries.statistics') }}";
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
</script>
<script src="{{ asset('js/Accounting/accounting-entry-datatable.js') }}"></script>
@endsection