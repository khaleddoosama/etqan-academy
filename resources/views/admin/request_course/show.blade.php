@extends('admin.master')
@section('title')
    {{ __('attributes.request_course') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.request_course') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body">

                                <p><strong>{{ __('attributes.name') }}:</strong> {{ $requestCourse->student->name }}</p>
                                <p><strong>{{ __('attributes.email') }}:</strong> <a
                                        href="mailto:{{ $requestCourse->student->email }}">{{ $requestCourse->student->email }}</a>
                                </p>
                                <p><strong>{{ __('attributes.phone') }}:</strong> <a
                                        href="https://wa.me/{{ $requestCourse->phone }}" target="_blank">
                                        {{ $requestCourse->phone }}
                                    </a></p>
                                <p><strong>{{ __('attributes.course') }}:</strong> {{ $requestCourse->course->title }}</p>
                                <p><strong>{{ __('attributes.message') }}:</strong> {{ $requestCourse->message }}</p>
                                <p><strong>{{ __('attributes.status') }}:</strong> {{ $requestCourse->status_text }}</p>

                                <p><strong>{{ __('attributes.created_at') }}:</strong> {{ $requestCourse->created_at }}
                                </p>

                                @can('request_course.reply')
                                @if ($requestCourse->status == 0)
                                    <form action="{{ route('admin.request_courses.reply', $requestCourse->id) }}"
                                        method="POST">
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
