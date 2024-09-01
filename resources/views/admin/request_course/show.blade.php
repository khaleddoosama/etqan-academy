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
                                <p><strong>{{ __('attributes.status') }}:</strong> <span
                                        class="badge badge-{{ $requestCourse->status_color }}">{{ $requestCourse->status_text }}</span>
                                </p>

                                <p><strong>{{ __('attributes.created_at') }}:</strong> {{ $requestCourse->created_at }}
                                </p>

                                @can('request_course.status')
                                    @if ($requestCourse->status == 0)
                                        <div class="row">
                                            <form action="{{ route('admin.request_courses.status', $requestCourse->id) }}"
                                                method="POST" class="mx-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="1">
                                                {{-- button --}}
                                                <button type="submit" class="btn btn-success"
                                                    title="{{ __('buttons.approve') }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.request_courses.status', $requestCourse->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="2">
                                                {{-- button --}}
                                                <button type="submit" class="btn btn-danger"
                                                    title="{{ __('buttons.reject') }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif ($requestCourse->status == 1)
                                        <form action="{{ route('admin.request_courses.status', $requestCourse->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="2">
                                            {{-- button --}}
                                            <button type="submit" class="btn btn-danger" title="{{ __('buttons.reject') }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @elseif ($requestCourse->status == 2)
                                        <form action="{{ route('admin.request_courses.status', $requestCourse->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="1">
                                            {{-- button --}}
                                            <button type="submit" class="btn btn-success" title="{{ __('buttons.approve') }}">
                                                <i class="fas fa-check"></i>
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
