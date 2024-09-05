@extends('admin.master')
@section('title')
    {{ __('buttons.create_course') }}
@endsection
@section('content')
    <div class="content-wrapper">

        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.create_course') }}" />

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
                                        {{ __('attributes.course') }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="form1" action="{{ route('admin.courses.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="card-body row">
                                    <div class="col-md-12">
                                        <img src="#" alt="" class="img-thumbnail d-none"
                                            id="course-thumbnail" style="max-height: 70vh">
                                    </div>

                                    <x-custom.form-group class="col-md-6" type="text" name="title" />

                                    <x-custom.form-group class="col-md-6" type="select" name="category_id"
                                        :options="$categories" />

                                    <div class='row form-group col-md-12'>
                                        <x-input-label for="summernote"
                                            class='col-sm-12 col-form-label'>{{ __('attributes.description') }}</x-input-label>

                                        <div class='col-sm-12'>
                                            <textarea name="description" id="summernote" class="form-control summernote" rows="1">{{ old('description') ?? ($value ?? '') }}</textarea>
                                        </div>
                                    </div>


                                    <x-custom.form-group class="col-md-6" type="number" name="price" />
                                    <x-custom.form-group class="col-md-6" type="number" name="discount_price" />

                                    <x-custom.form-group class="col-md-6" type="number" name="number_of_levels" value=1 />

                                    <x-custom.form-group class="col-md-6" type="select" name="programs[]" :options="$programs"
                                        multiple="true" />

                                    <x-custom.form-group class="col-md-6" type="select" name="instructor_id"
                                        :options="$instructors" />

                                    <x-custom.form-group class="col-md-6" type="file" name="thumbnail" />

                                    {{-- sections --}}
                                    <div class="col-md-12">
                                        <h4>{{ __('attributes.sections') }}</h4>
                                        <div class="row" id="sections">
                                            <div class="col-md-12">
                                                <div class="align-items-end row position-relative">
                                                    <x-custom.form-group class="col-md-6" type="text"
                                                        name="sections[0][title]" />

                                                    <x-custom.form-group class="col-md-6" type="text"
                                                        name="sections[0][description]" />

                                                    <!-- plus button -->
                                                    <div class="mb-3 position-absolute end-0" style="margin-right: -5px">
                                                        <button type="button" class="btn btn-primary "
                                                            onclick="addSection()">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- end sections --}}

                                    {{-- @php
                                        $numberOfInputs = 3;
                                        $inputTypes = ['text', 'email', 'number'];
                                        $inputNames = ['Name', 'Email', 'Age'];
                                    @endphp

                                    <x-custom.dynamic-section :inputs="$numberOfInputs" :types="$inputTypes" :names="$inputNames" /> --}}

                                </div>

                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <x-custom.form-submit text="{{ __('buttons.add') }}" class="btn-primary" />
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
                    title: {
                        required: true,
                    },
                    description: {
                        required: true,
                    },
                    category_id: {
                        required: true,
                    },
                    price: {
                        required: true,
                        number: true
                    },
                    discount_price: {
                        required: true,
                        number: true,
                        lessThan: '#input-price'
                    },
                    number_of_levels: {
                        required: true,
                        number: true
                    },
                    instructor_id: {
                        required: true,
                    },
                },
                messages: {
                    title: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.title')]) }}"
                    },
                    description: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.description')]) }}"
                    },
                    category_id: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.category')]) }}"
                    },
                    price: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.price')]) }}",
                        number: "{{ __('validation.number', ['attribute' => __('attributes.price')]) }}"
                    },
                    discount_price: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.discount_price')]) }}",
                        number: "{{ __('validation.number', ['attribute' => __('attributes.discount_price')]) }}",
                        lessThan: "{{ __('validation.lt.numeric', ['attribute' => __('attributes.discount_price'), 'value' => __('attributes.price')]) }}"
                    },
                    number_of_levels: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.number_of_levels')]) }}",
                        number: "{{ __('validation.number', ['attribute' => __('attributes.number_of_levels')]) }}"
                    },
                    instructor_id: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.instructor')]) }}"
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
        // when user selects a file #exampleInputFile must be show in img with id = #profilePicture
        $(document).ready(function() {
            $('#input-thumbnail').on('change', function(event) {
                const input = event.target;
                const img = $('#course-thumbnail');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        img.attr('src', e.target.result);
                        // remove class d-none
                        img.removeClass('d-none');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
        });
    </script>

    <script>
        function addSection() {
            // get count from last sections[${count}][name]
            let count = parseInt($('#sections').children().last().children().first().children().first().children().last()
                .children().first().attr('name').split('[')[1].split(']')[0]) + 1;
            console.log(count);

            // add section
            $('#sections').append(`
            <div class="col-md-12">
                <div class="row align-items-end position-relative">
                   <x-custom.form-group class="col-md-6" type="text"
                        name="sections[${count}][title]" />
                    <x-custom.form-group class="col-md-6" type="text"
                        name="sections[${count}][description]" />
                    {{-- subtract button --}}
                    <div class="mb-3 position-absolute end-0"
                            style="margin-right: -5px">
                        <button type="button" class=" btn btn-danger" style="padding: 6px 14px;"
                            onclick="removeSection(this)">-</button>
                    </div>
                </div>
            </div>
        `);
        }

        function removeSection(e) {
            e.closest('.col-md-12').remove();
        }
    </script>

    {{-- <script>
        function addSection() {
            // add this <x-custom.dynamic-section component
            $('#sections').append(
                `<x-custom.dynamic-section :inputs="3" :types="['text', 'email', 'number']" :names="['Name', 'Email', 'Age']" />`
            );
        }
    </script> --}}
@endsection
