@extends('admin.master')
@section('title')
    {{ __('buttons.edit') }} {{ __('attributes.course_installment') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.edit') }} {{ __('attributes.course_installment') }}" />


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('buttons.edit') }}
                                    <small>{{ __('attributes.course_installment') }}</small>
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="quickForm"
                                action="{{ route('admin.course_installments.update', $courseInstallment->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-body row">
                                    <x-custom.form-group class="col-md-6" type="select" name="course_id" :options="$courses"
                                        selected="{{ $courseInstallment->course_id }}" />
                                    <x-custom.form-group class="col-md-6" type="number" name="number_of_installments"
                                        value="{{ $courseInstallment->number_of_installments }}" />
                                    <x-custom.form-group class="col-md-6" type="number" name="installment_duration"
                                        placeholder="{{ __('main.duration_in_days') }}"
                                        value="{{ $courseInstallment->installment_duration }}" />

                                    <div id="installment-values-container" class="col-md-12 mt-3 row">
                                        @foreach ($courseInstallment->installment_amounts as $amount)
                                            <div class="form-group col-md-6">
                                                <label for="installment_amounts">{{ __('attributes.installment_amount') }}
                                                    {{ $loop->iteration }}</label>
                                                <input type="number" class="form-control" id="installment_amounts"
                                                    name="installment_amounts[]" value="{{ $amount }}">
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class='row form-group col-md-12'>
                                        <x-input-label for="summernote"
                                            class='col-sm-12 col-form-label'>{{ __('attributes.description') }}</x-input-label>

                                        <div class='col-sm-12'>
                                            <textarea name="description" id="summernote" class="form-control summernote" rows="1">{{ old('description') ?? ($courseInstallment->description ?? '') }}</textarea>
                                        </div>
                                    </div>



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
            $('#quickForm').validate({
                rules: {
                    course_id: {
                        required: true,
                    },
                    number_of_installments: {
                        required: true,
                    },
                    installment_value: {
                        required: true,
                    },
                    installment_duration: {
                        required: true,
                    },
                },
                messages: {
                    course_id: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.course')]) }}",
                    },
                    number_of_installments: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.number_of_installments')]) }}",
                    },
                    installment_value: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.installment_value')]) }}",
                    },
                    installment_duration: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.installment_duration')]) }}",
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
        $(document).ready(function() {
            // Reference to the container for dynamic inputs
            const container = $('#installment-values-container');

            // Event listener for number_of_installments input
            $('#input-number_of_installments').on('change', function() {
                const numberOfInstallments = $(this).val();

                // Clear previous inputs
                container.empty();

                // Generate new inputs based on the entered value
                if (numberOfInstallments > 0) {
                    for (let i = 1; i <= numberOfInstallments; i++) {
                        container.append(`
                    <div class="form-group col-md-6">
                        <label for="installment_amount_${i}">{{ __('attributes.installment_amount') }} #${i}</label>
                        <input type="number" required min="0" name="installment_amounts[]" id="installment_amount_${i}" class="form-control" placeholder="{{ __('buttons.enter') }} {{ __('attributes.installment_amount') }}">
                    </div>
                `);
                    }
                }
            });

            container.on('change', 'input[name^="installment_amounts"]', function() {
                const firstValue = $(this).val();

                // Update all inputs with the first value if empty
                container.find('input[name^="installment_amounts"]').each(function() {
                    if (!$(this).val()) {
                        $(this).val(firstValue);
                    }
                });
            });
        });
    </script>
@endsection
