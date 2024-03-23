@extends('admin.master')
@section('title')
    {{ __('main.edit_permission') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('main.edit_permission') }}" />

        <!-- Main content -->
        <section class=" content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('main.edit') }} <small>{{ __('main.permission') }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="quickForm" action="{{ route('admin.permission.update', $permission->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="card-body row">


                                    <div class="form-group col-md-6">
                                        <label for="exampleInputname1">{{ __('attributes.name') }}:</label>
                                        <input type="text" name="name" class="form-control" id="exampleInputname1"
                                            required autofocus autocomplete="name" :value="old('name')"
                                            value="{{ $permission->name }}">
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label for="exampleInputmodule1">{{ __('attributes.module') }}:</label>
                                        <input type="text" name="module" class="form-control" id="exampleInputmodule1"
                                            required autofocus autocomplete="module" :value="old('module')"
                                            value="{{ $permission->module }}">
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

            $('#quickForm').validate({
                rules: {
                    name: {
                        required: true,
                    },
                    module: {
                        required: true,
                    },
                },
                messages: {
                    name: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.name')]) }}",
                    },
                    module: {
                        required: "{{ __('validation.required', ['attribute' => __('main.Module')]) }}",
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
