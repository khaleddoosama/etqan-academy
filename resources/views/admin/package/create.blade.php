@extends('admin.master')
@section('title')
{{ __('buttons.create_package') }}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('asset/admin/plugins/tags/tagsinput.css') }}">
@endsection
@section('content')
<div class="content-wrapper">

    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ __('buttons.create_package') }}" />

    <!-- Main content -->
    <section class=" content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- jquery validation -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('buttons.create') }}<small>
                                    {{ __('attributes.package') }}</small></h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="form1" action="{{ route('admin.packages.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body row">
                                <div class="col-md-2">
                                    <img src="#" alt="" class="img-logo d-none"
                                        id="program-logo" style="width: 64px; height: 64px">
                                </div>

                                <x-custom.form-group class="col-md-4" type="text" name="title" />
                                <x-custom.form-group class="col-md-6" type="text" name="description" />
                                <!-- End Row -->
                                
                                <div class="col-md-2">
                                </div>
                                <x-custom.form-group class="col-md-5" type="text" name="meaning_description" />

                                <x-custom.form-group class="col-md-5" type="file" name="logo" />


                                <div class="col-md-2">
                                </div>
                                <div class="form-group row col-md-5">
                                    <x-input-label for="input-features"
                                        class="col-sm-12 col-form-label">{{ __('attributes.features') }}</x-input-label>
                                    <div class="col-sm-12" id="inputWrapper">
                                        <x-text-input type="text" id="input-features" name="features" data-role="tagsinput"
                                            value="{{ old('features')}}" class="form-control" />
                                    </div>
                                    <x-input-error :messages="$errors->get('features')" style="padding: 0 7.5px;margin: 0;" />
                                </div>

                                <x-custom.form-group class="col-md-5" type="select" name="programs[]" :options="$programs"
                                    multiple="true" />

                                {{-- plans --}}
                                <div class="col-md-12">
                                    <h4>{{ __('attributes.plans') }}</h4>
                                    <div class="row" id="plans">
                                        <div class="col-md-12 mb-3" style="border-bottom: 2px solid #dee2e6;">
                                            <div class="align-items-end row position-relative">
                                                <div class="col-md-2">
                                                    <img src="#" alt="" class="img-logo d-none"
                                                        id="program-logo-0" style="width: 64px; height: 64px">
                                                </div>
                                                <x-custom.form-group class="col-md-2" type="text"
                                                    name="plans[0][title]" />

                                                <x-custom.form-group class="col-md-2" type="text"
                                                    name="plans[0][from]" />

                                                <x-custom.form-group class="col-md-2" type="number"
                                                    name="plans[0][price]" />

                                                <x-custom.form-group class="col-md-2" type="number"
                                                    name="plans[0][duration]" />


                                                <x-custom.form-group class="col-md-2" type="number"
                                                    name="plans[0][device_limit]" />


                                                <!-- End Row -->
                                                <div class="form-group col-md-1">
                                                    <x-input-label for="input-plans[0][has_ai_access]"
                                                        class="col-sm-12 col-form-label">{{ __('attributes.has_ai_access') }}</x-input-label>
                                                    <div class="col-sm-12">
                                                        <input id="input-plans[0][has_ai_access]" type="checkbox" name="plans[0][has_ai_access]" checked data-bootstrap-switch data-off-color="danger"
                                                            data-on-color="success">
                                                    </div>
                                                    <x-input-error :messages="$errors->get('plans[0][has_ai_access]')" style="padding: 0 7.5px;margin: 0;" />
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <x-input-label for="input-plans[0][has_flaticon_access]"
                                                        class="col-sm-12 col-form-label">{{ __('attributes.has_flaticon_access') }}</x-input-label>
                                                    <div class="col-sm-12">
                                                        <input id="input-plans[0][has_flaticon_access]" type="checkbox" name="plans[0][has_flaticon_access]" checked data-bootstrap-switch data-off-color="danger"
                                                            data-on-color="success">
                                                    </div>
                                                    <x-input-error :messages="$errors->get('plans[0][has_flaticon_access]')" style="padding: 0 7.5px;margin: 0;" />
                                                </div>


                                                <x-custom.form-group class="col-md-2" type="number"
                                                    name="plans[0][number_of_downloads]" />


                                                <x-custom.form-group class="col-md-4" type="text"
                                                    name="plans[0][description]" />

                                                <x-custom.form-group class="col-md-4" type="file" name="plans[0][logo]" />
                                                <!-- End Row -->

                                                <div class="col-md-2">
                                                </div>
                                                <x-custom.form-group class="col-md-6" type="select" name="plans[0][programs][]" :options="$programs"
                                                    multiple="true" />

                                                <!-- plus button -->
                                                <div class="mb-3 position-absolute end-0" style="margin-right: -5px">
                                                    <button type="button" class="btn btn-primary "
                                                        onclick="addSection()">+</button>
                                                </div>
                                                <!-- End Row -->
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <x-primary-button
                                    class="btn btn-primary"><b>{{ __('buttons.add') }}</b></x-primary-button>
                            </div>
                        </form>
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
<script src="{{ asset('asset/admin/plugins/tags/tagsinput.js') }}"></script>
<!-- Page specific script -->
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

                    <x-custom.form-group class="col-md-2" type="text"
                        name="plans[${count}][title]" />

                    <x-custom.form-group class="col-md-2" type="text"
                        name="plans[${count}][from]" />

                    <x-custom.form-group class="col-md-2" type="number"
                        name="plans[${count}][price]" />

                    <x-custom.form-group class="col-md-2" type="number"
                        name="plans[${count}][duration]" />

                    <x-custom.form-group class="col-md-2" type="number"
                        name="plans[${count}][device_limit]" />

                    <div class="form-group col-md-1">
                        <x-input-label for="input-plans[${count}][has_ai_access]"
                            class="col-sm-12 col-form-label">{{ __('attributes.has_ai_access') }}</x-input-label>
                        <div class="col-sm-12">
                            <input id="input-plans[${count}][has_ai_access]" type="checkbox" name="plans[${count}][has_ai_access]" checked data-bootstrap-switch data-off-color="danger"
                                data-on-color="success">
                        </div>
                    </div>

                    <div class="form-group col-md-1">
                        <x-input-label for="input-plans[${count}][has_flaticon_access]"
                            class="col-sm-12 col-form-label">{{ __('attributes.has_flaticon_access') }}</x-input-label>
                        <div class="col-sm-12">
                            <input id="input-plans[${count}][has_flaticon_access]" type="checkbox" name="plans[${count}][has_flaticon_access]" checked data-bootstrap-switch data-off-color="danger"
                                data-on-color="success">
                        </div>
                    </div>

                    <x-custom.form-group class="col-md-2" type="number"
                        name="plans[${count}][number_of_downloads]" />

                    <x-custom.form-group class="col-md-4" type="text"
                        name="plans[${count}][description]" />

                    <x-custom.form-group class="col-md-4" type="file"
                        name="plans[${count}][logo]" />

                    <div class="col-md-2"></div>
                    <x-custom.form-group class="col-md-6" type="select"
                        name="plans[${count}][programs][]" :options="$programs"
                        multiple="true" />

                    <!-- subtract button -->
                    <div class="mb-3 position-absolute end-0"
                            style="margin-right: -5px">
                        <button type="button" class="btn btn-danger" style="padding: 6px 14px;"
                            onclick="removeSection(this)">-</button>
                    </div>
                </div>
            </div>
        `);

        // initialize multi-select for new select
        $('#plans').find(`select[name="plans[${count}][programs][]"]`).select2({
            placeholder: 'Select programs',
            allowClear: true
        });

        // initialize bootstrap switches
        $(`input[data-bootstrap-switch]`).bootstrapSwitch('state', true);
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
