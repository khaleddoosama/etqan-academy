@extends('admin.master')
@section('title')
    {{ __('attributes.payment_detail') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.payment_detail') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body">

                                <p><strong>{{ __('attributes.name') }}:</strong>
                                    {{ $paymentDetail->user->name ?? 'Guest' }}</p>
                                <p><strong>{{ __('attributes.email') }}:</strong> <a
                                        href="mailto:{{ $paymentDetail->user->email ?? '' }}">{{ $paymentDetail->user->email ?? '' }}</a>
                                </p>
                                <p><strong>{{ __('attributes.phone') }}:</strong> <a
                                        href="https://wa.me/{{ $paymentDetail->user->phone }}" target="_blank">
                                        {{ $paymentDetail->user->phone }}
                                    </a></p>
                                <p><strong>{{ __('attributes.course') }}:</strong> {{ $paymentDetail->courseInstallment->course->title }}</p>

                                <p><strong>{{ __('attributes.whatsapp') }}:</strong> <a
                                        href="https://wa.me/{{ $paymentDetail->whatsapp_number }}" target="_blank">
                                        {{ $paymentDetail->whatsapp_number }}
                                    </a></p>


                                <p><strong>{{ __('attributes.transfer_phone') }}:</strong>{{ $paymentDetail->transfer_number }}
                                </p>
                                <p><strong>{{ __('attributes.transfer_image') }}:</strong>
                                    <a href="{{ Storage::url($paymentDetail->transfer_image) }}" target="_blank">
                                        <img src="{{ Storage::url($paymentDetail->transfer_image) }}" alt="Image"
                                            style="width: 100px">
                                    </a>
                                </p>

                                <form action="{{ route('admin.payment_details.update', $paymentDetail) }}" method="POST"
                                    class="d-flex align-items-end mb-2">
                                    @csrf
                                    @method('PUT')

                                    <label for="amount" class="mr-2">{{ __('attributes.amount') }}:</label>
                                    <input type="number" name="amount" class="form-control mr-2 w-auto" id="amount"
                                        value="{{ $paymentDetail->amount }}" min="0" step="0.01" required>

                                    <x-custom.form-submit text="{{ __('buttons.update') }}" class="btn-primary" />
                                </form>


                                <p><strong>{{ __('attributes.status') }}:</strong> <span
                                        class="badge badge-{{ $paymentDetail->status->Color() }}">{{ $paymentDetail->status }}</span>
                                </p>

                                <p><strong>{{ __('attributes.created_at') }}:</strong> {{ $paymentDetail->created_at }}
                                </p>

                                <p><strong>{{ __('attributes.approved_at') }}:</strong> {{ $paymentDetail->approved_at }}
                                </p>
                                <p><strong>{{ __('attributes.approved_by') }}:</strong>
                                    {{ $paymentDetail->approvedBy?->name }}
                                </p>
                                <p><strong>{{ __('attributes.rejected_at') }}:</strong> {{ $paymentDetail->rejected_at }}
                                </p>
                                <p><strong>{{ __('attributes.rejected_by') }}:</strong>
                                    {{ $paymentDetail->rejectedBy?->name }}
                                </p>

                                @can('payment_detail.status')
                                    <x-custom.status :model="$paymentDetail" routeName="admin.payment_details.status" />
                                @endcan
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
