@extends('admin.master')
@section('title')
    {{ __('buttons.edit_course') }}
@endsection
@section('content')
    <div class="content-wrapper">

        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.edit_course') }}" />

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
                                        {{ __('attributes.course') }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="form1" action="{{ route('admin.courses.update', $course) }}" method="POST"
                                enctype="multipart/form-data">
                                @method('PUT')
                                @csrf

                                <div class="card-body row">
                                    <div class="col-md-12">
                                        <img src="@if ($course->thumbnail) {{ asset($course->thumbnail) }} @else # @endif"
                                            alt=""
                                            class="img-thumbnail @if ($course->thumbnail) {{ asset($course->thumbnail) }} @else d-none @endif"
                                            id="course-thumbnail" style="max-height: 70vh">
                                    </div>

                                    <x-custom.form-group class="col-md-6" type="text" name="title"
                                        value="{{ $course->title }}" />

                                    <x-custom.form-group class="col-md-6" type="textarea" name="description"
                                        value="{{ $course->description }}" />

                                    <x-custom.form-group class="col-md-6" type="select" name="category_id"
                                        :options="$categories" selected="{{ $course->category->id }}" />

                                    <x-custom.form-group class="col-md-6" type="file" name="thumbnail" />

                                    {{-- sections --}}
                                    <div class="col-md-12">
                                        <h4>{{ __('attributes.sections') }}</h4>
                                        <div class="row" id="sections">

                                            @foreach ($course->sections as $key => $section)
                                                <div class="col-md-12">
                                                    <div class="align-items-end row position-relative">

                                                        <x-custom.form-group class="col-md-6" type="text"
                                                            name="sections[{{ $key }}][title]"
                                                            value="{{ $section->title }}" />

                                                        {{-- id hidden --}}
                                                        <input type="hidden" name="sections[{{ $key }}][id]"
                                                            value="{{ $section->id }}" />

                                                        <x-custom.form-group class="col-md-6" type="text"
                                                            name="sections[{{ $key }}][description]"
                                                            value="{{ $section->description }}" />

                                                        {{-- if first iteration --}}
                                                        @if ($loop->first)
                                                            <!-- plus button -->
                                                            <div class="mb-3 position-absolute end-0"
                                                                style="margin-right: -5px">
                                                                <button type="button" class="btn btn-primary "
                                                                    onclick="addSection()">+</button>
                                                            </div>
                                                        @else
                                                            <!-- minus button -->
                                                            <div class="mb-3 position-absolute end-0"
                                                                style="margin-right: -5px">
                                                                <button type="button" class="btn btn-danger "
                                                                    onclick="removeSection(this)">-</button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>

                                    {{-- end sections --}}

                                </div>

                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <x-custom.form-submit text="{{ __('buttons.edit') }}" class="btn-primary" />
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
                    }
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
