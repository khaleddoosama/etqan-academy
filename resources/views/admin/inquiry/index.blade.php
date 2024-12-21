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
                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.name') }}</th>
                                            <th>{{ __('attributes.email') }}</th>
                                            <th>{{ __('attributes.phone') }}</th>
                                            <th>{{ __('attributes.message') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($inquiries as $inquiry)
                                            @php
                                                // check if created_at is today
                                                $isToday = date('Y-m-d', strtotime($inquiry->created_at)) == date('Y-m-d');
                                            @endphp
                                            <tr class="{{ $isToday ? 'bg-dark' : '' }}">
                                                <td>{{ $inquiry->id }}</td>
                                                <td>{{ $inquiry->name }}</td>
                                                <td><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></td>
                                                <td>
                                                    <a href="https://wa.me/{{ $inquiry->phone }}" target="_blank">
                                                        {{ $inquiry->phone }}
                                                    </a>
                                                </td>
                                                <td>{!! Str::limit($inquiry->message, 50) !!}
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $inquiry->status == 'pending' ? 'warning' : 'success' }}">{{ $inquiry->status }}</span>
                                                </td>
                                                <td>{{ $inquiry->created_at }}</td>
                                                <td>
                                                    @can('inquiry.show')
                                                        <a href="{{ route('admin.inquiries.show', $inquiry) }}"
                                                            class="btn btn-success btn-sm" title="{{ __('main.show') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
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
                                            <th>{{ __('attributes.message') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
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
