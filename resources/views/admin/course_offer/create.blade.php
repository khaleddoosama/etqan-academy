@extends('admin.master')
@section('title')
    {{ __('buttons.create') }} {{ __('attributes.course_offer') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.create') }} {{ __('attributes.course_offer') }}" />


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('buttons.create') }}
                                    <small>{{ __('attributes.course_offer') }}</small>
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="quickForm" action="{{ route('admin.course_offers.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body row">


                                    <x-custom.form-group class="col-md-6" type="select" name="course_id"
                                        :options="$courses" />
                                    <x-custom.form-group class="col-md-6" type="number" name="price" />
                                    <x-custom.form-group class="col-md-6" type="date" name="start_date" />
                                    <x-custom.form-group class="col-md-6" type="date" name="end_date" />

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <x-primary-button
                                        class="btn btn-primary"><b>{{ __('buttons.submit') }}</b></x-primary-button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('scripts')
    <!-- Page specific script -->
    <script>
        $(function() {

            $('#quickForm').validate({
                rules: {
                    course_id: {
                        required: true,
                    },
                    price: {
                        required: true,
                        number: true,
                    },
                    start_date: {
                        required: true,
                        date: true,
                    },

                    end_date: {
                        required: true,
                        date: true,
                    },
                },
                messages: {
                    course_id: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.course')]) }}",
                    },
                    price: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.price')]) }}",
                    },
                    start_date: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.start_date')]) }}",
                    },
                    end_date: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.end_date')]) }}",
                    },

                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    error.css('padding', '0 7.5px');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection