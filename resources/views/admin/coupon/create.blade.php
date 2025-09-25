@extends('admin.master')
@section('title')
{{ __('buttons.create_coupon') }}
@endsection
@section('content')
<div class="content-wrapper">

    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ __('buttons.create_coupon') }}" />

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
                                    {{ __('attributes.coupon') }}</small></h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="form1" action="{{ route('admin.coupons.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body row">
                                <x-custom.form-group class="col-md-4" type="text" name="code" />
                                <x-custom.form-group class="col-md-4" type="number" name="discount" />
                                <x-custom.form-group class="col-md-4" type="number" name="usage_limit" title="If you leave it blank it will be unlimited." data-bs-toggle="tooltip" />

                                <x-custom.form-group class="col-md-4" type="number" name="access_duration_days" title="Days of access when using this coupon (leave empty for unlimited)." data-bs-toggle="tooltip" />


                                @php
                                $types = collect([
                                [
                                'id' => 'percentage',
                                'title' => __('attributes.percentage'),
                                'name' => '',
                                ],
                                [
                                'id' => 'fixed',
                                'title' => __('attributes.fixed'),
                                'name' => '',
                                ],
                                ])->map(function ($item) {
                                return (object) $item;
                                });
                                @endphp

                                <x-custom.form-group class="col-md-4" type="select" name="type" :options="$types" />


                                <x-custom.form-group class="col-md-4" type="date" name="start_at" value="{{ now()->format('Y-m-d') }}" />
                                <x-custom.form-group class="col-md-4" type="date" name="expires_at" />
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
    $(function() {

        $('#form1').validate({
            rules: {
                code: {
                    required: true,
                },
                discount: {
                    required: true,
                },
                type: {
                    required: true,
                },
                start_at: {
                    required: true,
                    // type date
                    date: true,
                },
                expires_at: {
                    required: true,
                    date: true,
                    min: function() {
                        return $("#input-start_at").val();
                    }
                },
            },
            messages: {
                code: {
                    required: "{{ __('validation.required', ['attribute' => __('attributes.name')]) }}"
                },
                discount: {
                    required: "{{ __('validation.required', ['attribute' => __('attributes.discount')]) }}"
                },
                type: {
                    required: "{{ __('validation.required', ['attribute' => __('attributes.type')]) }}"
                },
                start_at: {
                    required: "{{ __('validation.required', ['attribute' => __('attributes.start_at')]) }}",
                    date: "{{ __('validation.date', ['attribute' => __('attributes.start_at')]) }}"
                },
                expires_at: {
                    required: "{{ __('validation.required', ['attribute' => __('attributes.expires_at')]) }}",
                    date: "{{ __('validation.date', ['attribute' => __('attributes.expires_at')]) }}",
                    min: "{{ __('validation.after', ['attribute' => __('attributes.expires_at'), 'date' => __('attributes.start_at')]) }}"

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
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
