@extends('admin.master')
@section('title')
    {{ __('attributes.course_offers') }}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.course_offers') }}" />


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            @can('course_offer.create')
                                <div class="card-header" style="display: flex;justify-content: end">
                                    <a href="{{ route('admin.course_offers.create') }}" class="btn btn-primary"
                                        style="color: white; text-decoration: none;">
                                        {{ __('buttons.create') }} {{ __('attributes.course_offer') }}
                                    </a>
                                </div>
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.course_title') }}</th>
                                            <th>{{ __('attributes.price') }}</th>
                                            <th>{{ __('attributes.start_date') }}</th>
                                            <th>{{ __('attributes.end_date') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($courseOffers as $courseOffer)
                                            @php
                                                $isValied = $courseOffer->end_date > now();

                                            @endphp
                                            <tr class="{{ $isValied ? 'table-success' : 'table-danger' }}">
                                                <td>{{ $courseOffer->id }}</td>
                                                <td>{{ $courseOffer->course->title }}</td>
                                                <td>{{ $courseOffer->price }}</td>
                                                <td>{{ $courseOffer->start_date }}</td>
                                                <td>{{ $courseOffer->end_date }}</td>
                                                <td>
                                                    @can('course_offer.edit')
                                                        <x-custom.edit-button route="admin.course_offers.edit"
                                                            id="{{ $courseOffer->id }}" />
                                                    @endcan
                                                    @can('course_offer.delete')
                                                        <x-custom.delete-button route="admin.course_offers.destroy"
                                                            id="{{ $courseOffer->id }}" />
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.course_title') }}</th>
                                            <th>{{ __('attributes.price') }}</th>
                                            <th>{{ __('attributes.start_date') }}</th>
                                            <th>{{ __('attributes.end_date') }}</th>
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
