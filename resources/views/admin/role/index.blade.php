@extends('admin.master')
@section('title')
    {{ __('main.roles') }}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.roles') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            {{-- @can('role.create') --}}
                            <div class="card-header" style="display: flex;justify-content: end">
                                <a href="{{ route('admin.role.create') }}" class="btn btn-primary"
                                    style="color: white; text-decoration: none;">
                                    {{ __('main.create_role') }}
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
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $role)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $role->name }}</td>
                                                <td>
                                                    {{-- @can('role.edit') --}}

                                                    <x-custom.edit-button route="admin.role.edit"
                                                        id="{{ $role->id }}" />
                                                    {{-- @endcan --}}
                                                    {{-- @can('role.delete') --}}

                                                    <x-custom.delete-button route="admin.role.destroy"
                                                        id="{{ $role->id }}" />
                                                    {{-- @endcan --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
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
