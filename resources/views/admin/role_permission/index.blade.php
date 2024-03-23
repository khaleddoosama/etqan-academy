@extends('admin.master')
@section('title')
    {{ __('main.all_roles_permission') }}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.all_roles_permission') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            @can('role_permission.create')
                                <div class="card-header" style="display: flex;justify-content: end">
                                    <a href="{{ route('admin.role_permission.create') }}" class="btn btn-primary"
                                        style="color: white; text-decoration: none;">
                                        {{ __('main.create_role_permissions') }}
                                    </a>
                                </div>
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.permissions') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $role)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $role->name }}</td>
                                                <td>
                                                    @foreach ($role->permissions as $permission)
                                                        <span class="badge badge-success">{{ $permission->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @can('role_permission.edit')
                                                        <a href="{{ route('admin.role_permission.edit', $role->id) }}"
                                                            class="btn btn-primary"
                                                            style="color: white; text-decoration: none;">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    {{-- <form action="{{ route('admin.role_permission.destroy', $role->id) }}"
                                                        method="POST" style="display: inline-block" id="delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger delete-button"
                                                            style="color: white; text-decoration: none;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form> --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.permissions') }}</th>
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
