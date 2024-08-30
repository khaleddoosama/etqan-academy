@extends('admin.master')
@section('title')
    {{ __('main.create_admin') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.create_admin') }}" />


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('main.create') }} <small>{{ __('main.admin') }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="quickForm" action="{{ route('admin.all_admin.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body row">


                                    {{-- <div class="form-group col-md-6">
                                        <label for="exampleInputname1">{{ __('attributes.name') }}:</label>
                                        <input type="text" name="name" class="form-control" id="exampleInputname1"
                                            required autofocus autocomplete="name" :value="old('name')">
                                    </div> --}}
                                    <x-custom.form-group class="col-md-6" type="text" name="first_name" />
                                    <x-custom.form-group class="col-md-6" type="text" name="last_name" />

                                    <x-custom.form-group class="col-md-6" type="text" name="email" />

                                    <x-custom.form-group class="col-md-6" type="text" name="phone" />


                                    <x-custom.form-group class="col-md-6" type="password" name="password" />

                                    <x-custom.form-group class="col-md-6" type="password" name="password_confirmation" />


                                    <x-custom.form-group class="col-md-6" type="select" name="role" :options="$roles" />

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
                    // address: {
                    //     required: true,
                    // },
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
                    role: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.role')]) }}",
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
