@extends('admin.master')
@section('title')
    {{ __('attributes.request_courses') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.request_courses') }}" />

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
                                            <th>{{ __('attributes.student_name') }}</th>
                                            <th>{{ __('attributes.student_email') }}</th>
                                            <th>{{ __('attributes.student_phone') }}</th>
                                            <th>{{ __('attributes.course') }}</th>
                                            <th>{{ __('attributes.message') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requestCourses as $request_course)
                                            <tr>
                                                <td>{{ $request_course->id }}</td>
                                                <td>{{ $request_course->student->name }}</td>
                                                <td>
                                                    <a
                                                        href="mailto:{{ $request_course->student->email }}">{{ $request_course->student->email }}</a>
                                                </td>
                                                <td><a href="https://wa.me/{{ $request_course->phone }}" target="_blank">
                                                        {{ $request_course->phone }}
                                                    </a></td>
                                                <td>
                                                    <a
                                                        href="{{ route('admin.courses.students.index', $request_course->course) }}">{{ $request_course->course->title }}</a>
                                                </td>
                                                <td>{!! Str::limit($request_course->message, 50) !!}
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $request_course->status_color }}">{{ $request_course->status_text }}</span>
                                                </td>
                                                <td title="{{ $request_course->created_at }}">
                                                    {{ $request_course->created_at->diffForHumans() }}</td>
                                                <td>
                                                    @can('request_course.show')
                                                        <a href="{{ route('admin.request_courses.show', $request_course) }}"
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
                                            <th>{{ __('attributes.student_name') }}</th>
                                            <th>{{ __('attributes.student_email') }}</th>
                                            <th>{{ __('attributes.student_phone') }}</th>
                                            <th>{{ __('attributes.course') }}</th>
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
