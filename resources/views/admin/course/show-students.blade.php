@extends('admin.master')
@section('title')
    {{ $title }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ $title }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header" style="display: flex;justify-content: end;align-items: center">
                                <h3 class="card-title">Course: {{ $course->title }}</h3>
                                {{-- button modal --}}
                                @can('user_course.create')
                                    <button class="btn btn-primary ml-auto" data-toggle="modal" data-target="#addStudentModal"
                                        style="color: white; text-decoration: none;">
                                        {{ __('buttons.add_student') }}
                                    </button>
                                @endcan
                                {{-- start modal --}}
                                <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog"
                                    aria-labelledby="addStudentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.courses.users.store', $course) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addStudentModalLabel">
                                                        {{ __('buttons.add_student') }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label for="student_id">{{ __('attributes.student_title') }}</label>
                                                        <select name="user_id" id="user_id" class="form-control select2">
                                                            <option value="">{{ __('buttons.select') }}
                                                                {{ __('attributes.student') }}</option>
                                                            @foreach ($students as $student)
                                                                <option value="{{ $student->id }}">{{ $student->name }} -
                                                                    {{ $student->email }}
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

                            </div>
                            {{-- @endcan --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.student_name') }}</th>
                                            <th>{{ __('attributes.student_email') }}</th>
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
                                        @foreach ($course_students as $SC)
                                            <tr>
                                                <td>{{ $SC->id }}</td>
                                                <td>{{ $SC->student->first_name }} {{ $SC->student->last_name }}</td>
                                                <td>{{ $SC->student->email }}</td>
                                                <td>{{ $SC->completed }}</td>
                                                <td>{{ $SC->rating }}</td>
                                                <td>{{ $SC->review }}</td>
                                                <td>{{ $SC->progress }}%</td>

                                                <td>
                                                    <span class="badge badge-{{ $SC->status_color }}">
                                                        {{ $SC->status_text }}
                                                    </span>
                                                </td>

                                                <td>{{ $SC->created_at }}</td>

                                                <td>
                                                    @can('user_course.status')
                                                        <form
                                                            action="{{ route('admin.users.courses.change_status', [$SC->student, $course]) }}"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('PUT')
                                                            @if ($SC->status == 0)
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
                                            <th>{{ __('attributes.student_name') }}</th>
                                            <th>{{ __('attributes.student_email') }}</th>
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
            $('#addStudentModal').on('shown.bs.modal', function() {
                $('#user_id').select2({
                    dropdownParent: $('#addStudentModal')
                });
            });

        });
    </script>
@endsection
