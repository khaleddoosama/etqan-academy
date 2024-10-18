@extends('admin.master')
@section('title')
    {{ __('buttons.edit_lecture') }}
@endsection
@section('content')
    <div class="content-wrapper">

        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        {{-- <x-custom.header-page title="{{ __('buttons.edit_lecture') }}" /> --}}

        <section class="content-header">
            <div class="container-fluid">
                <div class="px-4 mb-3 row">
                    <div class="w-50">
                        <h1> {{ __('buttons.edit_lecture') }} </h1>
                    </div>
                    <div class="w-50">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('main.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.courses.index') }}">{{ $lecture->course->title }}</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.sections.show', $lecture->section->id) }}">{{ $lecture->section->title }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.edit_lecture') }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div
                            class="card {{ $lecture->processed == 0 ? 'card-warning' : ($lecture->processed == -1 ? 'card-danger' : 'card-primary') }}">
                            <div class="toggler">
                                <div id="effect" class="text-center ui-widget-content ui-corner-all bg-primary">
                                    <p>
                                        <strong>{{ __('messages.dont_close_or_reload') }}</strong>
                                    </p>

                                    <div id="progressBarContainer" class="relative w-100 bg-light">
                                        <div id="progressBar" style="height: 20px; background-color: #4CAF50; width: 0%;">
                                        </div>
                                        <p id="progressText" class="position-absolute"
                                            style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                            0%
                                        </p>
                                    </div>
                                    <div id="status" class="flex items-center justify-between px-3 pt-2">
                                        <p id="statusText"></p>
                                        <button type="button" id="cancelUpload" class="mb-3 btn btn-danger btn-xs">Cancel
                                            Upload</button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-header">
                                <h3 class="card-title"><small>
                                        {{ __('attributes.lecture') }}: {{ $lecture->title }}</small></h3>
                            </div>

                            {{-- check box --}}
                            <div class="mx-3 mt-3  callout callout-info d-flex justify-content-between align-items-center">
                                <h5>{{ __('attributes.is_free') }}:
                                    {{ $lecture->is_free ? __('status.yes') : __('status.no') }}</h5>
                                <form action="{{ route('admin.lectures.changeIsFree', $lecture) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="custom-control custom-checkbox">
                                        @if (!$lecture->is_free)
                                            <button type="submit" class="btn btn-success"
                                                title="{{ __('buttons.activate') }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="fas fa-toggle-off"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-danger"
                                                title="{{ __('buttons.deactivate') }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>

                            <!-- /.card-header -->
                            <form action="{{ route('admin.lectures.update', $lecture) }}" method="POST" id="form1">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="id" value="{{ $lecture->id }}" id="input-id">



                                <div class="mx-3 my-3 callout callout-info">
                                    <h5>{{ __('attributes.title') }}:</h5>
                                    <input type="text" name="title" class="form-control" id="input-title"
                                        value="{{ $lecture->title }}">
                                </div>
                                {{-- description --}}
                                <div class="mx-3 my-3 callout callout-info">
                                    <h5>{{ __('attributes.description') }}:</h5>
                                    <textarea name="description" id="summernote" class="form-control summernote" rows="1">{{ $lecture->description }}</textarea>
                                </div>

                                {{-- section --}}
                                {{-- <div class='form-group row'>
                                    <x-input-label for="input-section"
                                        class="col-sm-12 col-form-label">{{ __('main.transfer_lecture') }}</x-input-label>
                                    <div class="col-sm-12" style="font-weight: 200">
                                        <select class="form-control select2" id="input-section_id"
                                            style="width: 100%;" name="section_id">
                                            <option selected="selected" disabled>
                                                {{ __('buttons.choose') }}</option>
                                            @foreach ($sections as $option)
                                                <option value="{{ $option->id }}"
                                                    @if ($lecture->section->id == $option->id) selected @endif>
                                                    {{ $option->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <x-input-error :messages="$errors->get('section_id')" style="padding: 0 7.5px;margin: 0;" />
                                </div> --}}
                                <div class="mx-3 my-3 callout callout-info">
                                    <h5>{{ __('main.transfer_lecture') }}:</h5>
                                    <select class="form-control select2" id="input-section_id" style="width: 100%;"
                                        name="section_id">
                                        <option selected="selected" disabled>
                                            {{ __('buttons.choose') }}</option>
                                        @foreach ($sections as $option)
                                            <option value="{{ $option->id }}"
                                                @if ($lecture->section->id == $option->id) selected @endif>
                                                {{ $option->title }} - {{ $option->course->title }}</option>
                                        @endforeach
                                    </select>


                                    <x-input-error :messages="$errors->get('section_id')" style="padding: 0 7.5px;margin: 0;" />

                                </div>

                                {{-- video --}}
                                <div class="row mx-3 my-3 callout callout-info">

                                    <x-custom.form-group class="col-md-6 col-12" type="file" name="video" />

                                    <div class="col-md-6 col-12">
                                        {{-- <h5 class="text-right">{{ __('attributes.video') }}</h5> --}}
                                        <video width="320" height="240" controls style="float: right" id="show-video">
                                            <source src="{{ $lecture->getBestQualityVideoAttribute() }}" type="video/mp4">
                                        </video>
                                    </div>
                                </div>
                                {{-- quailties --}}
                                <div class="row mx-3 my-3 callout callout-info">
                                    <div class="col-12">

                                        <table id="" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th colspan="5" class="text-center">MP4 Formats</th>
                                                </tr>
                                                <tr>
                                                    <th>240 MP4</th>
                                                    <th>360 MP4</th>
                                                    <th>480 MP4</th>
                                                    <th>720 MP4</th>
                                                    <th>1080 MP4</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (!is_null($lecture->convertedVideo))
                                                    @php
                                                        $mp4_formats = [
                                                            'mp4_Format_240' => '240 MP4',
                                                            'mp4_Format_360' => '360 MP4',
                                                            'mp4_Format_480' => '480 MP4',
                                                            'mp4_Format_720' => '720 MP4',
                                                            'mp4_Format_1080' => '1080 MP4',
                                                        ];
                                                        $webm_formats = [
                                                            'webm_Format_240' => '240 Webm',
                                                            'webm_Format_360' => '360 Webm',
                                                            'webm_Format_480' => '480 Webm',
                                                            'webm_Format_720' => '720 Webm',
                                                            'webm_Format_1080' => '1080 Webm',
                                                        ];
                                                    @endphp

                                                    <tr>
                                                        @foreach ($mp4_formats as $key => $label)
                                                            <td>
                                                                @if (is_null($lecture->convertedVideo[$key]))
                                                                    <span class="text-danger">Not Found</span>
                                                                @elseif (!Storage::exists($lecture->convertedVideo[$key]))
                                                                    <span class="text-danger">Converted But Not Found In
                                                                        Server</span>
                                                                @else
                                                                    <a href="{{ Storage::url($lecture->convertedVideo[$key]) }}"
                                                                        target="_blank"
                                                                        class="text-primary">{{ $label }}</a>
                                                                @endif
                                                                {{-- <input type="text" name="video_paths[{{ $key }}]" value="{{ $lecture->convertedVideo[$key] }}" class="form-control mt-2" /> --}}
                                                                {{-- convert to textarea --}}
                                                                <textarea name="video_paths[{{ $key }}]" class="form-control mt-2" rows="5">{{ $lecture->convertedVideo[$key] }}</textarea>

                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @else
                                                    <td colspan="5" class="text-center">
                                                        <span class="text-danger">Not Converted Yet</span>
                                                    </td>
                                                @endif
                                            </tbody>

                                            <thead>
                                                <tr>
                                                    <th colspan="5" class="text-center">WebM Formats</th>
                                                </tr>
                                                <tr>
                                                    <th>240 Webm</th>
                                                    <th>360 Webm</th>
                                                    <th>480 Webm</th>
                                                    <th>720 Webm</th>
                                                    <th>1080 Webm</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (!is_null($lecture->convertedVideo))
                                                    <tr>
                                                        @foreach ($webm_formats as $key => $label)
                                                            <td>
                                                                @if (is_null($lecture->convertedVideo[$key]))
                                                                    <span class="text-danger">Not Found</span>
                                                                @elseif (!Storage::exists($lecture->convertedVideo[$key]))
                                                                    <span class="text-danger">Converted But Not Found
                                                                        In Server</span>
                                                                @else
                                                                    <a href="{{ Storage::url($lecture->convertedVideo[$key]) }}"
                                                                        target="_blank"
                                                                        class="text-primary">{{ $label }}</a>
                                                                @endif
                                                                {{-- <input type="text" name="video_paths[{{ $key }}]" value="{{ $lecture->convertedVideo[$key] }}" class="form-control mt-2" /> --}}
                                                                {{-- convert to textarea --}}
                                                                <textarea name="video_paths[{{ $key }}]" class="form-control mt-2" rows="5">{{ $lecture->convertedVideo[$key] }}</textarea>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @else
                                                    <td colspan="5" class="text-center">
                                                        <span class="text-danger">Not Converted Yet</span>
                                                    </td>
                                                @endif
                                            </tbody>
                                        </table>
                                        <div class="text-center mt-3">
                                            <button type="button" class="btn btn-primary" id="updateAllButton">Update
                                                All</button>
                                        </div>


                                    </div>
                                </div>


                                {{-- thumbnail --}}
                                <div class="row mx-3 my-3 callout callout-info">

                                    <x-custom.form-group class="col-md-6 col-12" type="file" name="thumbnail" />

                                    <div class="col-md-6 col-12">
                                        <img src="{{ $lecture->thumbnail_url }}" alt="{{ $lecture->title }} image"
                                            class="img-thumbnail" id="show-thumbnail"
                                            style="max-height: 50vh;float: right">
                                    </div>
                                </div>

                                {{-- attachments --}}
                                <div class="row mx-3 my-3 callout callout-info">

                                    <x-custom.form-group class="col-md-6 col-12" type="file" name="attachments[]"
                                        multiple />

                                    <div class="col-12">
                                        <table id="example2" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('attributes.attachment') }}</th>
                                                    <th>{{ __('attributes.name') }}</th>
                                                    <th>{{ __('attributes.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (!is_null($lecture->attachments))
                                                    @foreach ($lecture->attachments as $key => $attachment)
                                                        <tr class="attachment">
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>
                                                                @if (Str::contains($attachment['type'], 'image'))
                                                                    <img src="{{ Storage::url($attachment['path']) }}"
                                                                        alt="{{ $attachment['originalName'] }}"
                                                                        class="img-thumbnail" style="height: 100px">
                                                                @elseif (Str::contains($attachment['type'], 'video'))
                                                                    <video style="height: 100px" controls>
                                                                        <source
                                                                            src="{{ Storage::url($attachment['path']) }}"
                                                                            type="video/mp4">
                                                                    </video>
                                                                @elseif (Str::contains($attachment['type'], 'audio'))
                                                                    <audio style="height: 100px" controls>
                                                                        <source
                                                                            src="{{ Storage::url($attachment['path']) }}"
                                                                            type="audio/mp3">
                                                                    </audio>
                                                                @elseif (Str::contains($attachment['type'], 'application'))
                                                                    <iframe src="{{ Storage::url($attachment['path']) }}"
                                                                        style="width: 100%; height: 100px;"></iframe>
                                                                @else
                                                                    <a href="{{ Storage::url($attachment['path']) }}"
                                                                        target="_blank">{{ $attachment['originalName'] }}</a>
                                                                @endif
                                                            </td>
                                                            <td class="text-edit">{{ $attachment['originalName'] }}</td>
                                                            <td style="display: none">
                                                                <form
                                                                    action="{{ route('admin.lectures.updateAttachment', $lecture) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="attachment_path"
                                                                        value="{{ $attachment['path'] }}">
                                                                    <input type="text" name="attachment_name"
                                                                        class="form-control "
                                                                        value="{{ $attachment['originalName'] }}"
                                                                        id="input-name-{{ $key }}">
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <!-- Button trigger modal -->
                                                                <button type="button" class="btn btn-danger"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteModal-{{ $key }}"
                                                                    title="{{ __('buttons.delete') }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>

                                                                <!-- Modal -->
                                                                <div class="modal fade"
                                                                    id="deleteModal-{{ $key }}" tabindex="-1"
                                                                    role="dialog"
                                                                    aria-labelledby="exampleModalCenterTitle"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered"
                                                                        role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="exampleModalLongTitle">
                                                                                    {{ __('messages.are_you_sure') }}</h5>
                                                                                <button type="button" class="close"
                                                                                    data-dismiss="modal"
                                                                                    aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                {{ __('messages.you_want_to_delete_it') }}
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <x-custom.close-modal-button />

                                                                                <form
                                                                                    action="{{ route('admin.lectures.deleteAttachment', $lecture) }}"
                                                                                    method="POST"
                                                                                    style="display: inline-block;">
                                                                                    @method('PUT')
                                                                                    @csrf
                                                                                    <input type="hidden"
                                                                                        name="attachment_path"
                                                                                        value="{{ $attachment['path'] }}">
                                                                                    <button type="submit"
                                                                                        class="btn btn-danger">{{ __('buttons.delete') }}</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('attributes.attachment') }}</th>
                                                    <th>{{ __('attributes.name') }}</th>
                                                    <th>{{ __('attributes.action') }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">{{ __('buttons.update') }}</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('scripts')
    <script>
        var operationType = 'update'; // Pass the variable
    </script>
    <script src="{{ asset('asset/admin/dist/js/uploadvideo.js') }}" defer></script>

    <script>
        $(document).ready(function() {
            $('.text-edit').click(function() {
                $(this).hide();
                var next = $(this).next().show();
                next.find('input').focus();
                var originalValue = $(this).text();


                //when click in any element change element to text and vice versa
                next.find('input').off('blur').on('blur', function() {
                    let parent = $(this).parent();
                    let prevtd = parent.prev();
                    parent.hide();
                    // near text-edit button
                    prevtd.show();
                    // console.log($(this).prev());
                    prevtd.text($(this).val());

                    if ($(this).val() == '' || $(this).val() == originalValue) {
                        return;
                    } else {
                        // send ajax
                        $.ajax({
                            url: "{{ route('admin.lectures.updateAttachment', $lecture) }}",
                            method: "PUT",
                            data: {
                                _token: "{{ csrf_token() }}",
                                attachment_name: $(this).val(),
                                attachment_path: $(this).prev().val(),
                            },
                            success: function(response) {
                                // toastr.success
                                toastr.success('Name updated successfully');
                            },
                            error: function(xhr, status, error) {
                                console.log(error);
                                console.log(xhr);
                                console.log(status);
                                // toastr.error
                                toastr.error('Error updating name');
                            }
                        });
                    }
                });

                // form on submit
                next.find('form').on('submit', function(e) {
                    e.preventDefault();
                });
            });
        })
    </script>

    <script>
        $(document).ready(function() {
            // when change in input-video show the video preview show-video
            $('#input-video').change(function() {
                $('#show-video').hide('blind');

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
                    $('#show-video').attr('src', e.target
                        .result);
                    $('#show-video').show('blind');
                }
                reader.readAsDataURL(this.files[0]);
            })

            // when change in input-thumbnail show the thumbnail preview show-thumbnail
            $('#input-thumbnail').change(function() {
                $('#show-thumbnail').hide('blind');
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#show-thumbnail').attr('src', e.target
                        .result);
                    $('#show-thumbnail').show('blind');
                }
                reader.readAsDataURL(this.files[0]);
            })

            // when change in input-attachments show the attachments preview add it in table example2
            $('#input-attachments').change(function() {
                // $('#show-attachments').show('blind');
                var files = this.files;

                // remove old preview attachments unless they have class attachment
                $('#example2 tbody tr').each(function() {
                    if (!$(this).hasClass('attachment')) {
                        $(this).remove();
                    }
                });

                for (let i = 0; i < files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var type = files[i].type;
                        var path = e.target.result;
                        var name = files[i].name;
                        // check if type is image
                        var attachment;

                        if (type.indexOf('image') == 0) {
                            attachment =
                                `<img src="${path}" alt="${name}" class="img-thumbnail" style="height: 100px">`;
                        } else if (type.indexOf('video') == 0) {
                            attachment = `<video style="height: 100px" controls>
                                <source src="${path}" type="video/mp4">
                            </video>`;
                        } else if (type.indexOf('audio') == 0) {
                            attachment = `<audio style="height: 100px" controls>
                                <source src="${path}" type="audio/mp3">
                            </audio>`;
                        } else if (type.indexOf('application') == 0) {
                            attachment =
                                `<iframe src="${path}" style="width: 100%; height: 100px;"></iframe>`;
                        } else {
                            attachment =
                                `<a href="${path}" target="_blank" class="text-primary">${name}</a>`;
                        }

                        var tr = `<tr>
                            <td></td>
                            <td>
                                ${attachment}
                            </td>
                            <td>${name}</td>
                            <td>

                            </td>
                        </tr>`;
                        $('#example2 tbody').append(tr);
                    }
                    reader.readAsDataURL(files[i]);
                }
            })

        })
    </script>

    <script>
        $(document).ready(function() {

            $('#updateAllButton').on('click', function() {
                let formData = {};
                $('textarea[name^="video_paths"]').each(function() {

                    formData[$(this).attr('name')] = $(this).val();
                });

                $.ajax({
                    url: "{{ route('admin.lectures.updateVideoPath', $lecture) }}",
                    type: 'PUT',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('An error occurred while updating the video paths.');
                    }
                });
            });
        });
    </script>
@endsection
