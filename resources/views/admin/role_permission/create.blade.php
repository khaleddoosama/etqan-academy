@extends('admin.master')
@section('title')
    {{ __('main.create_role_permissions') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.create_role_permissions') }}" />

        <!-- Main content -->
        <section class=" content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('main.create') }}
                                    <small>{{ __('main.role_permissions') }}</small>
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="quickForm" action="{{ route('admin.role_permission.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body row">
                                    <div class="form-group col-md-12">
                                        <label for="exampleInputname1">{{ __('main.role_name') }}:</label>
                                        {{-- select role --}}
                                        <select class="form-control select2" style="width: 100%;" name="role_id">
                                            <option value="">{{ __('main.select_role') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- permission all check box --}}
                                    <div class="clearfix col-md-12 form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="allPermissions"
                                                value="option1">
                                            <label for="allPermissions" class="custom-control-label">
                                                {{ __('main.all_permissions') }}
                                            </label>
                                        </div>
                                    </div>
                                    <hr class="col-md-12">
                                    {{-- check boxex --}}
                                    @foreach ($permission_modules as $perm_mod)
                                        <div class="col-md-12">
                                            <h4>{{ $perm_mod->module }}</h4>
                                        </div>
                                        @foreach ($perm_mod->permissions as $permission)
                                            <div class="col-md-3 form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input" type="checkbox"
                                                        id="permission{{ $permission->id }}" name="permission_id[]"
                                                        value="{{ $permission->id }}">
                                                    <label for="permission{{ $permission->id }}"
                                                        class="custom-control-label">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach

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
                    role_id: {
                        required: true,
                    },
                    'permission_id[]': {
                        required: true,
                        minlength: 1
                    },

                },
                messages: {
                    role_id: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.role')]) }}",
                    },
                    'permission_id[]': {
                        required: "{{ __('validation.required', ['attribute' => __('main.Permission')]) }}",
                        minlength: "{{ __('validation.min.array', ['attribute' => __('main.Permission'), 'min' => 1]) }}"
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

    {{-- script for permission all --}}
    <script>
        $(document).ready(function() {
            $('#allPermissions').click(function() {
                if ($(this).is(':checked')) {
                    // check all
                    $('input[type=checkbox]').prop('checked', true);
                } else {
                    // uncheck all
                    $('input[type=checkbox]').prop('checked', false);
                }
            });
        });
    </script>
@endsection
