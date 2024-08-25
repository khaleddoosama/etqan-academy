@extends('admin.master')
@section('title')
    {{ __('buttons.edit_program') }}
@endsection
@section('content')
    <div class="content-wrapper">


        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.edit_program') }}" />

        <!-- Main content -->
        <section class=" content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('buttons.edit') }}<small>
                                        {{ __('attributes.program') }}</small></h3>
                            </div>
                            <div class="card-body">
                                <img src="{{ asset($program->icon) }}" alt="" width="50" id="icon-preview">
                            </div>

                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="form1" action="{{ route('admin.programs.update', $program->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="card-body row">
                                    <x-custom.form-group class="col-md-6" type="text" name="name"
                                        value="{{ $program->name }}" />

                                    <x-custom.form-group class="col-md-6" type="textarea" name="description"
                                        value="{{ $program->description }}" />


                                    <x-custom.form-group class="col-md-6" type="file" name="icon"
                                        value="{{ $program->icon }}" />
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <x-primary-button
                                        class="btn btn-primary"><b>{{ __('buttons.update') }}</b></x-primary-button>
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
                    name: {
                        required: true,
                    },
                },
                messages: {
                    name: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.name')]) }}"
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


    <script>
        $('#input-icon').on('change', function() {
            if (this.files && this.files[0]) { // Check if files exist
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#icon-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                console.error('No file selected.');
            }
        });
    </script>
@endsection
