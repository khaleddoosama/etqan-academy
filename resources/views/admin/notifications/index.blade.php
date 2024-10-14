@extends('admin.master')
@section('title')
    {{ __('attributes.notifications') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.notifications') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                {{-- <h3 class="card-title">All Notifications</h3> --}}


                                <form action="{{ route('admin.notifications.index') }}" method="GET"
                                    class="form-inline d-inline">
                                    <input type="text" name="search" class="form-control form-control-sm mr-2"
                                        placeholder="Search notifications" value="{{ request('search') }}">
                                    <button type="submit"
                                        class="btn btn-sm btn-primary">{{ __('buttons.search') }}</button>


                                    <div class="float-right d-flex justify-content-between align-items-center gap-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="float-right form-control" id="reservation"
                                                style="width: 225px" name="daterange" value="{{ request('daterange') }}">
                                        </div>

                                        <div class="input-group" style="width: auto">
                                            <a href="{{ route('admin.notifications.read') }}" class="btn btn-sm btn-primary"
                                                style="min-width: 123px">
                                                {{ __('buttons.mark_all_as_read') }}</a>
                                        </div>
                                    </div>
                                </form>

                            </div>
                            <div class="card-body">
                                @if ($notifications->isEmpty())
                                    <p>You have no notifications.</p>
                                @else
                                    <ul class="list-group">
                                        @foreach ($notifications as $notification)
                                            <li class="list-group-item"
                                                @if ($notification->read_at == null) style="background-color:#f1f4f8" @endif>
                                                <a href="#" class="dropdown-item notification"
                                                    data-id="{{ $notification->id }}"
                                                    data-url="{{ $notification->data['action'] }}">
                                                    <i class="mr-2 {{ $notification->data['icon'] }}"></i>
                                                    {{ $notification->data['title'] }}
                                                    <span class="float-right text-sm text-muted"
                                                        title="{{ $notification->created_at }}">{{ $notification->created_at->diffForHumans() }}</span>
                                                    <div class="pl-1 mx-4 dropdown-message">
                                                        <p class="text-sm">{{ $notification->data['message'] }}</p>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-3">
                                        {{ $notifications->appends(['search' => request('search')])->links() }}
                                    </div>
                                @endif
                            </div>
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
        $(function() {
            // Initialize date range picker
            $('#reservation').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            // Update the input value when a date range is selected and submit the form
            $('#reservation').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                    'MM/DD/YYYY'));
                $(this).closest('form').submit(); // Submit the form when date range is applied
            });

            // Clear the input field if the date range is canceled
            $('#reservation').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endsection
