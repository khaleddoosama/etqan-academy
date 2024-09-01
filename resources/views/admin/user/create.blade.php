@extends('admin.master')
@section('title')
    {{ __('buttons.create_admin') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.create_user') }}" />


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('buttons.create') }} <small>{{ __('main.user') }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="quickForm" action="{{ route('admin.users.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body row">

                                    <x-custom.form-group class="col-md-6" type="text" name="first_name" />
                                    <x-custom.form-group class="col-md-6" type="text" name="last_name" />

                                    <x-custom.form-group class="col-md-6" type="text" name="email" />

                                    <x-custom.form-group class="col-md-6" type="text" name="phone" />


                                    <x-custom.form-group class="col-md-6" type="password" name="password" />

                                    <x-custom.form-group class="col-md-6" type="password" name="password_confirmation" />


                                    <x-custom.form-group class="col-md-6" type="select" name="category_id" :options="$categories" />



                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <x-primary-button
                                        class="btn btn-primary"><b>{{ __('buttons.submit') }}</b></x-primary-button>
                                </div>
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
            // $.validator.setDefaults({
            //     submitHandler: function() {
            //         alert("Form successful submitted!");
            //     }
            // });
            $('#quickForm').validate({
                rules: {
                    first_name: {
                        required: true,
                    },
                    last_name: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true,
                    },
                    phone: {
                        required: true,
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    password_confirmation: {
                        required: true,
                        minlength: 8,
                        equalTo: "#input-password"
                    },
                    role: {
                        required: true,
                    },
                    category_id: {
                        required: true,
                    }

                },
                messages: {
                    first_name: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.first_name')]) }}",
                    },
                    last_name: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.last_name')]) }}",
                    },
                    email: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.email')]) }}",
                        email: "{{ __('validation.email', ['attribute' => __('attributes.email')]) }}",
                    },
                    phone: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.phone')]) }}",
                    },

                    password: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.password')]) }}",
                        minlength: "{{ __('validation.min.string', ['attribute' => __('attributes.password'), 'min' => 6]) }}",
                    },
                    password_confirmation: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.password_confirmation')]) }}",
                        minlength: "{{ __('validation.min.string', ['attribute' => __('attributes.password_confirmation'), 'min' => 6]) }}",
                        equalTo: "{{ __('validation.same', ['attribute' => __('attributes.password_confirmation'), 'other' => __('attributes.password')]) }}"
                    },
                    category_id: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.category_id')]) }}",
                    }

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
