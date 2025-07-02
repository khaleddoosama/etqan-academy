@extends('admin.master')
@section('title')
{{ $title }}
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ $title }}" />


    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        @can('user.create')
                        <div class="card-header" style="display: flex;justify-content: end">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary"
                                style="color: white; text-decoration: none;">
                                {{ __('buttons.create_user') }}
                            </a>
                        </div>
                        @endcan

                        <!-- Filter Form -->
                        <div class="card-body border-bottom">
                            <form id="filter-form" method="GET" action="{{ url()->current() }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="search">{{ __('main.search') }}</label>
                                        <input type="text" name="search" id="search" class="form-control"
                                               placeholder="{{ __('main.search_users_placeholder') }}"
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
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
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="sort_by">{{ __('main.sort_by') }}</label>
                                        <select name="sort_by" id="sort_by" class="form-control">
                                            <option value="id" {{ request('sort_by', 'id') == 'id' ? 'selected' : '' }}>{{ __('attributes.last_login') }}</option>
                                            <option value="first_name" {{ request('sort_by') == 'first_name' ? 'selected' : '' }}>{{ __('attributes.first_name') }}</option>
                                            <option value="last_name" {{ request('sort_by') == 'last_name' ? 'selected' : '' }}>{{ __('attributes.last_name') }}</option>
                                            <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>{{ __('attributes.email') }}</option>
                                            <option value="phone" {{ request('sort_by') == 'phone' ? 'selected' : '' }}>{{ __('attributes.phone') }}</option>
                                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>{{ __('attributes.status') }}</option>
                                            <option value="email_verified_at" {{ request('sort_by') == 'email_verified_at' ? 'selected' : '' }}>{{ __('attributes.email_verified_at') }}</option>
                                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>{{ __('attributes.created_at') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="sort_direction">{{ __('main.sort_direction') }}</label>
                                        <select name="sort_direction" id="sort_direction" class="form-control">
                                            <option value="desc" {{ request('sort_direction', 'desc') == 'desc' ? 'selected' : '' }}>{{ __('main.descending') }}</option>
                                            <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>{{ __('main.ascending') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-search"></i> {{ __('buttons.search') }}
                                            </button>
                                            <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">
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
                            <div id="users-loading" class="text-center" style="display: none;">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">{{ __('main.loading') }}...</p>
                            </div>

                            <!-- Users table container -->
                            <div id="users-container">
                                @include('admin.user.users-table', ['users' => $users])
                            </div>
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

            // AJAX Pagination
            $(document).on('click', '.ajax-pagination', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                loadUsersPage(url);
            });

            // AJAX Filter Form
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var formData = $(this).serialize();
                loadUsersPage(url + '?' + formData);
            });

            // Auto-submit on per_page change
            $('#per_page').on('change', function() {
                $('#filter-form').trigger('submit');
            });

            // Auto-submit on sort_by change
            $('#sort_by').on('change', function() {
                $('#filter-form').trigger('submit');
            });

            // Auto-submit on sort_direction change
            $('#sort_direction').on('change', function() {
                $('#filter-form').trigger('submit');
            });

            // Search with debounce
            let searchTimeout;
            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    $('#filter-form').trigger('submit');
                }, 500); // 500ms delay
            });

            function loadUsersPage(url) {
                // Show loading spinner
                $('#users-loading').show();
                $('#users-container').hide();

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        $('#users-container').html(response).show();
                        $('#users-loading').hide();

                        // Update browser URL without page reload
                        if (history.pushState) {
                            history.pushState(null, null, url);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#users-loading').hide();
                        $('#users-container').show();
                        console.error('Error loading users:', error);

                        // Show error message if toastr is available
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Error loading users. Please try again.');
                        } else {
                            alert('Error loading users. Please try again.');
                        }
                    }
                });
            }

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(e) {
                loadUsersPage(window.location.href);
            });

            // Handle column sorting
            $(document).on('click', '.sort-link', function(e) {
                e.preventDefault();
                var sortBy = $(this).data('sort');
                var currentSortBy = $('#sort_by').val();
                var currentSortDirection = $('#sort_direction').val();

                // If clicking the same column, toggle direction
                if (sortBy === currentSortBy) {
                    $('#sort_direction').val(currentSortDirection === 'asc' ? 'desc' : 'asc');
                } else {
                    // If clicking a different column, set it and default to asc
                    $('#sort_by').val(sortBy);
                    $('#sort_direction').val('asc');
                }

                // Submit the form
                $('#filter-form').trigger('submit');
            });
        });
    </script>
@endsection
