<div class="modal fade" id="createSectionModal" tabindex="-1" role="dialog" aria-labelledby="createSectionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.sections.store') }}" method="POST" id="form1"
                enctype="multipart/form-data">
                @csrf




                <div class="modal-header">
                    <h5 class="modal-title" id="createSectionModalLabel">
                        {{ __('buttons.add_section') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="parent_section_id" value="{{ $section->id }}" id="input-parent_section_id">
                    <input type="hidden" name="course_id" value="{{ $section->course_id }}" id="input-course_id">

                    <x-custom.form-group type="text" name="title" />

                </div>

                <div class="modal-footer">
                    <x-custom.close-modal-button />
                    <x-custom.form-submit text="{{ __('buttons.save') }}" class=" btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div>
