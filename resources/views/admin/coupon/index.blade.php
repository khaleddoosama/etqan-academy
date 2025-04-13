@extends('admin.master')
@section('title')
{{ __('attributes.coupons') }}
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ __('attributes.coupons') }}" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @can('coupons.create')
                        <x-custom.create-button route="admin.coupons.create"
                            title="{{ __('buttons.create_coupon') }}" />
                        @endcan
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.code') }}</th>
                                        <th>{{ __('attributes.type') }}</th>
                                        <th>{{ __('attributes.discount') }}</th>
                                        <th>{{ __('attributes.start_at') }}</th>
                                        <th>{{ __('attributes.expires_at') }}</th>
                                        <th>{{ __('attributes.usage_limit') }}</th>
                                        <th>{{ __('attributes.status') }}</th>
                                        <th>{{ __('main.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($coupons as $coupon)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $coupon->code }}</td>
                                        <td>{{ ucfirst($coupon->type) }}</td>
                                        <td>{{ $coupon->discount }}</td>
                                        <td>{{ optional($coupon->start_at)->format('Y-m-d') }}</td>
                                        <td>{{ optional($coupon->expires_at)->format('Y-m-d') }}</td>
                                        <td>{{ $coupon->usage_count }}/{{ $coupon->usage_limit ?? '∞' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $coupon->status ? 'success' : 'danger' }}">
                                                {{ $coupon->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('coupons.status')
                                            @if ($coupon->status == 1)
                                            <form action="{{ route('admin.coupons.status', $coupon->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="0">
                                                <button type="submit" class="btn btn-danger" title="{{ __('buttons.deactivate') }}"
                                                    style="color: white; text-decoration: none;">
                                                    <i class="fas fa-toggle-off"></i>
                                                </button>
                                            </form>
                                            @else
                                            <form action="{{ route('admin.coupons.status', $coupon->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit" class="btn btn-success" title="{{ __('buttons.activate') }}"
                                                    style="color: white; text-decoration: none;">
                                                    <i class="fas fa-toggle-on"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endcan

                                            @can('coupons.edit')
                                            <x-custom.edit-button route="admin.coupons.edit"
                                                id="{{ $coupon->id }}" />
                                            @endcan

                                            @can('coupons.delete')
                                            <x-custom.delete-button route="admin.coupons.destroy"
                                                id="{{ $coupon->id }}" />
                                            @endcan

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.code') }}</th>
                                        <th>{{ __('attributes.type') }}</th>
                                        <th>{{ __('attributes.discount') }}</th>
                                        <th>{{ __('attributes.start_at') }}</th>
                                        <th>{{ __('attributes.expires_at') }}</th>
                                        <th>{{ __('attributes.usage_limit') }}</th>
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
