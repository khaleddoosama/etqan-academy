@extends('admin.master')
@section('title')
    {{ __('buttons.show_section') }}
@endsection
@section('content')
    <div class="content-wrapper">

        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="px-4 mb-3 row">
                    <div class="w-50">
                        <h1> {{ __('buttons.show_section') }} </h1>
                    </div>
                    <div class="w-50">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('main.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.courses.index') }}">{{ $section->course->title }}</a></li>

                            <li class="breadcrumb-item active">{{ __('buttons.show_section') }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class=" content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><small>
                                        {{ __('attributes.section') }}: {{ $section->title }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            {{--  --}}
                            <div class="mx-3 my-3 callout callout-info">
                                <h5>{{ __('attributes.description') }}:</h5>
                                <p>{{ $section->description }}</p>
                            </div>

                            <div class="mx-3 my-3 callout callout-info">
                                <h5>{{ __('attributes.lectures') }}:</h5>
                                <div id="accordion">
                                    @foreach ($section->lectures as $lecture)
                                        <div class="card card-lecture" data-id="{{ $lecture->id }}" style="cursor: grab">
                                            <div class="card-header" id="heading-{{ $loop->iteration }}">
                                                <h5 class="mb-0 row justify-content-between align-items-center">
                                                    <a class="btn btn-link btnn text-primary text-decoration-none"
                                                        {{-- data-toggle="collapse" --}}
                                                        href="{{ route('admin.lectures.edit', $lecture->id) }}"
                                                        {{-- data-target="#collapse-{{ $loop->iteration }}" --}} {{-- aria-expanded="false" --}} {{-- aria-controls="collapse-{{ $loop->iteration }}" --}}
                                                        @if ($lecture->processed == 0) style="color: orange !important"
                                                        @elseif ($lecture->processed == -1)
                                                        style="color: red !important; text-decoration: line-through !important" @endif>
                                                        {{ __('attributes.video') }} #{{ $loop->iteration }}:
                                                        {{ $lecture->title }} @if ($lecture->processed == 0)
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        @endif
                                                    </a>

                                                    <div>

                                                        <x-custom.edit-button :route="'admin.lectures.edit'" :id="$lecture->id" />
                                                        {{-- get compoent called delete-button --}}
                                                        <x-custom.delete-button :route="'admin.lectures.destroy'" :id="$lecture->id" />
                                                    </div>

                                                </h5>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>




                            <div class="card-footer">
                                <x-custom.form-submit text="{{ __('buttons.add_lecture') }}" class="mb-3 btn-primary"
                                    attr='data-toggle=modal data-target=#createVideoModal' />

                                <x-custom.form-submit text="{{ __('buttons.get_lecture') }}" class="btn-secondary"
                                    attr='data-toggle=modal data-target=#getVideoModal' />
                            </div>
                            <!-- form start -->

                        </div>
                        <!-- /.card -->
                    </div>

                </div>
                <!-- /.row -->
                @include('admin.section.create-lecture-modal')

                @include('admin.section.duplicate-lecture-modal')
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('scripts')
    <script>
        var operationType = 'store'; // Pass the variable
    </script>
    <script src="{{ asset('asset/admin/dist/js/uploadvideo.js') }}" defer></script>

    <script>
        // when upload thumbnail show the thumbnail preview
        $('#input-thumbnail').change(function() {
            $('#showThumbnail').show('blind');
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showThumbnail img').attr('src', e.target
                    .result);
            }
            reader.readAsDataURL(this.files[0]);
        })
        // when upload video show the video preview
        $('#input-video').change(function() {
            $('#showVideo').show('blind');

            // Get the selected file
            var file = this.files[0];

            // Check file size (in bytes), for example, limit to 100MB (100 * 1024 * 1024 bytes)
            var maxSize = 100 * 1024 * 1024; // 100MB

            if (file.size > maxSize) {
                // can't preview this file but you can upload it to the server
                toastr.warning('File size is too large to preview but you can upload it to the server');
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showVideo video').attr('src', e.target
                    .result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        // when upload attachments show the attachments preview
        $('#input-attachments').change(function() {
            console.log('attachments');
            $('#showAttachments').show('blind');

            // remove old preview
            $('#showAttachments').empty();

            // get the files
            var files = this.files;

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.createElement('div');
                    preview.classList.add('col-md-6');
                    preview.classList.add('col-12');
                    preview.classList.add('my-3');
                    let type = e.target.result.split(':')[1].split('/')[0];

                    if (type == 'image') {
                        preview.innerHTML = '<img class="img-thumbnail img-fluid" src="' + e.target.result +
                            '"/>';
                    } else if (type == 'video') {
                        preview.innerHTML = '<video style="width: 100%; height: 100%;" controls src="' + e
                            .target.result + '" >';
                    } else if (type == 'audio') {
                        preview.innerHTML =
                            '<audio style="width: 100%; height: 100%;" controls class="audio"  src="' + e.target
                            .result + '" >';
                    } else if (type == 'application') {
                        preview.innerHTML = '<iframe style="width: 100%; height: 100%;" src="' + e.target
                            .result + '" >' + '</iframe>';
                    } else {
                        console.log(e.target);
                        preview.innerHTML = '<a class="text-primary" href="' + e.target.result +
                            '" target="_blank">View attachment</a>';
                    }
                    $('#showAttachments').append(preview);
                }
                reader.readAsDataURL(file);
            }

        });
    </script>


    <script>
        $(function() {
            $('#accordion').sortable({
                update: function(event, ui) {
                    var lectureOrder = [];
                    $('#accordion .card-lecture').each(function(index) {
                        lectureOrder.push($(this).data('id'));
                    });

                    $.ajax({
                        url: '{{ route('admin.lectures.updateOrder') }}',
                        method: 'Post',
                        data: {
                            lectures: lectureOrder,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // toastr.success
                            toastr.success('Order updated successfully');
                            //btnn rewrite the order
                            $('#accordion .card-lecture').each(function(index) {
                                $(this).find('.btnn').text(
                                    `{{ __('attributes.video') }} #${index + 1}: ${$(this).find('.btnn').text().split(':')[1]}`
                                );
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log(error);
                            console.log(xhr);
                        }
                    });
                }
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $('#getVideoModal').on('shown.bs.modal', function() {
                $('#input-get-course').select2({
                    dropdownParent: $('#getVideoModal')
                });
                $('#input-get-section').select2({
                    dropdownParent: $('#getVideoModal')
                });
                $('#input-get-lecture').select2({
                    dropdownParent: $('#getVideoModal')
                });
            });
        });
    </script>

    <script>
        $('#input-get-course').change(function() {
            var course_id = $(this).val();
            if (course_id) {
                $.ajax({

                    url: '{{ route('admin.sections.get', ':course_id') }}'.replace(':course_id',
                        course_id),
                    type: 'Get',
                    success: function(data) {
                        $('#div-get-setion').show('blind');
                        $options =
                            '<option selected="selected" disabled>{{ __('buttons.choose') }}</option>';
                        data.data.forEach(element => {
                            $options +=
                                `<option value="${element.id}">${element.title}</option>`;
                        });
                        $('#input-get-section').html($options);
                    }
                });
            } else {
                $('#div-get-setion').hide('blind');
                $('#div-get-lecture').hide('blind');
            }
        });

        $('#input-get-section').change(function() {
            var section_id = $(this).val();
            if (section_id) {
                $.ajax({
                    url: '{{ route('admin.lectures.get', ':section_id') }}'.replace(':section_id',
                        section_id),
                    type: 'Get',
                    success: function(data) {
                        $('#div-get-lecture').show('blind');
                        $options =
                            '<option selected="selected" disabled>{{ __('buttons.choose') }}</option>';
                        data.data.forEach(element => {
                            $options +=
                                `<option value="${element.id}">${element.title}</option>`;
                        });
                        $('#input-get-lecture').html($options);
                    }
                });
            } else {
                $('#div-get-lecture').hide('blind');
            }
        });
    </script>
@endsection
