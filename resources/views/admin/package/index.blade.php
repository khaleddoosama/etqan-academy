@extends('admin.master')
@section('title')
{{ __('attributes.packages') }}
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ __('attributes.packages') }}" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @can('package.create')
                        <x-custom.create-button route="admin.packages.create"
                            title="{{ __('buttons.create_package') }}" />
                        @endcan
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.title') }}</th>
                                        <th>{{ __('attributes.description') }}</th>
                                        <th>{{ __('attributes.programs') }}</th>
                                        <th>{{ __('main.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packages as $package)
                                    <tr>
                                        <td>{{ $package->id }}</td>
                                        <td>{{ $package->title }}</td>
                                        <td>{{ $package->description }}</td>
                                        <td>
                                            @foreach ($package->programs() as $program)
                                            <p class="mb-0 fs-6">{{ $program->name }}</p>
                                            @endforeach
                                        </td>
                                        <td>
                                            @can('package.edit')
                                            <x-custom.edit-button route="admin.packages.edit"
                                                id="{{ $package->id }}" />
                                            @endcan

                                            @can('package.delete')
                                            <x-custom.delete-button route="admin.packages.destroy"
                                                id="{{ $package->id }}" />
                                            @endcan

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.title') }}</th>
                                        <th>{{ __('attributes.description') }}</th>
                                        <th>{{ __('attributes.programs') }}</th>
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
