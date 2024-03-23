@extends('admin.master')
@section('title')
    {{ __('main.edit_admin') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.edit_admin') }}" />

        <!-- Main content -->
        <section class=" content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('main.Edit') }} <small>{{ __('main.Admin') }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="quickForm" action="{{ route('admin.all_admin.update', $admin->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="card-body row">


                                    <div class="form-group col-md-6">
                                        <label for="exampleInputname1">{{ __('attributes.name') }}:</label>
                                        <input type="text" name="name" class="form-control" id="exampleInputname1"
                                            placeholder="{{ __('main.Enter name') }}" required autofocus autocomplete="name"
                                            :value="old('name')" value="{{ $admin->name }}">
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label for="exampleInputemail1">{{ __('attributes.email') }}:</label>
                                        <input type="text" name="email" class="form-control" id="exampleInputemail1"
                                            placeholder="{{ __('main.Enter Email') }}" required autofocus
                                            autocomplete="email" :value="old('email')" value="{{ $admin->email }}">
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label for="exampleInputphone1">{{ __('attributes.phone') }}:</label>
                                        <input type="text" name="phone" class="form-control" id="exampleInputphone1"
                                            placeholder="{{ __('main.Enter Phone') }}" required autofocus
                                            autocomplete="phone" :value="old('phone')" value="{{ $admin->phone }}">
                                    </div>



                                    <div class="form-group col-md-6">
                                        <label>{{ __('attributes.role') }}:</label>
                                        <select class="form-control select2" style="width: 100%;" name="role">
                                            <option selected="selected" disabled>{{ __('main.select_role') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ $admin->hasRole($role->name) ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>{{ __('attributes.category') }}:</label>
                                        <select class="form-control select2" style="width: 100%;" name="category_id">
                                            <option selected="selected" disabled>{{ __('main.choose') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $admin->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}</option>
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
                    role: {
                        required: true,
                    },

                },
                messages: {
                    name: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.name')]) }}",
                    },
                    email: {
                        required: "{{ __('validation.required', ['attribute' => __('main.Email')]) }}",
                        email: "{{ __('validation.email', ['attribute' => __('main.Email')]) }}",
                    },
                    phone: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.phone')]) }}",
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
