@extends('admin.master')
@section('title')
    {{ __('attributes.admins') }}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.admins') }}" />


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            @can('admin.create')
                                <div class="card-header" style="display: flex;justify-content: end">
                                    <a href="{{ route('admin.all_admin.create') }}" class="btn btn-primary"
                                        style="color: white; text-decoration: none;">
                                        {{ __('buttons.create_admin') }}
                                    </a>
                                </div>
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.email') }}</th>
                                            <th>{{ __('attributes.phone') }}</th>
                                            <th>{{ __('attributes.role') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($admins as $admin)
                                            <tr>
                                                <td title="{{ $admin->UserOnline() ? 'Online' : Carbon\Carbon::parse($admin->last_login)->diffForHumans() }}">
                                                    {!! $admin->UserOnline()
                                                        ? "<i class='fas fa-circle text-success'></i>"
                                                        : "<i class='fas fa-circle text-danger'></i>" !!}

                                                    {{ $admin->id }}</td>
                                                <td>{{ $admin->name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>{{ $admin->phone }}</td>
                                                <td>
                                                    @foreach ($admin->roles as $role)
                                                        <span class="badge badge-pill bg-danger">{{ $role->name }}</span>
                                                    @endforeach
                                                </td>

                                                <td>
                                                    @can('admin.edit')
                                                        </a><x-custom.edit-button route="admin.all_admin.edit"
                                                            id="{{ $admin->id }}" />
                                                    @endcan
                                                    @can('admin.delete')
                                                        <x-custom.delete-button route="admin.all_admin.destroy"
                                                            id="{{ $admin->id }}" />
                                                    @endcan
                                                    <a href="{{ route('admin.admins.logs', $admin->id) }}"
                                                       class="btn btn-info btn-md"
                                                       title="{{ __('main.view_logs') }}"
                                                       data-toggle="tooltip">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.email') }}</th>
                                            <th>{{ __('attributes.phone') }}</th>
                                            <th>{{ __('attributes.role') }}</th>
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

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
