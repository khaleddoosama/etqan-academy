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


                                    <div class="form-group col-md-6">
                                        <label for="exampleInputname1">{{ __('attributes.name') }}:</label>
                                        <input type="text" name="name" class="form-control" id="exampleInputname1"
                                            required autofocus autocomplete="name" :value="old('name')">
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label for="exampleInputemail1">{{ __('attributes.email') }}:</label>
                                        <input type="text" name="email" class="form-control" id="exampleInputemail1"
                                            required autofocus autocomplete="email" :value="old('email')">
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label for="exampleInputphone1">{{ __('attributes.phone') }}:</label>
                                        <input type="text" name="phone" class="form-control" id="exampleInputphone1"
                                            required autofocus autocomplete="phone" :value="old('phone')">
                                    </div>

                                    {{-- <div class="form-group col-md-6">
                                        <label for="exampleInputaddress1">{{ __('buttons.address') }}:</label>
                                        <textarea name="address" class="form-control" id="exampleInputaddress1" placeholder="Enter address" required autofocus
                                            autocomplete="address" :value="old('address')"></textarea>
                                    </div> --}}

                                    <div class="form-group col-md-6">
                                        <label for="exampleInputpassword1">{{ __('attributes.password') }}</label>
                                        <input type="password" name="password" class="form-control"
                                            id="exampleInputpassword1" placeholder="{{ __('attributes.password') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label
                                            for="exampleInputpassword2">{{ __('attributes.password_confirmation') }}</label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            id="exampleInputpassword2"
                                            placeholder="{{ __('attributes.password_confirmation') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>{{ __('attributes.role') }}:</label>
                                        <select class="form-control select2" style="width: 100%;" name="role">
                                            <option selected="selected" disabled>{{ __('main.select_role') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>{{ __('attributes.category') }}:</label>
                                        <select class="form-control select2" style="width: 100%;" name="category_id">
                                            <option selected="selected" disabled>{{ __('main.choose') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">{{ __('main.submit') }}</button>
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
                    // address: {
                    //     required: true,
                    // },
                    password: {
                        required: true,
                        minlength: 6
                    },
                    password_confirmation: {
                        required: true,
                        minlength: 6,
                        equalTo: "#exampleInputpassword1"
                    },
                    role: {
                        required: true,
                    },
                    category_id: {
                        required: true,
                    }

                },
                messages: {
                    name: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.name')]) }}",
                    },
                    email: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.email')]) }}",
                        email: "{{ __('validation.email', ['attribute' => __('attributes.email')]) }}",
                    },
                    phone: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.phone')]) }}",
                    },
                    // address: {
                    //     required: "{{ __('validation.required', ['attribute' => __('attributes.address')]) }}",
                    // },
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
                    category_id: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.category')]) }}",
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
