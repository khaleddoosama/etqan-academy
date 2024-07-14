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
                            {{-- @can('user.create') --}}
                            <div class="">
                            </div>
                            <div class="card-header" style="display: flex;justify-content: end;align-items: center">
                                <h3 class="card-title">Student: {{ $user->first_name }} {{ $user->last_name }}</h3>
                                {{-- button modal --}}
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
                                                        <select name="course_id" id="course_id" class="form-control">
                                                            @foreach ($courses as $course)
                                                                <option value="{{ $course->id }}">{{ $course->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">{{ __('buttons.close') }}</button>
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
                                                <td>{{ $user_course->pivot->id }}</td>
                                                <td>{{ $user_course->title }}</td>
                                                <td>{{ $user_course->pivot->completed }}</td>
                                                <td>{{ $user_course->pivot->rating }}</td>
                                                <td>{{ $user_course->pivot->review }}</td>
                                                <td>{{ $user_course->pivot->progress }}%</td>

                                                <td>
                                                    {!! $user_course->pivot->status == 0
                                                        ? '<span class="badge badge-danger">غير مفعل</span>'
                                                        : '<span class="badge badge-success">مفعل</span>' !!}
                                                </td>

                                                <td>{{ $user_course->pivot->created_at }}</td>


                                                <td>



                                                    {{-- @can('user.edit') --}}

                                                    {{-- @endcan --}}


                                                    <form
                                                        action="{{ route('admin.users.courses.change_status', [$user, $user_course]) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('PUT')
                                                        @if ($user_course->pivot->status == 0)
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
