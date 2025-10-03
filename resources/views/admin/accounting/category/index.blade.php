@extends('admin.master')
@section('title')
    {{ __('attributes.categories') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.categories') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            @can('accounting_category.create')
                                <x-custom.create-button route="admin.accounting.categories.create"
                                    title="{{ __('buttons.create_category') }}" />
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.type') }}</th>
                                            <th>{{ __('attributes.description') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td>{{ $category->id }}</td>
                                                <td>{{ $category->name }}</td>
                                                <td><span class="badge badge-{{ $category->type->color()  }}">{{ $category->type }}</span></td>
                                                <td>{{ $category->description }}</td>
                                                <td>
                                                    @can('accounting_category.edit')
                                                        <x-custom.edit-button route="admin.accounting.categories.edit"
                                                            id="{{ $category->id }}" />
                                                    @endcan

                                                    @can('accounting_category.delete')
                                                    <x-custom.delete-button route="admin.accounting.categories.destroy"
                                                        id="{{ $category->id }}" />
                                                    @endcan

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.type') }}</th>
                                            <th>{{ __('attributes.description') }}</th>
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
