@extends('admin.master')
@section('title')
    {{ __('attributes.courses') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.courses') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            {{-- @can('course.create') --}}
                            <x-custom.create-button route="admin.courses.create" title="{{ __('buttons.create_course') }}" />
                            {{-- @endcan --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.title') }}</th>
                                            <th style="width: 20%">{{ __('attributes.description') }}</th>
                                            <th>{{ __('attributes.category') }}</th>
                                            <th>{{ __('attributes.sections') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($courses as $course)
                                            <tr>
                                                <td>{{ $course->id }}</td>
                                                <td>{{ $course->title }}</td>
                                                <td>{{ $course->description }}</td>
                                                <td>{{ $course->category->name }}</td>
                                                <td>
                                                    @foreach ($course->sections as $section)
                                                        <a href="{{ route('admin.sections.show', $section) }}" title="show {{ $section->title }}">
                                                            {{ $section->title }} <i class="fas fa-eye"></i> 
                                                            <span class="sr-only">(current)</span>
                                                            <br>
                                                        </a>
                                                        
                                                    @endforeach

                                                </td>
                                                <td>
                                                    {{-- @can('course.edit') --}}
                                                    <x-custom.edit-button route="admin.courses.edit"
                                                        id="{{ $course->id }}" />
                                                    {{-- @endcan --}}

                                                    {{-- @can('course.delete') --}}
                                                    {{-- <x-custom.delete-button route="admin.courses.destroy"
                                                        id="{{ $course->id }}" /> --}}

                                                    {{-- @endcan --}}

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.title') }}</th>
                                            <th>{{ __('attributes.description') }}</th>
                                            <th>{{ __('attributes.category') }}</th>
                                            <th>{{ __('attributes.sections') }}</th>
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
