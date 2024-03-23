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
                            {{-- @can('user.create')
                                <div class="card-header" style="display: flex;justify-content: end">
                                    <a href="{{ route('user.all_user.create') }}" class="btn btn-primary"
                                        style="color: white; text-decoration: none;">
                                        {{ __('main.create_user') }}
                                    </a>
                                </div>
                            @endcan --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.image') }}</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.email') }}</th>
                                            <th>{{ __('attributes.phone') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>
                                                    <x-custom.profile-picture :user="$user" size="50" />
                                                </td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->phone }}</td>
                                                <td>
                                                    <x-custom.status-span :status="$user->status" />
                                                </td>


                                                <td>
                                                    {{-- @can('user.edit') --}}
                                                    <x-custom.edit-button route="admin.users.edit" :id="$user->id" />

                                                    {{-- @endcan --}}

                                                    <x-custom.change-status-button :status="$user->status"
                                                        route="admin.users.status" :id="$user->id" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.image') }}</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.email') }}</th>
                                            <th>{{ __('attributes.phone') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
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
