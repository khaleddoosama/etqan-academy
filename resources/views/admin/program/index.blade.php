@extends('admin.master')
@section('title')
    {{ __('attributes.programs') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.programs') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            @can('program.create')
                            <x-custom.create-button route="admin.programs.create"
                                title="{{ __('buttons.create_program') }}" />
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.description') }}</th>
                                            <th>{{ __('attributes.icon') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($programs as $program)
                                            <tr>
                                                <td>{{ $program->id }}</td>
                                                <td>{{ $program->name }}</td>
                                                <td>{{ $program->description }}</td>
                                                <td><img src="{{ $program->icon_url }}" alt=""
                                                        width="50" /></td>
                                                <td>
                                                    @can('program.edit')
                                                    <x-custom.edit-button route="admin.programs.edit"
                                                        id="{{ $program->id }}" />
                                                    @endcan

                                                    @can('program.delete')
                                                    <x-custom.delete-button route="admin.programs.destroy"
                                                        id="{{ $program->id }}" />


                                                    @endcan

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.description') }}</th>
                                            <th>{{ __('attributes.icon') }}</th>
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
