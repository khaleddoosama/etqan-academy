<div class="modal fade" id="createVideoModal" tabindex="-1" role="dialog" aria-labelledby="createVideoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.lectures.store') }}" method="POST" id="form1"
                enctype="multipart/form-data">
                @csrf
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



                <div class="modal-header">
                    <h5 class="modal-title" id="createVideoModalLabel">
                        {{ __('buttons.add_video') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="section_id" value="{{ $section->id }}" id="input-section_id">

                    <x-custom.form-group type="text" name="title" />


                    <div class='form-group row'>
                        <x-input-label for="input-video"
                            class="col-sm-12 col-form-label">{{ __('attributes.video') }}</x-input-label>

                        <div class="input-group col-sm-12">
                            <div class="custom-file">
                                <input type="file" name="video" id="input-video" class="custom-file-input"
                                    accept="video/*">
                                <x-input-label for="input-video" class="custom-file-label col-form-label"
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
                        <img src="" alt="" id="thumbnail" class="img-thumbnail" style="height: 240px">
                    </div>

                    <div class='row form-group col-md-12'>
                        <x-input-label for="summernote"
                            class='col-sm-12 col-form-label'>{{ __('attributes.description') }}</x-input-label>

                        <div class='col-sm-12'>
                            <textarea name="description" id="summernote" class="form-control summernote" rows="1">{{ old('description') ?? '' }}</textarea>
                        </div>
                    </div>

                    <x-custom.form-group type="file" name="attachments[]" :multiple="true" />

                    <div class="form-group row" style="display: none" id="showAttachments">

                    </div>
                </div>

                <div class="modal-footer">
                    <x-custom.close-modal-button />
                    <x-custom.form-submit text="{{ __('buttons.save') }}" class=" btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div>
