@extends('admin.master')
@section('title')
    {{ __('attributes.lectures') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.lectures') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            {{-- @can('lecture.create')
                                <x-custom.create-button route="admin.lectures.create"
                                    title="{{ __('buttons.create_lecture') }}" />
                            @endcan --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.slug') }}</th>
                                            <th>{{ __('attributes.title') }}</th>
                                            <th>{{ __('attributes.section') }}</th>
                                            <th>{{ __('attributes.course') }}</th>
                                            <th>{{ __('attributes.processed') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lectures as $lecture)
                                            <tr>
                                                <td>{{ $lecture->id }}</td>
                                                <td>{{ $lecture->slug }}</td>
                                                <td><a
                                                        href="{{ route('admin.lectures.edit', $lecture->id) }}">{{ $lecture->title }}</a>
                                                </td>
                                                <td><a
                                                        href="{{ route('admin.sections.show', $lecture->section_id) }}">{{ $lecture->section->title }}</a>
                                                </td>
                                                <td>{{ $lecture->course->title }}</td>

                                                <td>
                                                    @if ($lecture->processed == 0)
                                                        <span class="badge badge-warning">{{ __('status.pending') }}</span>
                                                    @elseif ($lecture->processed == 1)
                                                        <span class="badge badge-success">{{ __('status.success') }}</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ __('status.failed') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $lecture->created_at->diffForHumans() }}
                                                </td>
                                                <td>

                                                    {{-- @can('lecture.delete') --}}
                                                        <x-custom.delete-button route="admin.lectures.destroy"
                                                            id="{{ $lecture->id }}" />
                                                    {{-- @endcan --}}

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.slug') }}</th>
                                            <th>{{ __('attributes.title') }}</th>
                                            <th>{{ __('attributes.section') }}</th>
                                            <th>{{ __('attributes.course') }}</th>
                                            <th>{{ __('attributes.processed') }}</th>
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
