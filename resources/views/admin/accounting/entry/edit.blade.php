@extends('admin.master')
@section('title')
    {{ __('buttons.edit_entry') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.edit_entry') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('buttons.edit') }} <small>{{ __('attributes.accounting_entry') }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="quickForm" action="{{ route('admin.accounting.entries.update', $entry->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body row">

                                    <x-custom.form-group class="col-md-6" type="text" name="title" :value="$entry->title" />

                                    <x-custom.form-group class="col-md-6" type="select" name="category_id" :options="$categories" :selected="$entry->category_id" />

                                    <x-custom.form-group class="col-md-6" type="number" name="amount" step="0.01" :value="$entry->amount" />

                                    <x-custom.form-group class="col-md-6" type="date" name="transaction_date" :value="$entry->transaction_date->format('Y-m-d')" />

                                    <x-custom.form-group class="col-md-12" type="textarea" name="description" rows="3" :value="$entry->description" />

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <x-primary-button class="btn btn-primary"><b>{{ __('buttons.update') }}</b></x-primary-button>
                                    <a href="{{ route('admin.accounting.entries.index') }}" class="btn btn-secondary ml-2">{{ __('buttons.cancel') }}</a>
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
            $('#quickForm').validate({
                rules: {
                    title: {
                        required: true,
                        minlength: 3
                    },
                    category_id: {
                        required: true,
                    },
                    amount: {
                        required: true,
                        number: true,
                        min: 0.01
                    },
                    transaction_date: {
                        required: true,
                        date: true
                    },
                    description: {
                        maxlength: 1000
                    }
                },
                messages: {
                    title: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.title')]) }}",
                        minlength: "{{ __('validation.min.string', ['attribute' => __('attributes.title'), 'min' => 3]) }}"
                    },
                    category_id: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.category')]) }}",
                    },
                    amount: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.amount')]) }}",
                        number: "{{ __('validation.numeric', ['attribute' => __('attributes.amount')]) }}",
                        min: "{{ __('validation.min.numeric', ['attribute' => __('attributes.amount'), 'min' => 0.01]) }}"
                    },
                    transaction_date: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.transaction_date')]) }}",
                        date: "{{ __('validation.date', ['attribute' => __('attributes.transaction_date')]) }}"
                    },
                    description: {
                        maxlength: "{{ __('validation.max.string', ['attribute' => __('attributes.description'), 'max' => 1000]) }}"
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
@endsection
