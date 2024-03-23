@extends('admin.master')
@section('title')
    {{ __('attributes.support') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.support') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            {{-- @can('contact.create')
                                <x-custom.create-button route="admin.support.create"
                                    title="{{ __('buttons.create_contact') }}" />
                            @endcan --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.email') }}</th>
                                            <th>{{ __('attributes.subject') }}</th>
                                            <th>{{ __('attributes.message') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            {{-- <th>{{ __('main.actions') }}</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contacts as $contact)
                                            <tr>
                                                <td>{{ $contact->id }}</td>
                                                <td>{{ $contact->name }}</td>
                                                <td>{{ $contact->email }}</td>
                                                <td>{{ $contact->subject }}</td>
                                                <td>{{ $contact->message }}</td>
                                                <td>{{ $contact->created_at }}</td>
                                                {{-- <td>
                                                    @can('contact.edit')
                                                        <x-custom.edit-button route="admin.support.edit"
                                                            id="{{ $contact->id }}" />
                                                    @endcan

                                                    @can('contact.delete')
                                                    <x-custom.delete-button route="admin.support.destroy"
                                                        id="{{ $contact->id }}" />


                                                    @endcan

                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.email') }}</th>
                                            <th>{{ __('attributes.subject') }}</th>
                                            <th>{{ __('attributes.message') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            {{-- <th>{{ __('main.actions') }}</th> --}}
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
