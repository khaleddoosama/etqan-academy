@extends('admin.master')
@section('title')
{{ __('attributes.user_logs') }} - {{ $user->first_name }} {{ $user->last_name }}
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ __('attributes.user_logs') }} - {{ $user->first_name }} {{ $user->last_name }}" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- User Info Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('attributes.user_information') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <x-custom.profile-picture :user="$user" size="100" />
                                </div>
                                <div class="col-md-10">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('attributes.name') }}:</strong></td>
                                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('attributes.email') }}:</strong></td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('attributes.phone') }}:</strong></td>
                                            <td>{{ $user->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('attributes.status') }}:</strong></td>
                                            <td><x-custom.status-span :status="$user->status" /></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logs Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('attributes.activity_logs') }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.users.active') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> {{ __('buttons.back') }}
                                </a>
                            </div>
                        </div>

                        <!-- Filter Form -->
                        <div class="card-body border-bottom">
                            <form id="filter-form" method="GET" action="{{ route('admin.users.logs', $user->id) }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="log_name">{{ __('attributes.log_name') }}</label>
                                        <select name="log_name" id="log_name" class="form-control">
                                            <option value="">{{ __('main.all') }}</option>
                                            @foreach($logNames as $logName)
                                            <option value="{{ $logName }}" {{ request('log_name') == $logName ? 'selected' : '' }}>
                                                {{ $logName }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="causer_type">
                                            {{ __('attributes.action_type') }}
                                            <i class="fas fa-info-circle text-info"
                                               title="{{ __('main.action_type_help') }}"
                                               data-toggle="tooltip"
                                               data-placement="top"></i>
                                        </label>
                                        <select name="causer_type" id="causer_type" class="form-control">
                                            <option value="all" {{ request('causer_type', 'all') == 'all' ? 'selected' : '' }}>{{ __('main.all') }}</option>
                                            <option value="performed_by_user" {{ request('causer_type') == 'performed_by_user' ? 'selected' : '' }}>🔹 {{ __('attributes.performed_by_user') }}</option>
                                            <option value="performed_on_user" {{ request('causer_type') == 'performed_on_user' ? 'selected' : '' }}>🔸 {{ __('attributes.performed_on_user') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_from">{{ __('attributes.date_from') }}</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_to">{{ __('attributes.date_to') }}</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label for="per_page">
                                            {{ __('attributes.per_page') }}
                                            <i class="fas fa-info-circle text-info"
                                               title="{{ __('main.per_page_help') }}"
                                               data-toggle="tooltip"
                                               data-placement="top"></i>
                                        </label>
                                        <select name="per_page" id="per_page" class="form-control">
                                            <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                                            <option value="500" {{ request('per_page', 25) == 500 ? 'selected' : '' }}>500</option>
                                        </select>
                                    </div>
                                    <div class="col-md-9">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-search"></i> {{ __('buttons.filter') }}
                                            </button>
                                            <a href="{{ route('admin.users.logs', $user->id) }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-times"></i> {{ __('buttons.clear') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- Loading spinner -->
                            <div id="logs-loading" class="text-center" style="display: none;">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">{{ __('main.loading') }}...</p>
                            </div>

                            <!-- Logs table container -->
                            <div id="logs-container">
                                @include('admin.user.logs-table', ['logs' => $logs, 'user' => $user])
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

<!-- Modals for log details -->
@foreach ($logs as $log)
<div class="modal fade" id="show-{{ $log->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('attributes.log_details') }} #{{ $log->id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>{{ __('attributes.id') }}:</strong></td>
                        <td>{{ $log->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('attributes.log_name') }}:</strong></td>
                        <td><span class="badge badge-secondary">{{ $log->log_name }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('attributes.description') }}:</strong></td>
                        <td>{!! $log->description !!}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('attributes.subject') }}:</strong></td>
                        <td>
                            @if($log->subject_type)
                            {{ $log->subject_type }} (ID: {{ $log->subject_id }})
                            @else
                            N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('attributes.causer') }}:</strong></td>
                        <td>
                            @if($log->causer)
                            {{ $log->causer->first_name }} {{ $log->causer->last_name }} ({{ $log->causer->email }})
                            @else
                            {{ __('attributes.system') }}
                            @endif
                        </td>
                    </tr>
                    <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
@endforeach
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // AJAX Pagination
        $(document).on('click', '.ajax-pagination', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            loadLogsPage(url);
        });

        // AJAX Filter Form
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var formData = $(this).serialize();
            loadLogsPage(url + '?' + formData);
        });

        // Auto-submit on filter change
        $('#log_name, #date_from, #date_to, #per_page, #causer_type').on('change', function() {
            $('#filter-form').trigger('submit');
        });

        function loadLogsPage(url) {
            // Show loading spinner
            $('#logs-loading').show();
            $('#logs-container').hide();

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#logs-container').html(response).show();
                    $('#logs-loading').hide();

                    // Update browser URL without page reload
                    if (history.pushState) {
                        history.pushState(null, null, url);
                    }
                },
                error: function(xhr, status, error) {
                    $('#logs-loading').hide();
                    $('#logs-container').show();
                    console.error('Error loading logs:', error);

                    // Show error message if toastr is available
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Error loading logs. Please try again.');
                    } else {
                        alert('Error loading logs. Please try again.');
                    }
                }
            });
        }

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function(e) {
            loadLogsPage(window.location.href);
        });
    });
</script>
@endsection
