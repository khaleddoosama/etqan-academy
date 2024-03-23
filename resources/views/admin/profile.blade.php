@extends('admin.master')
@section('title')
    {{ __('main.profile') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.profile') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">

                        <!-- Profile Image -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <x-custom.profile-picture :user="$user" size="100" id="profilePicture" />
                                </div>

                                <h3 class="text-center profile-username">{{ $user->name }}</h3>

                                <p class="text-center text-muted">{{ $user->role }}</p>

                                <form action="{{ route('admin.change.password') }}" method="POST" id="Form2">
                                    @method('PUT')
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $user->id }}">

                                    <x-custom.form-group name="old_password" type="password" />

                                    <x-custom.form-group name="new_password" type="password" />

                                    <x-custom.form-group name="new_password_confirmation" type="password" />

                                    <x-custom.form-submit text="{{ __('buttons.change_password') }}"
                                        class="btn-primary btn-block" />

                                </form>

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->


                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="card">

                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="settings">
                                        <form action="{{ route('admin.profile.update', $user->id) }}"
                                            class="form-horizontal" id="Form1" method="POST"
                                            enctype="multipart/form-data">
                                            @method('PUT')
                                            @csrf

                                            <x-custom.form-group name="name" value="{{ $user->name }}" type="text"
                                                COLINPUT="col-sm-10" COLLABEL="col-sm-2" />

                                            <x-custom.form-group name="email" value="{{ $user->email }}" type="email"
                                                COLINPUT="col-sm-10" COLLABEL="col-sm-2" />

                                            <x-custom.form-group name="phone" value="{{ $user->phone }}" type="text"
                                                COLINPUT="col-sm-10" COLLABEL="col-sm-2" />

                                            <x-custom.form-group name="picture" type="file" COLINPUT="col-sm-10"
                                                COLLABEL="col-sm-2" />

                                            <x-custom.form-submit text="{{ __('buttons.update') }}" COLOFFSET="offset-sm-2"
                                                class="btn-primary" />
                                        </form>
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
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
            $('#Form1').validate({
                rules: {
                    name: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true,
                    },
                    phone: {
                        required: true,
                    },
                    picture: {
                        // required: true,
                        // must be an image (can be different formats)
                        accept: "image/*"
                    },

                },
                messages: {
                    name: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.name')]) }}"
                    },
                    email: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.email')]) }}",
                        email: "{{ __('validation.email', ['attribute' => __('attributes.email')]) }}"
                    },
                    phone: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.phone')]) }}"
                    },
                    picture: {
                        accept: "{{ __('validation.image', ['attribute' => __('attributes.picture')]) }}"
                    },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
            $('#Form2').validate({
                rules: {
                    old_password: {
                        required: true,
                    },
                    new_password: {
                        required: true,
                        minlength: 5
                    },
                    new_password_confirmation: {
                        required: true,
                        minlength: 5,
                        equalTo: "#input-new_password"
                    },


                },
                messages: {
                    old_password: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.old_password')]) }}"
                    },
                    new_password: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.new_password')]) }}",
                        minlength: "{{ __('validation.min.string', ['attribute' => __('attributes.new_password'), 'min' => 5]) }}",
                    },
                    new_password_confirmation: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.new_password_confirmation')]) }}",
                        minlength: "{{ __('validation.min.string', ['attribute' => __('attributes.new_password_confirmation'), 'min' => 5]) }}",
                        equalTo: "{{ __('validation.same', ['attribute' => __('attributes.new_password_confirmation'), 'other' => __('attributes.new_password')]) }}",
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
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

    <script>
        // when user selects a file #exampleInputFile must be show in img with id = #profilePicture
        $(document).ready(function() {
            $('#input-picture').on('change', function(event) {
                const input = event.target;
                const img = $('#profilePicture');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        img.attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
        });
    </script>
@endsection
