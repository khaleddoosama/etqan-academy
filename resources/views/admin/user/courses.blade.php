@extends('admin.master')
@section('title')
    {{ $title }}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ $title }}" />


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            <div class="card-header" style="display: flex;justify-content: end;align-items: center">
                                <h3 class="card-title">Student: {{ $user->first_name }} {{ $user->last_name }}</h3>
                                {{-- button modal --}}
                                @can('user_course.create')
                                    <button class="btn btn-primary ml-auto" data-toggle="modal" data-target="#addCourseModal"
                                        style="color: white; text-decoration: none;">
                                        {{ __('buttons.add_course') }}
                                    </button>

                                    {{-- start modal --}}
                                    <div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog"
                                        aria-labelledby="addCourseModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.users.courses.store', $user) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addCourseModalLabel">
                                                            {{ __('buttons.add_course') }}
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <div class="form-group">
                                                            <label for="course_id">{{ __('attributes.course_title') }}</label>
                                                            <select name="course_id" id="course_id"
                                                                class="form-control select2">
                                                                <option value="">{{ __('buttons.select') }}
                                                                    {{ __('attributes.course') }}</option>

                                                                @foreach ($courses as $course)
                                                                    <option value="{{ $course->id }}">{{ $course->title }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <x-custom.close-modal-button />
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ __('buttons.add') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end button modal --}}
                                @endcan


                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.course_title') }}</th>
                                            <th>{{ __('attributes.completed') }}</th>
                                            <th>{{ __('attributes.rating') }}</th>
                                            <th>{{ __('attributes.review') }}</th>
                                            <th>{{ __('attributes.progress') }}</th>
                                            <th>{{ __('attributes.status') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user_courses as $user_course)
                                            <tr>
                                                <td>{{ $user_course->id }}</td>
                                                <td>{{ $user_course->course->title }}</td>
                                                <td>{{ $user_course->completed }}</td>
                                                <td>{{ $user_course->rating }}</td>
                                                <td>{{ $user_course->review }}</td>
                                                <td>{{ $user_course->progress }}%</td>

                                                <td>
                                                    <span class="badge badge-{{ $user_course->status_color }}">
                                                        {{ $user_course->status_text }}
                                                    </span>
                                                </td>

                                                <td>{{ $user_course->created_at }}</td>

                                                <td>
                                                    @can('user_course.status')
                                                    <form
                                                        action="{{ route('admin.users.courses.change_status', [$user, $user_course->course]) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('PUT')
                                                        @if ($user_course->status == 0)
                                                            <input type="hidden" name="status" value="1">
                                                            <button type="submit" class="btn btn-success"
                                                                title="{{ __('buttons.activate') }}"
                                                                style="color: white; text-decoration: none;">
                                                                <i class="fas fa-toggle-on"></i>
                                                            </button>
                                                        @else
                                                            <input type="hidden" name="status" value="0">
                                                            <button type="submit" class="btn btn-danger"
                                                                title="{{ __('buttons.deactivate') }}"
                                                                style="color: white; text-decoration: none;">
                                                                <i class="fas fa-toggle-off"></i>
                                                            </button>
                                                        @endif

                                                    </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.course_title') }}</th>
                                            <th>{{ __('attributes.completed') }}</th>
                                            <th>{{ __('attributes.rating') }}</th>
                                            <th>{{ __('attributes.review') }}</th>
                                            <th>{{ __('attributes.progress') }}</th>
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
        $(document).ready(function() {
            $('#addCourseModal').on('shown.bs.modal', function() {
                $('#course_id').select2({
                    dropdownParent: $('#addCourseModal')
                });
            });

        });
    </script>
@endsection
