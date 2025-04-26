@extends('admin.master')

@section('title')
{{ __('buttons.edit_package') }}
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
                                <x-custom.form-group class="col-md-6" type="text" name="title" :value="$package->title" />
                                <x-custom.form-group class="col-md-6" type="text" name="description" :value="$package->description" />
                                <x-custom.form-group
                                    class="col-md-6"
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

                                                <x-custom.form-group class="col-md-2" type="text" name="plans[{{ $index }}][from]" :value="$plan->from" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ $index }}][price]" :value="$plan->price" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ $index }}][duration]" :value="$plan->duration" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ $index }}][device_limit]" :value="$plan->device_limit" />
                                                <x-custom.form-group class="col-md" type="file" name="plans[{{ $index }}][logo]" />

                                                <x-custom.form-group class="col-md-4" type="text" name="plans[{{ $index }}][description]" :value="$plan->description" />

                                                <x-custom.form-group
                                                    class="col-md-8"
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

                                                <x-custom.form-group class="col-md-2" type="text" name="plans[{{ ($index ?? -1) + 1 }}][from]" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ ($index ?? -1) + 1 }}][price]" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ ($index ?? -1) + 1 }}][duration]" />
                                                <x-custom.form-group class="col-md-2" type="number" name="plans[{{ ($index ?? -1) + 1 }}][device_limit]" />
                                                <x-custom.form-group class="col-md" type="file" name="plans[{{ ($index ?? -1) + 1 }}][logo]" />

                                                <x-custom.form-group class="col-md-4" type="text" name="plans[{{ ($index ?? -1) + 1 }}][description]" />

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
<script>
    // when user selects a file #exampleInputFile must be show in img with id = #profilePicture
    $(document).ready(function() {

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

        // add section
        $('#plans').append(`
            <div class="col-md-12 mb-3" style="border-bottom: 2px solid #dee2e6;">
                <div class="row align-items-end position-relative">
                    <div class="col-md-2">
                        <img src="#" alt="" class="img-logo d-none"
                            id="program-logo-${count}" style="width: 64px; height: 64px">
                    </div>
                   <x-custom.form-group class="col-md-2" type="text"
                        name="plans[${count}][from]" />
                    <x-custom.form-group class="col-md-2" type="number"
                        name="plans[${count}][price]" />
                    <x-custom.form-group class="col-md-2" type="number"
                        name="plans[${count}][duration]" />
                    <x-custom.form-group class="col-md-2" type="number"
                        name="plans[${count}][device_limit]" />
                    <x-custom.form-group class="col-md" type="file" name="plans[${count}][logo]" />

                    <x-custom.form-group class="col-md-4" type="text"
                        name="plans[${count}][description]" />
                    <x-custom.form-group class="col-md-8" type="select" name="plans[${count}][programs][]" :options="$programs"
                        multiple="true" />


                    {{-- subtract button --}}
                    <div class="mb-3 position-absolute end-0"
                            style="margin-right: -5px">
                        <button type="button" class=" btn btn-danger" style="padding: 6px 14px;"
                            onclick="removeSection(this)">-</button>
                    </div>
                </div>
            </div>
        `);

        // multi select
        $('#plans').find(`select[name="plans[${count}][programs][]"]`).select2({
            placeholder: 'Select programs',
            allowClear: true
        });
    }

    function removeSection(e) {
        e.closest('.col-md-12').remove();
    }
</script>
@endsection
