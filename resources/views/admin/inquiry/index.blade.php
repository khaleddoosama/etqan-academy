@extends('admin.master')
@section('title')
    {{ __('attributes.inquiries') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.inquiries') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.email') }}</th>
                                            <th>{{ __('attributes.phone') }}</th>
                                            <th>{{ __('attributes.message') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($inquiries as $inquiry)
                                            <tr>
                                                <td>{{ $inquiry->id }}</td>
                                                <td>{{ $inquiry->name }}</td>
                                                <td>{{ $inquiry->email }}</td>
                                                <td>{{ $inquiry->phone }}</td>
                                                <td>{!! Str::limit($inquiry->message, 50) !!}
                                                <td>{{ $inquiry->status }}</td>
                                                <td>
                                                    {{-- @can('inquiry.show') --}}
                                                    <a href="{{ route('admin.inquiries.show', $inquiry) }}"
                                                        class="btn btn-success btn-sm" title="{{ __('main.show') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    {{-- @endcan --}}
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
                                            <th>{{ __('attributes.message') }}</th>
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