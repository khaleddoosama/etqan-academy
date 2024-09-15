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
                                <h3 class="card-title">All Notifications</h3>
                                <a href="{{ route('admin.notifications.read') }}" class="float-right btn btn-sm btn-primary">
                                    {{ __('buttons.mark_all_as_read') }}</a>
                            </div>
                            <div class="card-body">
                                @if ($notifications->isEmpty())
                                    <p>You have no notifications.</p>
                                @else
                                    <ul class="list-group">
                                        @foreach ($notifications as $notification)
                                            <li class="list-group-item" @if ($notification->read_at == null) style="background-color:#f1f4f8" @endif>
                                                <a href="#" class="dropdown-item notification"
                                                    data-id="{{ $notification->id }}"
                                                    data-url="{{ $notification->data['action'] }}"
                                                    >
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
                                        {{ $notifications->links() }}
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
