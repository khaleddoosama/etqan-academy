@extends('admin.master')
@section('title')
    {{ __('attributes.logs') }} - {{ ucfirst($type) }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.logs') }} - {{ ucfirst($type) }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Back button -->
                        <div class="mb-3">
                            <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('buttons.back') }}
                            </a>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">{{ __('attributes.logs') }} - {{ ucfirst($type) }}</h3>
                                <form action="{{ route('admin.logs.bulk_delete', $type) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        title="{{ __('buttons.delete_all') }}"
                                        onclick="return confirm('Are you sure you want to delete all logs?')">>
                                        <i class="fas fa-trash"></i> {{ __('buttons.delete_all') }}
                                    </button>
                                </form>
                            </div>

                            <!-- Filters -->
                            <div class="card-body">
                                <form id="logs-filter-form" class="mb-4">
                                    <div class="row">
                                        <!-- Search Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="search">
                                                    {{ __('main.search') }}
                                                    <i class="fas fa-info-circle text-muted"
                                                       data-toggle="tooltip"
                                                       title="{{ __('main.search_logs_tooltip') }}"></i>
                                                </label>
                                                <input type="text" name="search" id="search" class="form-control"
                                                       value="{{ request('search') }}"
                                                       placeholder="{{ __('main.search_logs_placeholder') }}">
                                            </div>
                                        </div>

                                        <!-- Date From Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_from">
                                                    {{ __('main.date_from') }}
                                                    <i class="fas fa-info-circle text-muted"
                                                       data-toggle="tooltip"
                                                       title="Filter logs from this date and time"></i>
                                                </label>
                                                <input type="datetime-local" name="date_from" id="date_from" class="form-control"
                                                       value="{{ request('date_from') }}">
                                            </div>
                                        </div>

                                        <!-- Date To Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_to">
                                                    {{ __('main.date_to') }}
                                                    <i class="fas fa-info-circle text-muted"
                                                       data-toggle="tooltip"
                                                       title="Filter logs up to this date and time"></i>
                                                </label>
                                                <input type="datetime-local" name="date_to" id="date_to" class="form-control"
                                                       value="{{ request('date_to') }}">
                                            </div>
                                        </div>

                                        <!-- Per Page Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="per_page">
                                                    {{ __('main.per_page') }}
                                                    <i class="fas fa-info-circle text-muted"
                                                       data-toggle="tooltip"
                                                       title="{{ __('main.items_per_page_tooltip') }}"></i>
                                                </label>
                                                <div class="d-flex">
                                                    <select name="per_page" id="per_page" class="form-control mr-2">
                                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                                                    </select>
                                                    <div class="d-flex flex-column">
                                                        <button type="button" id="apply-filters" class="btn btn-primary btn-sm mb-1">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                        <button type="button" id="clear-filters" class="btn btn-secondary btn-sm">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Logs Table Container -->
                                <div id="logs-table-container">
                                    @include('admin.logs.logs-table', ['logs' => $logs, 'type' => $type])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
<style>
/* Improve datetime input styling */
input[type="datetime-local"] {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Responsive adjustments for smaller screens */
@media (max-width: 768px) {
    .col-md-3 {
        margin-bottom: 1rem;
    }
    
    .d-flex .btn {
        width: 100%;
        margin-bottom: 0.25rem;
    }
}
</style>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // AJAX form submission for filters
    function loadLogs(url = null, pushState = true) {
        var formData = $('#logs-filter-form').serialize();
        var targetUrl = url || '{{ route("admin.logs.show", $type) }}';

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
    $('#logs-filter-form select, #logs-filter-form input').on('change keyup', function() {
        // Debounce search input
        if ($(this).attr('type') === 'text') {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(function() {
                loadLogs();
            }, 500);
        } else if ($(this).attr('type') === 'datetime-local') {
            // Apply immediately for datetime inputs
            loadLogs();
        } else {
            loadLogs();
        }
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

    // Handle Enter key in search
    $('#search').keypress(function(e) {
        if (e.which == 13) {
            loadLogs();
            return false;
        }
    });
});
</script>
@endsection
