@extends('admin.master')
@section('title')
    {{ __('attributes.instructors') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.instructors') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            @can('instructor.create')
                                <x-custom.create-button route="admin.instructors.create"
                                    title="{{ __('buttons.create_instructor') }}" />
                            @endcan
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
                                        @foreach ($instructors as $instructor)
                                            <tr>
                                                <td>{{ $instructor->id }}</td>
                                                <td>{{ $instructor->name }}</td>
                                                <td>
                                                    @can('instructor.edit')
                                                        <x-custom.edit-button route="admin.instructors.edit"
                                                            id="{{ $instructor->id }}" />
                                                    @endcan

                                                    @can('instructor.delete')
                                                        <x-custom.delete-button route="admin.instructors.destroy"
                                                            id="{{ $instructor->id }}" />
                                                    @endcan

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
