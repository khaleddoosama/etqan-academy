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

                            <div class="modal fade" id="createVideoModal" tabindex="-1" role="dialog"
                                aria-labelledby="createVideoModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.lectures.store') }}" method="POST" id="form1"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="toggler">
                                                <div id="effect"
                                                    class="text-center ui-widget-content ui-corner-all bg-primary">
                                                    <p>
                                                        <strong>{{ __('messages.dont_close_or_reload') }}</strong>
                                                    </p>

                                                    <div id="progressBarContainer" class="relative w-100 bg-light">
                                                        <div id="progressBar"
                                                            style="height: 20px; background-color: #4CAF50; width: 0%;">
                                                        </div>
                                                        <p id="progressText" class="position-absolute"
                                                            style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                                            0%
                                                        </p>
                                                    </div>
                                                    <div id="status" class="flex items-center justify-between px-3 pt-2">
                                                        <p id="statusText"></p>
                                                        <button type="button" id="cancelUpload"
                                                            class="mb-3 btn btn-danger btn-xs">Cancel Upload</button>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="modal-header">
                                                <h5 class="modal-title" id="createVideoModalLabel">
                                                    {{ __('buttons.add_video') }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                <input type="hidden" name="section_id" value="{{ $section->id }}"
                                                    id="input-section_id">

                                                <x-custom.form-group type="text" name="title" />


                                                <div class='form-group row'>
                                                    <x-input-label for="input-video"
                                                        class="col-sm-12 col-form-label">{{ __('attributes.video') }}</x-input-label>

                                                    <div class="input-group col-sm-12">
                                                        <div class="custom-file">
                                                            <input type="file" name="video" id="input-video"
                                                                class="custom-file-input" accept="video/*">
                                                            <x-input-label for="input-video"
                                                                class="custom-file-label col-form-label"
                                                                data-browse="{{ __('buttons.browse') }}">{{ __('buttons.choose') }}</x-input-label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- show video --}}
                                                <div class="form-group" style="display: none" id="showVideo">
                                                    <video width="320" height="240" controls id="video">
                                                        <source src="" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div>

                                                <x-custom.form-group type="file" name="thumbnail" />

                                                {{-- show thumbnail --}}
                                                <div class="form-group" style="display: none" id="showThumbnail">
                                                    <img src="" alt="" id="thumbnail"
                                                        class="img-thumbnail" style="height: 240px">
                                                </div>

                                                <div class='row form-group col-md-12'>
                                                    <x-input-label for="summernote"
                                                        class='col-sm-12 col-form-label'>{{ __('attributes.description') }}</x-input-label>

                                                    <div class='col-sm-12'>
                                                        <textarea name="description" id="summernote" class="form-control summernote" rows="1">{{ old('description') ?? '' }}</textarea>
                                                    </div>
                                                </div>

                                                <x-custom.form-group type="file" name="attachments[]"
                                                    :multiple="true" />

                                                <div class="form-group row" style="display: none" id="showAttachments">

                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ __('buttons.close') }}</button>
                                                <x-custom.form-submit text="{{ __('buttons.save') }}"
                                                    class=" btn-primary" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="getVideoModal" tabindex="-1" role="dialog"
                                aria-labelledby="getVideoModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.lectures.duplicate') }}" method="POST"
                                            id="form2" enctype="multipart/form-data">
                                            @csrf

                                            <div class="modal-header">
                                                <h5 class="modal-title" id="createVideoModalLabel">
                                                    {{ __('buttons.get_video') }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                <input type="hidden" name="section_id" value="{{ $section->id }}">


                                                <div class='form-group row'>
                                                    <x-input-label for="input-lecture"
                                                        class="col-sm-12 col-form-label">{{ __('main.duplicate_lecture') }}</x-input-label>
                                                    <div class="col-sm-12" style="font-weight: 200">
                                                        <select class="form-control select2" style="width: 100%;"
                                                            name="lecture_id">
                                                            <option selected="selected" disabled>
                                                                {{ __('buttons.choose') }}</option>
                                                            @foreach ($lectures as $option)
                                                                <option value="{{ $option->id }}">
                                                                    {{ $option->section->course->title }} -
                                                                    {{ $option->section->title }} - {{ $option->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <x-input-error :messages="$errors->get('lecture_id')" style="padding: 0 7.5px;margin: 0;" />
                                                </div>


                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ __('buttons.close') }}</button>
                                                <x-custom.form-submit text="{{ __('buttons.save') }}"
                                                    class=" btn-primary" />
                                            </div>
                                        </form>
                                    </div>
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
@endsection
