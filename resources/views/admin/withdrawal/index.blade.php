@extends('admin.master')
@section('title')
    {{ __('attributes.withdrawal_requests') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.withdrawal_requests') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.user_name') }}</th>
                                            <th>{{ __('attributes.wallet_phone') }}</th>
                                            <th>{{ __('attributes.amount') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($withdrawalRequests as $withdrawal)
                                            <tr>
                                                <td>{{ $withdrawal->id }}</td>
                                                <td><a href="{{ route('admin.users.show', $withdrawal->user) }}">{{ $withdrawal->user->name }}</a></td>
                                                <td>{{ $withdrawal->wallet_phone }}</td>
                                                <td>{{ $withdrawal->points }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $withdrawal->status_color }}">
                                                        {{ $withdrawal->status_text }}
                                                </td>
                                                <td>{{ $withdrawal->created_at }}</td>
                                                <td>
                                                    @can('withdrawal.show')
                                                    <a href="{{ route('admin.withdrawal_requests.show', $withdrawal) }}"
                                                        class="btn btn-success btn-sm" title="{{ __('main.show') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.user_name') }}</th>
                                            <th>{{ __('attributes.wallet_phone') }}</th>
                                            <th>{{ __('attributes.amount') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </tfoot>
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
