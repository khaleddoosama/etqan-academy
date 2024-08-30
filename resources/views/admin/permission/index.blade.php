@extends('admin.master')
@section('title')
    {{ __('main.permissions') }}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.permissions') }}" />


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            {{-- @can('permission.create') --}}
                            <div class="card-header" style="display: flex;justify-content: end">
                                <a href="{{ route('admin.permission.create') }}" class="btn btn-primary"
                                    style="color: white; text-decoration: none;">
                                    {{ __('main.create_permission') }}
                                </a>
                            </div>
                            {{-- @endcan --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.module') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permissions as $permission)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $permission->name }}</td>
                                                <td>{{ $permission->module }}</td>
                                                <td>
                                                    {{-- @can('permission.edit') --}}
                                                    <x-custom.edit-button route="admin.permission.edit"
                                                        id="{{ $permission->id }}" />
                                                    {{-- @endcan --}}
                                                    {{-- @can('permission.delete') --}}

                                                    <x-custom.delete-button route="admin.permission.destroy"
                                                        id="{{ $permission->id }}" />
                                                    {{-- @endcan --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.module') }}</th>
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
