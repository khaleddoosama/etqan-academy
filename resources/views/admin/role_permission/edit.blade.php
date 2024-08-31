@extends('admin.master')
@section('title')
    {{ __('buttons.edit_role_permissions') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.edit_role_permissions') }}" />

        <!-- Main content -->
        <section class=" content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('buttons.edit') }} <small>{{ __('main.role_permissions') }}</small>
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="quickForm" action="{{ route('admin.role_permissions.update', $role->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="card-body row">
                                    <div class="form-group col-md-12">
                                        {{-- <label for="exampleInputname1">{{ __('main.role Name') }}:</label> --}}
                                        <div class="form-group">
                                            <label for="exampleInputname1">{{ __('main.role_name') }}:</label>
                                            <input type="text" name="name" class="form-control" id="exampleInputname1"
                                                value="{{ $role->name }}" disabled>
                                        </div>

                                    </div>
                                    {{-- permission all check box --}}
                                    <div class="clearfix col-md-12 form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="allpermissions"
                                                value="option1">
                                            <label for="allpermissions" class="custom-control-label">
                                                {{ __('main.all_permissions') }}
                                            </label>
                                        </div>
                                    </div>
                                    <hr class="col-md-12">
                                    {{-- check boxex --}}
                                    @foreach ($permission_modules as $module => $permissions)
                                        <div class="col-md-12">
                                            <h4>{{ $module }}</h4>
                                        </div>
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-3 form-group">
                                                <div class="custom-control custom-checkbox">
                                                    {{-- check if role has permission or not --}}

                                                    <input class="custom-control-input" type="checkbox"
                                                        @if ($role->haspermissionTo($permission->name)) checked @endif
                                                        id="permission{{ $permission->id }}" name="permissions[]"
                                                        value="{{ $permission->name }}">
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
                                    <button type="submit" class="btn btn-primary">{{ __('buttons.submit') }}</button>
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
                        required: "{{ __('validation.required', ['attribute' => __('main.role')]) }}",
                    },
                    'permission_id[]': {
                        required: "{{ __('validation.required', ['attribute' => __('main.permission')]) }}",
                        minlength: "{{ __('validation.min.array', ['attribute' => __('main.permission'), 'min' => 1]) }}"
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
            $('#allpermissions').click(function() {
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
