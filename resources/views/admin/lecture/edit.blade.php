@extends('admin.master')
@section('title')
    {{ __('buttons.edit_lecture') }}
@endsection
@section('styles')
    <link href="https://vjs.zencdn.net/8.16.1/video-js.css" rel="stylesheet" />
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
                                    href="{{ route('admin.courses.index') }}">{{ $lecture->section->course->title }}</a></li>
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
                            <form action="{{ route('admin.lectures.update', $lecture) }}" method="POST" id="form1" enctype="multipart/form-data">
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


                                    <x-custom.form-group class="col-md-6 col-12" type="text" name="video" value="{{ $lecture->video }}" placeholder="Video ID From YouTube" />

                                    <div class="col-md-6 col-12">
                                        <iframe width="560" height="315"
                                            src="https://www.youtube.com/embed/{{ $lecture->video }}?rel=0" frameborder="0"
                                            allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>

                                    </div>
                                </div>

                                {{-- duration --}}
                                <div class="row mx-3 my-3 callout callout-info">

                                    <x-custom.form-group class="col-md-4 col-12" type="number" name="hours" value="{{ $lecture->hours }}" />

                                    <x-custom.form-group class="col-md-4 col-12" type="number" name="minutes" value="{{ $lecture->minutes }}" />

                                    <x-custom.form-group class="col-md-4 col-12" type="number" name="seconds" value="{{ $lecture->seconds }}" />
                                </div>

                                {{-- attachments --}}
                                <div class="row mx-3 my-3 callout callout-info">

                                    <x-custom.form-group class="col-md-6 col-12" type="file" name="attachments[]"
                                    :multiple="true" />

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
        $('#form1').validate({
            rules: {
                title: {
                    required: true,
                },
                video: {
                    required: true,
                }
            },
            messages: {
                title: {
                    required: "{{ __('validation.required', ['attribute' => __('attributes.title')]) }}"
                },
                video: {
                    required: "{{ __('validation.required', ['attribute' => __('attributes.video')]) }}",
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
            },


        });
    </script>
@endsection
