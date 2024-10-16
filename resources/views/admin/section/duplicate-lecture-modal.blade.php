<div class="modal fade" id="getVideoModal" tabindex="-1" role="dialog" aria-labelledby="getVideoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.lectures.duplicate') }}" method="POST" id="form2"
                enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="createVideoModalLabel">
                        {{ __('main.duplicate_lecture') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="section_id" value="{{ $section->id }}">


                    <div class='form-group row'>
                        <x-input-label for="input-get-course"
                            class="col-sm-12 col-form-label">{{ __('attributes.course') }}</x-input-label>
                        <div class="col-sm-12" style="font-weight: 200">
                            <select class="form-control select2" style="width: 100%;"
                                id="input-get-course">
                                <option selected="selected" disabled>
                                    {{ __('buttons.choose') }}</option>
                                @foreach ($courses as $option)
                                    <option value="{{ $option->id }}">
                                        {{ $option->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('course_id')" style="padding: 0 7.5px;margin: 0;" />
                    </div>

                    <div class='form-group row' style="display: none;" id="div-get-setion">
                        <x-input-label for="input-get-section"
                            class="col-sm-12 col-form-label">{{ __('attributes.section') }}</x-input-label>
                        <div class="col-sm-12" style="font-weight: 200">
                            <select class="form-control select2" style="width: 100%;"
                                id="input-get-section">
                                <option selected="selected" disabled>
                                    {{ __('buttons.choose') }}</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('section_id')" style="padding: 0 7.5px;margin: 0;" />
                    </div>

                    <div class='form-group row' style="display: none;" id="div-get-lecture">
                        <x-input-label for="input-get-lecture"
                            class="col-sm-12 col-form-label">{{ __('attributes.lecture') }}</x-input-label>
                        <div class="col-sm-12" style="font-weight: 200">
                            <select class="form-control select2" style="width: 100%;" name="lecture_id"
                                id="input-get-lecture">
                                <option selected="selected" disabled>
                                    {{ __('buttons.choose') }}</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('lecture_id')" style="padding: 0 7.5px;margin: 0;" />
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
