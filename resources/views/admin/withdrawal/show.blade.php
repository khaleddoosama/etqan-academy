@extends('admin.master')
@section('title')
    {{ __('attributes.withdrawal_request') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.withdrawal_request') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body">

                                <p><strong>{{ __('attributes.user_name') }}:</strong> {{ $withdrawalRequest->user->name }}
                                </p>
                                <p><strong>{{ __('attributes.wallet_phone') }}:</strong>
                                    {{ $withdrawalRequest->wallet_phone }}</p>
                                <p><strong>{{ __('attributes.amount') }}:</strong> {{ $withdrawalRequest->points }}</p>
                                <p><strong>{{ __('attributes.status') }}:</strong> <span
                                        class="badge badge-{{ $withdrawalRequest->status_color }}">{{ $withdrawalRequest->status_text }}</span>
                                </p>

                                <p><strong>{{ __('attributes.created_at') }}:</strong>
                                    {{ $withdrawalRequest->created_at }}</p>

                                @can('withdrawal.status')
                                    <div class="btn-group">
                                        @php
                                            $buttons = [
                                                0 => [
                                                    [
                                                        'status' => 1,
                                                        'class' => 'btn-success',
                                                        'icon' => 'fas fa-check',
                                                        'title' => __('main.approve'),
                                                    ],
                                                    [
                                                        'status' => 2,
                                                        'class' => 'btn-danger',
                                                        'icon' => 'fas fa-times',
                                                        'title' => __('main.reject'),
                                                    ],
                                                ],
                                                1 => [
                                                    [
                                                        'status' => 2,
                                                        'class' => 'btn-danger',
                                                        'icon' => 'fas fa-times',
                                                        'title' => __('main.reject'),
                                                    ],
                                                ],
                                                2 => [
                                                    [
                                                        'status' => 1,
                                                        'class' => 'btn-success',
                                                        'icon' => 'fas fa-check',
                                                        'title' => __('main.approve'),
                                                    ],
                                                ],
                                            ];
                                        @endphp

                                        @foreach ($buttons[$withdrawalRequest->status] as $button)
                                            <form
                                                action="{{ route('admin.withdrawal_requests.status', $withdrawalRequest->id) }}"
                                                method="POST" class="mx-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="{{ $button['status'] }}">
                                                <button type="submit" class="btn {{ $button['class'] }}"
                                                    title="{{ $button['title'] }}">
                                                    <i class="{{ $button['icon'] }}"></i>
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
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
