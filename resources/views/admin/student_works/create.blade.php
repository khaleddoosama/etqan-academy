@extends('admin.master')
@section('title')
    {{ __('buttons.create') }} {{ __('attributes.student_work') }}
@endsection
@section('content')
    <div class="content-wrapper">

        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.create') }} {{ __('attributes.student_work') }}" />

        <!-- Main content -->
        <section class=" content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('buttons.create') }}<small>
                                        {{ __('attributes.student_work') }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="form1" action="{{ route('admin.student_works.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body row">
                                    <x-custom.form-group class="col-md-6" type="select" name="student_work_category_id"
                                        :options="$studentWorkCategories" />

                                    <x-custom.form-group class="col-md-6" type="file" name="pathes[]"
                                        :multiple="true" />

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <x-primary-button
                                        class="btn btn-primary"><b>{{ __('buttons.add') }}</b></x-primary-button>
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

            $('#form1').validate({
                rules: {
                    student_work_category_id: {
                        required: true,
                    },
                    'pathes[]': {
                        required: true,
                    },
                },
                messages: {
                    student_work_category_id: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.student_work_category')]) }}"
                    },
                    'pathes[]': {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.files')]) }}",
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
