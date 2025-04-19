@extends('admin.master')
@section('title')
{{ __('attributes.payment_details') }}
@endsection
@section('styles')
<!-- remove paddind and margin in table -->

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
                        <div class="card-header">
                            <a href="{{ route('admin.payment_details.export') }}" class="btn btn-primary">
                                {{ __('buttons.export_sheets') }}
                            </a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.name') }}</th>
                                        <th>{{ __('attributes.email') }}</th>
                                        <th>{{ __('attributes.phone') }}</th>
                                        <th>{{ __('attributes.course') }}</th>
                                        <th>{{ __('attributes.payment_type') }}</th>
                                        <th>{{ __('attributes.payment_method') }}</th>
                                        <th>{{ __('attributes.transfer_identifier') }}</th>
                                        <th>{{ __('attributes.transfer_image') }}</th>
                                        <th>{{ __('attributes.amount_before_coupon') }}</th>
                                        <th>{{ __('attributes.amount_after_coupon') }}</th>
                                        <th>{{ __('attributes.amount_confirmed') }}</th>
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
                                        <td style="max-width: 150px">
                                            <a
                                                href="mailto:{{ $paymentDetail->user->email ?? '' }}">{{ $paymentDetail->user->email ?? '' }}</a>
                                        </td>
                                        <td><a href="https://wa.me/{{ $paymentDetail->user->phone }}"
                                                target="_blank">
                                                {{ $paymentDetail->user->phone }}
                                            </a></td>
                                        <td>
                                            @foreach ($paymentDetail->paymentItems as $item)
                                            {{ $item->courseInstallment->course->title }},
                                            @endforeach
                                        </td>

                                        <td>{{ $paymentDetail->payment_type }}</td>
                                        <td>{{ $paymentDetail->payment_method }}</td>
                                        <td>{{ $paymentDetail->transfer_identifier ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ Storage::url($paymentDetail->transfer_image) }}"
                                                target="_blank">
                                                <img src="{{ Storage::url($paymentDetail->transfer_image) }}"
                                                    alt="transfer_image" width="50px" height="50px">
                                            </a>
                                        </td>
                                        <td>{{ $paymentDetail->amount_before_coupon }}</td>
                                        <td>{{ $paymentDetail->amount_after_coupon }}</td>
                                        <td>{{ $paymentDetail->amount_confirmed }}</td>

                                        <td>
                                            <span
                                                class="badge bg-{{ $paymentDetail->status->Color() }}">{{ $paymentDetail->status }}</span>
                                        </td>

                                        <td title="{{ $paymentDetail->created_at }}">
                                            {{ $paymentDetail->created_at->diffForHumans() }}
                                        </td>
                                        <td>
                                            @can('payment_detail.show')
                                            <a href="{{ route('admin.payment_details.show', $paymentDetail) }}"
                                                class="btn btn-success my-1" title="{{ __('main.show') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            {{-- @can('payment_detail.status') --}}
                                            @if (auth()->user()->can('payment_detail.status') && $paymentDetail->status != \App\Enums\Status::APPROVED)
                                            <button class="btn btn-primary my-1" data-toggle="modal"
                                                data-target="#updateAmountModal-{{ $paymentDetail->id }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <div class="modal fade"
                                                id="updateAmountModal-{{ $paymentDetail->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="updateAmountModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form
                                                            action="{{ route('admin.payment_details.update', $paymentDetail) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-dark"
                                                                    id="updateAmountModalLabel">
                                                                    {{ __('buttons.update') }}
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">

                                                                <div class="form-group">
                                                                    <label for="amount"
                                                                        class="text-dark">{{ __('attributes.amount') }}</label>
                                                                    <input type="number" name="amount"
                                                                        class="form-control" id="amount"
                                                                        value="{{ $paymentDetail->amount }}"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <x-custom.close-modal-button />
                                                                <button type="submit"
                                                                    class="btn btn-primary">{{ __('buttons.update') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            {{-- @endcan --}}


                                            @can('payment_detail.status')
                                            <x-custom.status :model="$paymentDetail"
                                                routeName="admin.payment_details.status" />
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
                                        <th>{{ __('attributes.payment_type') }}</th>
                                        <th>{{ __('attributes.payment_method') }}</th>
                                        <th>{{ __('attributes.transfer_identifier') }}</th>
                                        <th>{{ __('attributes.transfer_image') }}</th>
                                        <th>{{ __('attributes.amount_before_coupon') }}</th>
                                        <th>{{ __('attributes.amount_after_coupon') }}</th>
                                        <th>{{ __('attributes.amount_confirmed') }}</th>
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
