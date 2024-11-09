<div class="modal fade" id="createVideoModal" tabindex="-1" role="dialog" aria-labelledby="createVideoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.lectures.store') }}" method="POST" id="form1"
                enctype="multipart/form-data">
                @csrf




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


                    <x-custom.form-group type="text" name="video" placeholder="Video ID From YouTube"/>

                    {{-- show video --}}
                    <iframe src="" id="showVideo" width="100%" style="display: none" frameborder="0" allowfullscreen></iframe>

                    {{-- duration --}}
                    <x-custom.form-group type="number" name="hours" value="0" class="col-md-4 d-inline-block" />

                    <x-custom.form-group type="number" name="minutes" value="0" class="col-md-4 d-inline-block" />

                    <x-custom.form-group type="number" name="seconds" value="0" class="col-md-4 d-inline-block" />

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
