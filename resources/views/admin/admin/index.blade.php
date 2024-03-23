@extends('admin.master')
@section('title')
    {{ __('main.admins') }}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.admins') }}" />


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
                                        {{ __('main.create_admin') }}
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
                                            <th>{{ __('attributes.email') }}</th>
                                            <th>{{ __('attributes.phone') }}</th>
                                            <th>{{ __('attributes.role') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($admins as $admin)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
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
                                                        <a href="{{ route('admin.all_admin.edit', $admin->id) }}"
                                                            class="btn btn-primary"
                                                            style="color: white; text-decoration: none;">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('admin.delete')
                                                        <form action="{{ route('admin.all_admin.destroy', $admin->id) }}"
                                                            method="POST" style="display: inline-block" id="delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger delete-button"
                                                                style="color: white; text-decoration: none;">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
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
