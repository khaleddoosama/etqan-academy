@extends('admin.master')
@section('title')
{{ __('attributes.student_opinions') }}
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ __('attributes.student_opinions') }}" />

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
                                        <th>{{ __('attributes.student_name') }}</th>
                                        <th>{{ __('attributes.course_name') }}</th>
                                        <th>{{ __('attributes.opinion') }}</th>
                                        <th>{{ __('attributes.status') }}</th>
                                        <th>{{ __('attributes.created_at') }}</th>
                                        <th>{{ __('main.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($studentOpinions as $student_opinion)
                                    <tr>
                                        <td>{{ $student_opinion->id }}</td>
                                        <td><a href="{{ route('admin.users.show', $student_opinion->student) }}">{{ $student_opinion->student->name }}</a></td>
                                        <td><a>{{ $student_opinion->course->title }}</a></td>
                                        <td title="{{ $student_opinion->opinion }}" data-bs-toggle="tooltip">{{ Str::limit($student_opinion->opinion, 100) }}</td>

                                        <td>
                                            <span class="badge badge-{{ $student_opinion->status_color }}">
                                                {{ $student_opinion->status_text }}
                                        </td>
                                        <td>{{ $student_opinion->created_at }}</td>
                                        <td>
                                            @can('student_opinion.status')
                                            
                                            <x-custom.change-status-button :status="$student_opinion->status"
                                                            route="admin.student-opinions.status" :id="$student_opinion->id" />
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.student_name') }}</th>
                                        <th>{{ __('attributes.course_name') }}</th>
                                        <th>{{ __('attributes.opinion') }}</th>
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
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

@endsection
