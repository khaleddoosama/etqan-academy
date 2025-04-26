@extends('admin.master')
@section('title')
{{ __('buttons.create_package') }}
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
                                <x-custom.form-group class="col-md-6" type="text" name="title" />
                                <x-custom.form-group class="col-md-6" type="text" name="description" />
                                <x-custom.form-group class="col-md-6" type="select" name="programs[]" :options="$programs"
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
                                                    name="plans[0][from]" />

                                                <x-custom.form-group class="col-md-2" type="number"
                                                    name="plans[0][price]" />

                                                <x-custom.form-group class="col-md-2" type="number"
                                                    name="plans[0][duration]" />

                                                <x-custom.form-group class="col-md-2" type="number"
                                                    name="plans[0][device_limit]" />

                                                <x-custom.form-group class="col-md" type="file" name="plans[0][logo]" />

                                                <x-custom.form-group class="col-md-4" type="text"
                                                    name="plans[0][description]" />

                                                <x-custom.form-group class="col-md-8" type="select" name="plans[0][programs][]" :options="$programs"
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
<!-- Page specific script -->
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
