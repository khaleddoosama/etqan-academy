@extends('admin.master')
@section('title')
    {{ __('main.admin_logs') }} - {{ $admin->name }}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.admin_logs') }} - {{ $admin->name }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Back button -->
                        <div class="mb-3">
                            <a href="{{ route('admin.all_admin.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('buttons.back') }}
                            </a>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('main.admin_logs') }} - {{ $admin->name }}</h3>
                            </div>
                            <!-- /.card-header -->

                            <!-- Filters -->
                            <div class="card-body">
                                <form id="logs-filter-form" class="mb-4">
                                    <div class="row">
                                        <!-- Log Type Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="log_name">
                                                    {{ __('main.log_type') }}
                                                    <i class="fas fa-info-circle text-muted"
                                                       data-toggle="tooltip"
                                                       title="{{ __('main.filter_by_log_type_tooltip') }}"></i>
                                                </label>
                                                <select name="log_name" id="log_name" class="form-control">
                                                    <option value="">{{ __('main.all_log_types') }}</option>
                                                    @foreach($logTypes as $type)
                                                        <option value="{{ $type }}" {{ request('log_name') == $type ? 'selected' : '' }}>
                                                            {{ $type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Date From Filter -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="date_from">
                                                    {{ __('main.date_from') }}
                                                    <i class="fas fa-info-circle text-muted"
                                                       data-toggle="tooltip"
                                                       title="{{ __('main.filter_by_date_from_tooltip') }}"></i>
                                                </label>
                                                <input type="date" name="date_from" id="date_from" class="form-control"
                                                       value="{{ request('date_from') }}">
                                            </div>
                                        </div>

                                        <!-- Date To Filter -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="date_to">
                                                    {{ __('main.date_to') }}
                                                    <i class="fas fa-info-circle text-muted"
                                                       data-toggle="tooltip"
                                                       title="{{ __('main.filter_by_date_to_tooltip') }}"></i>
                                                </label>
                                                <input type="date" name="date_to" id="date_to" class="form-control"
                                                       value="{{ request('date_to') }}">
                                            </div>
                                        </div>

                                        <!-- Causer/Performer Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="causer_type">
                                                    {{ __('main.performed_by') }}
                                                    <i class="fas fa-info-circle text-muted"
                                                       data-toggle="tooltip"
                                                       title="{{ __('main.filter_by_performer_tooltip') }}"></i>
                                                </label>
                                                <select name="causer_type" id="causer_type" class="form-control">
                                                    <option value="">{{ __('main.all_actions') }}</option>
                                                    <option value="performed_by_user" {{ request('causer_type') == 'performed_by_user' ? 'selected' : '' }}>
                                                        {{ __('main.performed_by_admin') }}
                                                    </option>
                                                    <option value="performed_on_user" {{ request('causer_type') == 'performed_on_user' ? 'selected' : '' }}>
                                                        {{ __('main.performed_on_admin') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Per Page Filter -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="per_page">
                                                    {{ __('main.per_page') }}
                                                    <i class="fas fa-info-circle text-muted"
                                                       data-toggle="tooltip"
                                                       title="{{ __('main.items_per_page_tooltip') }}"></i>
                                                </label>
                                                <select name="per_page" id="per_page" class="form-control">
                                                    <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                                                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                                    <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                                                    <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                                                    <option value="500" {{ request('per_page', 25) == 500 ? 'selected' : '' }}>500</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="button" id="apply-filters" class="btn btn-primary">
                                                <i class="fas fa-search"></i> {{ __('buttons.apply_filters') }}
                                            </button>
                                            <button type="button" id="clear-filters" class="btn btn-secondary ml-2">
                                                <i class="fas fa-times"></i> {{ __('buttons.clear_filters') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Logs Table Container -->
                                <div id="logs-table-container">
                                    @include('admin.admin.logs-table', ['logs' => $logs])
                                </div>
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
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // AJAX form submission for filters
    function loadLogs(url = null, pushState = true) {
        var formData = $('#logs-filter-form').serialize();
        var targetUrl = url || '{{ route("admin.admins.logs", $admin->id) }}';

        if (formData) {
            targetUrl += (targetUrl.includes('?') ? '&' : '?') + formData;
        }

        $.ajax({
            url: targetUrl,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#logs-table-container').html(response);

                // Update browser URL if requested
                if (pushState) {
                    window.history.pushState(null, '', targetUrl);
                }
            },
            error: function(xhr) {
                console.error('Error loading logs:', xhr);
                toastr.error('{{ __("main.error_loading_data") }}');
            }
        });
    }

    // Apply filters
    $('#apply-filters').click(function() {
        loadLogs();
    });

    // Clear filters
    $('#clear-filters').click(function() {
        $('#logs-filter-form')[0].reset();
        loadLogs();
    });

    // Auto-apply filters on change
    $('#logs-filter-form select, #logs-filter-form input').change(function() {
        loadLogs();
    });

    // Handle pagination clicks
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (url) {
            loadLogs(url);
        }
    });

    // Handle browser back/forward
    window.addEventListener('popstate', function() {
        loadLogs(window.location.href, false);
    });
});
</script>
@endsection
