@extends('admin.master')
@section('title')
    {{ __('attributes.course_installments') }}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.course_installments') }}" />


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            @can('course_installment.create')
                                <div class="card-header" style="display: flex;justify-content: end">
                                    <a href="{{ route('admin.course_installments.create') }}" class="btn btn-primary"
                                        style="color: white; text-decoration: none;">
                                        {{ __('buttons.create') }} {{ __('attributes.course_installment') }}
                                    </a>
                                </div>
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.course_title') }}</th>
                                            <th>{{ __('attributes.number_of_installments') }}</th>
                                            <th>{{ __('attributes.installment_amounts') }}</th>
                                            <th>{{ __('attributes.installment_duration') }}</th>
                                            <th>{{ __('attributes.description') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($courseInstallments as $courseInstallment)
                                            <tr>
                                                <td>{{ $courseInstallment->id }}</td>
                                                <td>{{ $courseInstallment->course->title }}</td>
                                                <td>{{ $courseInstallment->number_of_installments }}</td>
                                                <td>
                                                    @foreach ($courseInstallment->installment_amounts as $amount)
                                                        {{ $amount }},
                                                    @endforeach
                                                </td>
                                                <td>{{ $courseInstallment->installment_duration }}</td>
                                                <td>{{ Str::limit(strip_tags($courseInstallment->description), 100) }}</td>
                                                <td>
                                                    @can('course_installment.edit')
                                                        <x-custom.edit-button route="admin.course_installments.edit"
                                                            id="{{ $courseInstallment->id }}" />
                                                    @endcan
                                                    @can('course_installment.delete')
                                                        <x-custom.delete-button route="admin.course_installments.destroy"
                                                            id="{{ $courseInstallment->id }}" />
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.course_title') }}</th>
                                            <th>{{ __('attributes.number_of_installments') }}</th>
                                            <th>{{ __('attributes.installment_amounts') }}</th>
                                            <th>{{ __('attributes.installment_duration') }}</th>
                                            <th>{{ __('attributes.description') }}</th>
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
