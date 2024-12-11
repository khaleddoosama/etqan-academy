@extends('admin.master')
@section('title')
    {{ __('attributes.payment_details') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.payment_details') }}" />

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
                                            <th>{{ __('attributes.course') }}</th>
                                            <th>{{ __('attributes.whatsapp') }}</th>
                                            <th>{{ __('attributes.payment_type') }}</th>
                                            <th>{{ __('attributes.payment_method') }}</th>
                                            <th>{{ __('attributes.transfer_phone') }}</th>
                                            <th>{{ __('attributes.transfer_image') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paymentDetails as $paymentDetail)
                                            @php
                                                // check if created_at is today
                                                $isToday =
                                                    date('Y-m-d', strtotime($paymentDetail->created_at)) ==
                                                    date('Y-m-d');
                                            @endphp
                                            <tr class="{{ $isToday ? 'bg-dark' : '' }}">
                                                <td>{{ $paymentDetail->id }}</td>
                                                <td>{{ $paymentDetail->user->name ?? 'Guest' }}</td>
                                                <td style="max-width: 100px">
                                                    <a
                                                        href="mailto:{{ $paymentDetail->user->email ?? '' }}">{{ $paymentDetail->user->email ?? '' }}</a>
                                                </td>
                                                <td><a href="https://wa.me/{{ $paymentDetail->user->phone }}"
                                                        target="_blank">
                                                        {{ $paymentDetail->user->phone }}
                                                    </a></td>
                                                <td>
                                                    {{ $paymentDetail->course->title }}
                                                </td>
                                                <td><a href="https://wa.me/{{ $paymentDetail->whatsapp_number }}"
                                                        target="_blank">
                                                        {{ $paymentDetail->whatsapp_number }}
                                                    </a>
                                                </td>
                                                <td>{{ $paymentDetail->payment_type }}</td>
                                                <td>{{ $paymentDetail->payment_method }}</td>
                                                <td>{{ $paymentDetail->transfer_number ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ Storage::url($paymentDetail->transfer_image) }}"
                                                        target="_blank">
                                                        <img src="{{ Storage::url($paymentDetail->transfer_image) }}"
                                                            alt="transfer_image" width="50px" height="50px">
                                                    </a>
                                                </td>


                                                <td>
                                                    <span
                                                        class="badge bg-{{ $paymentDetail->status->Color() }}">{{ $paymentDetail->status }}</span>
                                                </td>

                                                <td title="{{ $paymentDetail->created_at }}">
                                                    {{ $paymentDetail->created_at->diffForHumans() }}</td>
                                                <td>
                                                    @can('payment_detail.show')
                                                        <a href="{{ route('admin.payment_details.show', $paymentDetail) }}"
                                                            class="btn btn-success mx-2" title="{{ __('main.show') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endcan
                                                    @can('payment_detail.status')
                                                        <x-custom.status :model="$paymentDetail" routeName="admin.payment_details.status" />
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
                                            <th>{{ __('attributes.course') }}</th>
                                            <th>{{ __('attributes.whatsapp') }}</th>
                                            <th>{{ __('attributes.payment_type') }}</th>
                                            <th>{{ __('attributes.payment_method') }}</th>
                                            <th>{{ __('attributes.transfer_phone') }}</th>
                                            <th>{{ __('attributes.transfer_image') }}</th>
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
