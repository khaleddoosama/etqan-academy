@extends('admin.master')
@section('title')
    {{ __('attributes.inquiry') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.inquiry') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body">

                                <p><strong>{{ __('attributes.name') }}:</strong> {{ $inquiry->name }}</p>
                                <p><strong>{{ __('attributes.email') }}:</strong><a
                                        href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></p>
                                <p><strong>{{ __('attributes.phone') }}:</strong> <a
                                        href="https://wa.me/{{ $inquiry->phone }}" target="_blank">
                                        {{ $inquiry->phone }}
                                    </a></p>
                                <p><strong>{{ __('attributes.message') }}:</strong> {{ $inquiry->message }}</p>
                                <p><strong>{{ __('attributes.status') }}:</strong> {{ $inquiry->status }}</p>

                                <p><strong>{{ __('attributes.created_at') }}:</strong> {{ $inquiry->created_at }}</p>

                                @can('inquiry.reply')
                                    @if ($inquiry->status == 'pending')
                                        <form action="{{ route('admin.inquiries.reply', $inquiry->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            {{-- button --}}
                                            <button type="submit" class="btn btn-success" title="{{ __('main.reply') }}">
                                                <i class="fas fa-reply"></i>
                                            </button>
                                        </form>
                                    @endif
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
