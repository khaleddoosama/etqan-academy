@extends('admin.master')

@section('title')
{{ __('buttons.edit_coupon') }}
@endsection

@section('content')
<div class="content-wrapper">

    <!-- Content Header -->
    <x-custom.header-page title="{{ __('buttons.edit_coupon') }}" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('buttons.edit') }} <small>{{ __('attributes.coupon') }}</small></h3>
                        </div>

                        <!-- Form start -->
                        <form id="form1" action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="card-body row">
                                <x-custom.form-group class="col-md-4" type="text" name="code" :value="$coupon->code" />
                                <x-custom.form-group class="col-md-4" type="number" name="discount" :value="$coupon->discount" />
                                <x-custom.form-group class="col-md-4" type="number" name="usage_limit" :value="$coupon->usage_limit" title="If you leave it blank it will be unlimited." data-bs-toggle="tooltip" />

                                <x-custom.form-group class="col-md-4" type="number" name="access_duration_days" :value="$coupon->access_duration_days" title="Days of access when using this coupon (leave empty for unlimited)." data-bs-toggle="tooltip" />

                                @php
                                $types = collect([
                                ['id' => 'percentage', 'title' => __('attributes.percentage'), 'name' => ''],
                                ['id' => 'fixed', 'title' => __('attributes.fixed'), 'name' => ''],
                                ])->map(fn($item) => (object) $item);
                                @endphp

                                <x-custom.form-group class="col-md-4" type="select" name="type" :options="$types"  :selected="$coupon->type" />

                                <x-custom.form-group class="col-md-4" type="date" name="start_at" :value="$coupon->start_at->format('Y-m-d')" />
                                <x-custom.form-group class="col-md-4" type="date" name="expires_at" :value="optional($coupon->expires_at)->format('Y-m-d')" />
                            </div>

                            <!-- Footer -->
                            <div class="card-footer">
                                <x-primary-button class="btn btn-primary"><b>{{ __('buttons.update') }}</b></x-primary-button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        $('#form1').validate({
            rules: {
                code: {
                    required: true
                },
                discount: {
                    required: true
                },
                type: {
                    required: true
                },
                start_at: {
                    required: true,
                    date: true
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
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
