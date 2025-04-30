@extends('admin.master')

@section('title')
{{ __('buttons.edit_package') }}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('asset/admin/plugins/tags/tagsinput.css') }}">
@endsection
@section('content')
<div class="content-wrapper">
    <x-custom.header-page title="{{ __('buttons.edit_package') }}" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('buttons.edit') }} <small>{{ __('attributes.package') }}</small></h3>
                        </div>

                        <form id="form1" action="{{ route('admin.packages.update', $package->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="card-body row">
                                <div class="col-md-2">
                                    <img src="{{ $package->logo_url }}" alt="" class="img-logo d-none"
                                        id="program-logo" style="width: 64px; height: 64px">
                                </div>
                                <x-custom.form-group class="col-md-5" type="text" name="title" :value="$package->title" />
                                <x-custom.form-group class="col-md-5" type="text" name="description" :value="$package->description" />
                                <!-- End Row -->

                                <div class="col-md-2">
                                </div>
                                <x-custom.form-group class="col-md-5" type="text" name="meaning_description" :value="$package->meaning_description" />

                                <x-custom.form-group class="col-md-5" type="file" name="logo" />
                                <!-- End Row -->


                                <div class="col-md-2">
                                </div>
                                <div class="form-group row col-md-5">
                                    <x-input-label for="input-features"
                                        class="col-sm-12 col-form-label">{{ __('attributes.features') }}</x-input-label>
                                    <div class="col-sm-12" id="inputWrapper">
                                        <x-text-input type="text" id="input-features" name="features" data-role="tagsinput"
                                            value="{{ implode(',', $package->features) }}" class="form-control" />
                                    </div>
                                    <x-input-error :messages="$errors->get('features')" style="padding: 0 7.5px;margin: 0;" />
                                </div>
                                <x-custom.form-group
                                    class="col-md-5"
                                    type="select"
                                    name="programs[]"
                                    :options="$programs"
                                    multiple="true"
                                    :selected="$package->programs" />

                                {{-- Plans --}}
                                <div class="col-md-12">
                                    <h4>{{ __('attributes.plans') }}</h4>
                                    <div class="row" id="plans">
                                        @foreach($package->packagePlans as $index => $plan)
                                        <div class="col-md-12 mb-3" style="border-bottom: 2px solid #dee2e6;">
                                            <div class="row align-items-end position-relative">
                                                <input type="hidden" name="plans[{{ $index }}][id]" value="{{ $plan->id }}">

                                                <div class="col-md-2">
                                                    <img src="{{ $plan->logo_url }}" alt="" class="img-logo {{ $plan->logo ? '' : 'd-none' }}" id="program-logo-{{ $index }}" style="width: 64px; height: 64px">
                                                </div>

                                                <x-custom.form-group class="col-md-2" type="text" name="plans[{{ $index }}][title]" :value="$plan->title" />
                                                <x-custom.form-group class="col-md-2" type="text" name="plans[{{ $index }}][from]" :value="$plan->from" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ $index }}][price]" :value="$plan->price" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ $index }}][duration]" :value="$plan->duration" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ $index }}][device_limit]" :value="$plan->device_limit" />
                                                <!-- End Row -->

                                                <div class="form-group col-md-1">
                                                    <x-input-label for="input-plans[{{ $index }}][has_ai_access]" class="col-sm-12 col-form-label">
                                                        {{ __('attributes.has_ai_access') }}
                                                    </x-input-label>
                                                    <div class="col-sm-12">
                                                        <input id="input-plans[{{ $index }}][has_ai_access]" type="checkbox" name="plans[{{ $index }}][has_ai_access]"
                                                            {{ $plan->has_ai_access ? 'checked' : '' }} data-bootstrap-switch data-off-color="danger"
                                                            data-on-color="success">
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <x-input-label for="input-plans[{{ $index }}][has_flaticon_access]" class="col-sm-12 col-form-label">
                                                        {{ __('attributes.has_flaticon_access') }}
                                                    </x-input-label>
                                                    <div class="col-sm-12">
                                                        <input id="input-plans[{{ $index }}][has_flaticon_access]" type="checkbox"
                                                            name="plans[{{ $index }}][has_flaticon_access]" {{ $plan->has_flaticon_access ? 'checked' : '' }}
                                                            data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    </div>
                                                </div>

                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ $index }}][number_of_downloads]" :value="$plan->number_of_downloads" />
                                                <x-custom.form-group class="col-md-4" type="text" name="plans[{{ $index }}][description]" :value="$plan->description" />
                                                <x-custom.form-group class="col-md-4" type="file" name="plans[{{ $index }}][logo]" />
                                                <!-- End Row -->

                                                <div class="col-md-2">
                                                </div>
                                                <x-custom.form-group
                                                    class="col-md-6"
                                                    type="select"
                                                    name="plans[{{ $index }}][programs][]"
                                                    :options="$programs"
                                                    multiple="true"
                                                    :selected="$plan->programs" />

                                                {{-- Remove Button --}}
                                                <div class="mb-3 position-absolute end-0" style="margin-right: -5px">
                                                    <button type="button" class="btn btn-danger" onclick="removeSection(this)">-</button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                        <div class="col-md-12 mb-3" style="border-bottom: 2px solid #dee2e6;">
                                            <div class="row align-items-end position-relative">
                                                <div class="col-md-2">
                                                    <img src="#" alt="" class="img-logo d-none" id="program-logo-{{ ($index ?? -1) + 1 }}" style="width: 64px; height: 64px">
                                                </div>
                                                <x-custom.form-group class="col-md-2" type="text" name="plans[{{ ($index ?? -1) + 1 }}][title]" />
                                                <x-custom.form-group class="col-md-2" type="text" name="plans[{{ ($index ?? -1) + 1 }}][from]" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ ($index ?? -1) + 1 }}][price]" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ ($index ?? -1) + 1 }}][duration]" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ ($index ?? -1) + 1 }}][device_limit]" />
                                                <!-- End Row -->

                                                <div class="form-group col-md-1">
                                                    <x-input-label for="input-plans[{{ ($index ?? -1) + 1 }}][has_ai_access]" class="col-sm-12 col-form-label">
                                                        {{ __('attributes.has_ai_access') }}
                                                    </x-input-label>
                                                    <div class="col-sm-12">
                                                        <input id="input-plans[{{ ($index ?? -1) + 1 }}][has_ai_access]" type="checkbox"
                                                            name="plans[{{ ($index ?? -1) + 1 }}][has_ai_access]" checked data-bootstrap-switch
                                                            data-off-color="danger" data-on-color="success">
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <x-input-label for="input-plans[{{ ($index ?? -1) + 1 }}][has_flaticon_access]" class="col-sm-12 col-form-label">
                                                        {{ __('attributes.has_flaticon_access') }}
                                                    </x-input-label>
                                                    <div class="col-sm-12">
                                                        <input id="input-plans[{{ ($index ?? -1) + 1 }}][has_flaticon_access]" type="checkbox"
                                                            name="plans[{{ ($index ?? -1) + 1 }}][has_flaticon_access]" checked data-bootstrap-switch
                                                            data-off-color="danger" data-on-color="success">
                                                    </div>
                                                </div>

                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ ($index ?? -1) + 1 }}][number_of_downloads]" />

                                                <x-custom.form-group class="col-md-4" type="text" name="plans[{{ ($index ?? -1) + 1 }}][description]" />

                                                <x-custom.form-group class="col-md-4" type="file" name="plans[{{ ($index ?? -1) + 1 }}][logo]" />
                                                <!-- End Row -->

                                                <div class="col-md-2">
                                                </div>
                                                <x-custom.form-group
                                                    class="col-md-8"
                                                    type="select"
                                                    name="plans[{{ ($index ?? -1) + 1 }}][programs][]"
                                                    :options="$programs"
                                                    multiple="true" />

                                                <!-- plus button -->
                                                <div class="mb-3 position-absolute end-0" style="margin-right: -5px">
                                                    <button type="button" class="btn btn-primary "
                                                        onclick="addSection()">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <x-primary-button class="btn btn-primary">
                                    <b>{{ __('buttons.update') }}</b>
                                </x-primary-button>
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
<script src="{{ asset('asset/admin/plugins/tags/tagsinput.js') }}"></script>

<script>
    // when user selects a file #exampleInputFile must be show in img with id = #profilePicture
    $(document).ready(function() {
        $('#input-logo').on('change', function(event) {
            const input = event.target;

            const img = $(`#program-logo`);

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
        //input-plans[0][logo]
        $('#plans').on('change', 'input[type="file"]', function(event) {
            const input = event.target;

            const img = $(`#program-logo-${$(input).attr('name').split('[')[1].split(']')[0]}`);

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
        let count = $('#plans').children().length;
        console.log(count);

        $('#plans').append(`
        <div class="col-md-12 mb-3" style="border-bottom: 2px solid #dee2e6;">
            <div class="row align-items-end position-relative">
                <div class="col-md-2">
                    <img src="#" alt="" class="img-logo d-none"
                        id="program-logo-${count}" style="width: 64px; height: 64px">
                </div>

                <x-custom.form-group class="col-md-2" type="text" name="plans[${count}][title]" />
                <x-custom.form-group class="col-md-2" type="text" name="plans[${count}][from]" />
                <x-custom.form-group class="col-md-2" type="number" name="plans[${count}][price]" />
                <x-custom.form-group class="col-md-2" type="number" name="plans[${count}][duration]" />
                <x-custom.form-group class="col-md-2" type="number" name="plans[${count}][device_limit]" />

                <div class="form-group col-md-1">
                    <x-input-label for="input-plans[${count}][has_ai_access]" class="col-sm-12 col-form-label">
                        {{ __('attributes.has_ai_access') }}
                    </x-input-label>
                    <div class="col-sm-12">
                        <input id="input-plans[${count}][has_ai_access]" type="checkbox"
                            name="plans[${count}][has_ai_access]" checked data-bootstrap-switch
                            data-off-color="danger" data-on-color="success">
                    </div>
                </div>

                <div class="form-group col-md-1">
                    <x-input-label for="input-plans[${count}][has_flaticon_access]" class="col-sm-12 col-form-label">
                        {{ __('attributes.has_flaticon_access') }}
                    </x-input-label>
                    <div class="col-sm-12">
                        <input id="input-plans[${count}][has_flaticon_access]" type="checkbox"
                            name="plans[${count}][has_flaticon_access]" checked data-bootstrap-switch
                            data-off-color="danger" data-on-color="success">
                    </div>
                </div>

                <x-custom.form-group class="col-md-2" type="number" name="plans[${count}][number_of_downloads]" />
                <x-custom.form-group class="col-md-4" type="text" name="plans[${count}][description]" />
                <x-custom.form-group class="col-md-4" type="file" name="plans[${count}][logo]" />

                <div class="col-md-2"></div>

                <x-custom.form-group class="col-md-8" type="select"
                    name="plans[${count}][programs][]"
                    :options="$programs"
                    multiple="true" />

                <div class="mb-3 position-absolute end-0" style="margin-right: -5px">
                    <button type="button" class="btn btn-danger" style="padding: 6px 14px;" onclick="removeSection(this)">-</button>
                </div>
            </div>
        </div>
    `);

        // multi select
        $('#plans').find(`select[name="plans[${count}][programs][]"]`).select2({
            placeholder: 'Select programs',
            allowClear: true
        });

        // Initialize Bootstrap Switch for new checkboxes
        $('[data-bootstrap-switch]').each(function() {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
    }


    function removeSection(e) {
        e.closest('.col-md-12').remove();
    }
</script>

<script>
    $(document).ready(function() {
        $('input[data-role="tagsinput"]').tagsinput();
    });
</script>
@endsection
