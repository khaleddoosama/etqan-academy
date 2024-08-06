@extends('admin.master')
@section('title')
    {{ __('buttons.show_section') }}
@endsection
@section('content')
    <div class="content-wrapper">

        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.show_section') }}" />

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
                                        <div class="card card-lecture" data-id="{{ $lecture->id }}">
                                            <div class="card-header" id="heading-{{ $loop->iteration }}">
                                                <h5 class="mb-0 row justify-content-between align-items-center">
                                                    <button class="btn btn-link btnn" data-toggle="collapse"
                                                        data-target="#collapse-{{ $loop->iteration }}" aria-expanded="false"
                                                        aria-controls="collapse-{{ $loop->iteration }}"
                                                        @if ($lecture->processed == 0) style="color: orange"
                                                        @elseif ($lecture->processed == -1)
                                                        style="color: red; text-decoration: line-through" @endif>
                                                        {{ __('attributes.video') }} #{{ $loop->iteration }}:
                                                        {{ $lecture->title }} @if ($lecture->processed == 0)
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        @endif
                                                    </button>

                                                    <div>
                                                        {{-- <button type="button" class="btn btn-primary"
                                                            title="{{ __('buttons.edit') }}" data-toggle='modal'
                                                            data-target='#editVideoModal-{{ $loop->iteration }}'
                                                            style="color: white; text-decoration: none;">
                                                            <i class="fas fa-edit"></i>
                                                        </button> --}}
                                                        <x-custom.edit-button :route="'admin.lectures.edit'" :id="$lecture->id" />
                                                        {{-- get compoent called delete-button --}}
                                                        <x-custom.delete-button :route="'admin.lectures.destroy'" :id="$lecture->id" />
                                                    </div>
                                                    <!--
                                                        <div class="modal fade" id="editVideoModal-{{ $loop->iteration }}"
                                                            tabindex="-1" role="dialog" aria-labelledby="editVideoModalLabel"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-lg" role="document">
                                                                <div class="modal-content">
                                                                    <form id="editForm-{{ $loop->iteration }}" class="editForm"
                                                                        action="{{ route('admin.lectures.update', $lecture) }}"
                                                                        method="POST" enctype="multipart/form-data">
                                                                        @method('PUT')
                                                                        @csrf
                                                                        <div class="toggler">
                                                                            <div id="effect-{{ $loop->iteration }}"
                                                                                style="display: none"
                                                                                class="text-center ui-widget-content ui-corner-all bg-primary">
                                                                                <p>
                                                                                    <strong>{{ __('messages.dont_close_or_reload') }}</strong>
                                                                                </p>

                                                                                <div id="progressBarContainer-{{ $loop->iteration }}"
                                                                                    class="relative w-100 bg-light">
                                                                                    <div id="progressBar-{{ $loop->iteration }}"
                                                                                        style="height: 20px; background-color: #4CAF50; width: 0%;">
                                                                                    </div>
                                                                                    <p id="progressText-{{ $loop->iteration }}"
                                                                                        class="position-absolute"
                                                                                        style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                                                                    </p>
                                                                                </div>
                                                                                <div id="status-{{ $loop->iteration }}"
                                                                                    class="flex items-center justify-between px-3 pt-2">
                                                                                    <p id="statusText"></p>
                                                                                    <button type="button"
                                                                                        id="cancelUpload-{{ $loop->iteration }}"
                                                                                        class="mb-3 btn btn-danger btn-xs">Cancel
                                                                                        Upload</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="editVideoModalLabel">
                                                                                {{ __('buttons.edit_video') }}
                                                                            </h5>
                                                                            <button type="button" class="close"
                                                                                data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                            <input type="hidden" name="id"
                                                                                value="{{ $lecture->id }}"
                                                                                id="input-id-{{ $loop->iteration }}">

                                                                            {{-- <x-custom.form-group type="text" name="title"
                                                                            value="{{ $lecture->title }}" /> --}}

                                                                            <div class='form-group row'>
                                                                                <x-input-label
                                                                                    for="input-title-{{ $loop->iteration }}"
                                                                                    class="col-sm-12 col-form-label">{{ __('attributes.title') }}</x-input-label>

                                                                                <div class="col-sm-12">
                                                                                    <x-text-input type="text" name="title"
                                                                                        id="input-title-{{ $loop->iteration }}"
                                                                                        value="{{ old('title') ?? ($lecture->title ?? '') }}"
                                                                                        class="form-control" />
                                                                                </div>

                                                                                <x-input-error :messages="$errors->get('title')"
                                                                                    style="padding: 0 7.5px;margin: 0;" />
                                                                            </div>

                                                                            <div class='form-group row'>
                                                                                <x-input-label
                                                                                    for="input-section-{{ $loop->iteration }}"
                                                                                    class="col-sm-12 col-form-label">{{ __('main.transfer_lecture') }}</x-input-label>
                                                                                <div class="col-sm-12" style="font-weight: 200">
                                                                                    <select class="form-control select2"
                                                                                        id="input-section_id-{{ $loop->iteration }}"
                                                                                        style="width: 100%;" name="section_id">
                                                                                        <option selected="selected" disabled>
                                                                                            {{ __('buttons.choose') }}</option>
                                                                                        @foreach ($sections as $option)
    <option value="{{ $option->id }}"
                                                                                                @if ($section->id == $option->id) selected @endif>
                                                                                                {{ $option->title }}</option>
    @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <x-input-error :messages="$errors->get('section_id')"
                                                                                    style="padding: 0 7.5px;margin: 0;" />
                                                                            </div>

                                                                            <div class='form-group row'>
                                                                                <x-input-label
                                                                                    for="input-video-{{ $loop->iteration }}"
                                                                                    class="col-sm-12 col-form-label">{{ __('attributes.video') }}</x-input-label>

                                                                                <div class="input-group col-sm-12">
                                                                                    <div class="custom-file">
                                                                                        <input type="file" name="video"
                                                                                            id="input-video-{{ $loop->iteration }}"
                                                                                            class="custom-file-input"
                                                                                            accept="video/*">
                                                                                        <x-input-label
                                                                                            for="input-video-{{ $loop->iteration }}"
                                                                                            class="custom-file-label col-form-label"
                                                                                            data-browse="{{ __('buttons.browse') }}">{{ __('buttons.choose') }}</x-input-label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- show video --}}
                                                                            <div class="form-group" style="display: none"
                                                                                id="showVideo-{{ $loop->iteration }}">
                                                                                <video width="320" height="240" controls
                                                                                    id="video">
                                                                                    <source src="" type="video/mp4">
                                                                                    Your browser does not support the video
                                                                                    tag.
                                                                                </video>
                                                                            </div>


                                                                            <div class='form-group row'>
                                                                                <x-input-label
                                                                                    for="input-thumbnail-{{ $loop->iteration }}"
                                                                                    class="col-sm-12 col-form-label">{{ __('attributes.thumbnail') }}</x-input-label>

                                                                                <div class="input-group col-sm-12">
                                                                                    <div class="custom-file">
                                                                                        <input type="file" name="thumbnail"
                                                                                            id="input-thumbnail-{{ $loop->iteration }}"
                                                                                            class="custom-file-input"
                                                                                            accept="image/*">
                                                                                        <x-input-label
                                                                                            for="input-thumbnail-{{ $loop->iteration }}"
                                                                                            class="custom-file-label col-form-label"
                                                                                            data-browse="{{ __('buttons.browse') }}">{{ __('buttons.choose') }}</x-input-label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- show thumbnail --}}
                                                                            <div class="form-group" style="display: none"
                                                                                id="showThumbnail-{{ $loop->iteration }}">
                                                                                <img src="" alt=""
                                                                                    id="thumbnail" class="img-thumbnail"
                                                                                    style="height: 240px">
                                                                            </div>

                                                                            <div class='row form-group col-md-12'>
                                                                                <x-input-label
                                                                                    for="summernote-{{ $loop->iteration }}"
                                                                                    class='col-sm-12 col-form-label'>{{ __('attributes.description') }}</x-input-label>

                                                                                <div class='col-sm-12'>
                                                                                    <textarea name="description" id="summernote-{{ $loop->iteration }}" class="form-control summernote">{{ old('description') ?? ($lecture->description ?? '') }}</textarea>
                                                                                </div>
                                                                            </div>

                                                                            {{-- <x-custom.form-group type="file"
                                                                            name="attachments[]" :multiple="true" /> --}}

                                                                            <div class="form-group row">
                                                                                <x-input-label
                                                                                    for="input-attachments-{{ $loop->iteration }}"
                                                                                    class="col-sm-12 col-form-label">{{ __('attributes.add_attachments') }}</x-input-label>

                                                                                <div class="input-group col-sm-12">
                                                                                    <div class="custom-file">
                                                                                        <input type="file"
                                                                                            name="attachments[]"
                                                                                            id="input-attachments-{{ $loop->iteration }}"
                                                                                            class="custom-file-input" multiple>
                                                                                        <x-input-label
                                                                                            for="input-attachments-{{ $loop->iteration }}"
                                                                                            class="custom-file-label col-form-label"
                                                                                            data-browse="{{ __('buttons.browse') }}">{{ __('buttons.choose') }}</x-input-label>
                                                                                    </div>
                                                                                </div>

                                                                                <x-input-error :messages="$errors->get('attachments')"
                                                                                    style="padding: 0 7.5px;margin: 0;" />
                                                                            </div>

                                                                            <div class="form-group row" style="display: none"
                                                                                id="showAttachments-{{ $loop->iteration }}">

                                                                            </div>

                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">{{ __('buttons.close') }}</button>
                                                                            <button type="submit"
                                                                                class="btn btn-primary">{{ __('buttons.save') }}</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    -->

                                                </h5>
                                                {{-- <video style="height: 240px" controls id="video-{{ $loop->iteration }}">
                                                    <source id="mp4-source" src="{{ asset($lecture->video) }}"
                                                        type="video/mp4">
                                                </video> --}}
                                            </div>
                                            <!--
                                                @if ($lecture->processed == 1)
    <div id="collapse-{{ $loop->iteration }}" class="collapse"
                                                        aria-labelledby="heading-{{ $loop->iteration }}"
                                                        data-parent="#accordion" style="visibility: visible!important">

                                                        <div class="mx-3 my-3 callout callout-info row">
                                                            <h5>{{ __('attributes.description') }}:</h5>
                                                            <p>{!! $lecture->description !!}</p>
                                                        </div>
                                                        <div
                                                            class="card-body d-flex justify-content-between align-items-center">
                                                            {{-- show video --}}
                                                            <div class="mx-3 my-3 callout callout-info">
                                                                <h5>{{ __('attributes.video') }}:</h5>
                                                                <video style="height: 240px" controls
                                                                    id="video-{{ $loop->iteration }}">
                                                                    @if ($lecture->quality == 1080)
    <source id="webm-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->webm_Format_1080) }}"
                                                                            type="video/webm">
                                                                        <source id="mp4-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->mp4_Format_1080) }}"
                                                                            type="video/mp4">
@elseif ($lecture->quality == 720)
    <source id="webm-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->webm_Format_720) }}"
                                                                            type="video/webm">
                                                                        <source id="mp4-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->mp4_Format_720) }}"
                                                                            type="video/mp4">
@elseif ($lecture->quality == 480)
    <source id="webm-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->webm_Format_480) }}"
                                                                            type="video/webm">
                                                                        <source id="mp4-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->mp4_Format_480) }}"
                                                                            type="video/mp4">
@elseif ($lecture->quality == 360)
    <source id="webm-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->webm_Format_360) }}"
                                                                            type="video/webm">
                                                                        <source id="mp4-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->mp4_Format_360) }}"
                                                                            type="video/mp4">
@else
    <source id="webm-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->webm_Format_240) }}"
                                                                            type="video/webm">
                                                                        <source id="mp4-source"
                                                                            src="{{ Storage::url($lecture->convertedVideo->mp4_Format_240) }}"
                                                                            type="video/mp4">
    @endif
                                                                </video>
                                                            </div>

                                                            {{-- show thumbnail --}}
                                                            @if ($lecture->thumbnail)
    <div class="mx-3 my-3 callout callout-info">
                                                                    <h5>{{ __('attributes.thumbnail') }}:</h5>
                                                                    <img src="{{ Storage::url($lecture->thumbnail) }}"
                                                                        alt="{{ $lecture->title }}" class="img-thumbnail"
                                                                        style="height: 240px">
                                                                </div>
    @endif

                                                        </div>
                                                        {{-- show attachments --}}
                                                        @if ($lecture->attachments)
    <div class="mx-3 my-3 callout callout-info">
                                                                <h5>{{ __('attributes.attachments') }}:</h5>
                                                                <div class="row">
                                                                    @foreach ($lecture->attachments as $attachment)
    <div class="col-md">
                                                                            <h6>{{ $attachment['originalName'] }}:</h6>
                                                                            @if (Str::contains($attachment['type'], 'image'))
    <img src="{{ Storage::url($attachment['path']) }}"
                                                                                    alt="{{ $attachment['originalName'] }}"
                                                                                    class="img-thumbnail"
                                                                                    style="height: 100px">
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
    <iframe
                                                                                    src="{{ Storage::url($attachment['path']) }}"
                                                                                    style="width: 100%; height: 100px;"></iframe>
@else
    <a href="{{ Storage::url($attachment['path']) }}"
                                                                                    target="_blank">{{ $attachment['originalName'] }}</a>
    @endif
                                                                        </div>
    @endforeach
                                                                </div>
                                                            </div>
    @endif
                                                    </div>
@elseif ($lecture->processed == 0)
    <div id="collapse-{{ $loop->iteration }}" class="collapse"
                                                        aria-labelledby="heading-{{ $loop->iteration }}"
                                                        data-parent="#accordion" style="visibility: visible!important">
                                                        <div
                                                            class="card-body d-flex justify-content-between align-items-center">
                                                            {{ __('messages.processing') }}
                                                        </div>
                                                    </div>
@elseif ($lecture->processed == -1)
    <div id="collapse-{{ $loop->iteration }}" class="collapse bg-danger"
                                                        aria-labelledby="heading-{{ $loop->iteration }}"
                                                        data-parent="#accordion" style="visibility: visible!important">
                                                        <div
                                                            class="card-body d-flex justify-content-between align-items-center text-white">
                                                            {{ __('messages.failed') }}
                                                        </div>
                                                    </div>
    @endif
                                                -->
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
                                                    <div id="status"
                                                        class="flex items-center justify-between px-3 pt-2">
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
        // $(function() {
        //     // edit lecture
        //     @foreach ($section->lectures as $lecture)
        //         $("#effect-{{ $loop->iteration }}").hide();
        //         var activeUploadRequest{{ $loop->iteration }} = null; // This will hold the current upload request

        //         $('#editForm-{{ $loop->iteration }}').validate({
        //             rules: {
        //                 title: {
        //                     required: true,
        //                 },
        //                 video: {
        //                     accept: "video/*"
        //                 }
        //             },
        //             messages: {
        //                 title: {
        //                     required: "{{ __('validation.required', ['attribute' => __('attributes.title')]) }}"
        //                 },
        //                 video: {
        //                     accept: "{{ __('validation.accept', ['attribute' => __('attributes.video')]) }}"
        //                 }
        //             },
        //             errorElement: 'span',
        //             errorPlacement: function(error, element) {
        //                 error.addClass('invalid-feedback');
        //                 error.css('padding', '0 7.5px');
        //                 element.closest('.form-group').append(error);
        //             },
        //             highlight: function(element, errorClass, validClass) {
        //                 $(element).addClass('is-invalid');
        //             },
        //             unhighlight: function(element, errorClass, validClass) {
        //                 $(element).removeClass('is-invalid');
        //             }, // when everything is ok, send ajax request
        //             submitHandler: function(form) {
        //                 console.log(form);
        //                 var formData = new FormData(form);
        //                 var startTime = Date.now(); // Capture the start time of the upload
        //                 formData.append('_method', 'PUT');
        //                 activeUploadRequest{{ $loop->iteration }} = $.ajax({
        //                     url: "{{ route('admin.lectures.update', $lecture) }}",
        //                     type: 'POST',
        //                     data: formData,
        //                     contentType: false,
        //                     processData: false,
        //                     headers: {
        //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                     },
        //                     xhr: function() {
        //                         var xhr = new window.XMLHttpRequest();
        //                         // check if the video has been uploaded
        //                         if ($('#input-video-{{ $loop->iteration }}').val() ==
        //                             '') {
        //                             $('#effect-{{ $loop->iteration }}').hide('blind');
        //                             return xhr;
        //                         }
        //                         $('#effect-{{ $loop->iteration }}').show('blind');

        //                         xhr.upload.addEventListener("progress", function(evt) {
        //                             if (evt.lengthComputable) {
        //                                 var percentComplete = evt.loaded / evt
        //                                     .total;
        //                                 percentComplete = parseInt(percentComplete *
        //                                     100);
        //                                 var uploadedMB = (evt.loaded / 1024 / 1024)
        //                                     .toFixed(2); // Convert bytes to MB
        //                                 var totalMB = (evt.total / 1024 / 1024)
        //                                     .toFixed(
        //                                         2); // Convert bytes to MB
        //                                 var elapsedTime = (Date.now() - startTime) /
        //                                     1000; // Calculate elapsed time in seconds
        //                                 var speedMbps = ((evt.loaded /
        //                                         elapsedTime) /
        //                                     1024 / 1024 * 8).toFixed(
        //                                     2); // Speed in Mbps
        //                                 $('#progressBar-{{ $loop->iteration }}')
        //                                     .width(percentComplete +
        //                                         '%');
        //                                 $('#progressText-{{ $loop->iteration }}')
        //                                     .html(
        //                                         percentComplete + '%'
        //                                     )
        //                                 $('#status-{{ $loop->iteration }} p')
        //                                     .html(
        //                                         `(${uploadedMB}MB of ${totalMB}MB)`
        //                                     );

        //                             }
        //                         }, false);
        //                         return xhr;
        //                     },
        //                     success: function(response) {
        //                         // Handle success
        //                         console.log('Success:', response);
        //                         $('#status-{{ $loop->iteration }} p').html(response
        //                             .message);
        //                         // reload page
        //                         location.reload();
        //                     },
        //                     error: function(xhr, status, error) {
        //                         // Handle error
        //                         console.log('Error:', error);
        //                         $('#status-{{ $loop->iteration }} p').html(
        //                             "Error uploading file.");
        //                     }
        //                 });

        //             }
        //         });

        //         // cancel upload
        //         $('#cancelUpload-{{ $loop->iteration }}').click(function() {
        //             if (activeUploadRequest{{ $loop->iteration }}) {
        //                 activeUploadRequest{{ $loop->iteration }}.abort(); // Abort the active request
        //                 activeUploadRequest{{ $loop->iteration }} = null; // Reset the variable
        //             }
        //             $("#effect-{{ $loop->iteration }}").hide('blind');
        //             $('#progressBar-{{ $loop->iteration }}').width('0%');
        //             $('#progressText-{{ $loop->iteration }}').html('0%');
        //             $('#status-{{ $loop->iteration }} p').html('');
        //         });
        //     @endforeach

        // });
    </script>

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


        // when upload video show the video preview
        // @foreach ($section->lectures as $lecture)
        //     $('#input-video-{{ $loop->iteration }}').change(function() {
        //         $('#showVideo-{{ $loop->iteration }}').show('blind');
        //         var reader = new FileReader();
        //         reader.onload = function(e) {
        //             $('#showVideo-{{ $loop->iteration }} video').attr('src', e.target
        //                 .result);
        //         }
        //         reader.readAsDataURL(this.files[0]);
        //     });

        //     // when upload thumbnail show the thumbnail preview
        //     $('#input-thumbnail-{{ $loop->iteration }}').change(function() {
        //         $('#showThumbnail-{{ $loop->iteration }}').show('blind');
        //         var reader = new FileReader();
        //         reader.onload = function(e) {
        //             $('#showThumbnail-{{ $loop->iteration }} img').attr('src', e.target
        //                 .result);
        //         }
        //         reader.readAsDataURL(this.files[0]);
        //     });

        //     // when upload attachments show the attachments preview
        //     $('#input-attachments-{{ $loop->iteration }}').change(function() {
        //         console.log('attachments');
        //         $('#showAttachments-{{ $loop->iteration }}').show('blind');
        //         // remove old preview
        //         $('#showAttachments-{{ $loop->iteration }}').empty();
        //         // get the files
        //         var files = this.files;
        //         for (var i = 0; i < files.length; i++) {
        //             var file = files[i];
        //             var reader = new FileReader();
        //             reader.onload = function(e) {
        //                 var preview = document.createElement('div');
        //                 preview.classList.add('col-md-6');
        //                 preview.classList.add('col-12');
        //                 preview.classList.add('my-3');
        //                 let type = e.target.result.split(':')[1].split('/')[0];
        //                 if (type == 'image') {
        //                     preview.innerHTML = '<img class="img-thumbnail img-fluid" src="' + e.target.result +
        //                         '"/>';
        //                 } else if (type == 'video') {
        //                     preview.innerHTML = '<video style="width: 100%; height: 100%;" controls src="' + e
        //                         .target.result + '" >';
        //                 } else if (type == 'audio') {
        //                     preview.innerHTML =
        //                         '<audio style="width: 100%; height: 100%;" controls class="audio"  src="' + e
        //                         .target
        //                         .result + '" >';
        //                 } else if (type == 'application') {
        //                     preview.innerHTML = '<iframe style="width: 100%; height: 100%;" src="' + e.target
        //                         .result + '" >' + '</iframe>';
        //                 } else {
        //                     console.log(e.target);
        //                     preview.innerHTML = '<a class="text-primary" href="' + e.target.result +
        //                         '" target="_blank">View attachment</a>';
        //                 }
        //                 $('#showAttachments-{{ $loop->iteration }}').append(preview);
        //             }
        //             reader.readAsDataURL(file);
        //         }

        //     });
        // @endforeach
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
