@extends('admin.master')

@section('title')
{{ __('buttons.create') }} {{ __('attributes.payment_detail') }}
@endsection

@section('styles')
<style>
    .payment-item-card {
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }

    .payment-item-card .card-header {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <x-custom.header-page title="{{ __('buttons.create') }} {{ __('attributes.payment_detail') }}" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-credit-card"></i>
                                {{ __('buttons.create') }} {{ __('attributes.payment_detail') }}
                            </h3>
                        </div>
                        <form action="{{ route('admin.payment_details.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <p class="mb-2"><strong>{{ __('messages.something_went_wrong') }}</strong></p>
                                    <ul class="mb-0 pl-3">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                <input type="hidden" name="payment_method" value="instapay">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_id">{{ __('attributes.user') }}</label>
                                            <select name="user_id" id="user_id" class="form-control select2">
                                                <option value="">{{ __('buttons.select') }}</option>
                                                @foreach($users as $user)
                                                <option value="{{ $user['id'] }}" {{ old('user_id') == $user['id'] ? 'selected' : '' }}>
                                                    {{ $user['id'] }} - {{ $user['name'] }} ({{ $user['email'] }})
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="gateway">{{ __('attributes.gateway') }}</label>
                                            <select name="gateway" id="gateway" class="form-control">
                                                <option value="instapay" selected>Instapay</option>
                                            </select>
                                            @error('gateway')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">{{ __('attributes.status') }}</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">{{ __('buttons.select') }}</option>
                                                @foreach($paymentStatuses as $status)
                                                <option value="{{ $status->value }}" {{ old('status', 'paid') === $status->value ? 'selected' : '' }}>
                                                    {{ $status->label() }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <x-custom.form-group class="col-md-4" type="number" name="amount_before_coupon" value="{{ old('amount_before_coupon', 0) }}" />

                                    <x-custom.form-group class="col-md-4" type="number" name="amount_after_coupon" value="{{ old('amount_before_coupon', 0) }}" />

                                    <x-custom.form-group class="col-md-4" type="number" name="amount_confirmed" value="{{ old('amount_before_coupon') }}" />
                                </div>

                                <div class="row">
                                    <x-custom.form-group class="col-md-4" type="select" name="coupon_id" :options="$coupons" value="{{ old('coupon_id') }}" />

                                    <x-custom.form-group class="col-md-4" type="datetime-local" name="paid_at" value="{{ old('paid_at') }}" />

                                    <x-custom.form-group class="col-md-4" type="file" name="transfer_image" />
                                </div>

                                <hr>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h5 class="mb-0">{{ __('attributes.payment_items') }}</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-payment-item">
                                        <i class="fas fa-plus"></i> {{ __('buttons.add_item') }}
                                    </button>
                                </div>
                                <p class="text-muted small mb-3">
                                    {{ __('messages.payment_item_helper') }}
                                </p>

                                @php
                                $oldItems = old('items', [
                                ['payment_type' => '', 'course_id' => null, 'course_installment_id' => null, 'package_plan_id' => null, 'amount' => '']
                                ]);
                                @endphp

                                <div id="payment-items-wrapper">
                                    @foreach($oldItems as $index => $item)
                                    <div class="payment-item mb-3" data-index="{{ $index }}">
                                        <div class="card payment-item-card">
                                            <div class="card-header d-flex justify-content-between align-items-center py-2">
                                                <span><i class="fas fa-list mr-1"></i> {{ __('attributes.payment_item') }} #<span class="item-sequence">{{ $loop->iteration }}</span></span>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-payment-item ml-auto" {{ $loop->count === 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-row">
                                                    <div class="form-group col-md-3">
                                                        <label>{{ __('attributes.payment_type') }}</label>
                                                        <select name="items[{{ $index }}][payment_type]" class="form-control">
                                                            <option value="cash" selected>Cash</option>
                                                        </select>
                                                        </select>
                                                        @error("items.$index.payment_type")
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>


                                                    <x-custom.form-group class="col-md-3" type="select" name="items[{{ $index }}][course_id]" :options="$courses" value="{{ old('items.' . $index . '.course_id') }}" />

                                                    <div class="form-group col-md-3">
                                                        <label>{{ __('attributes.package_plan') }}</label>
                                                        <select name="items[{{ $index }}][package_plan_id]" class="form-control">
                                                            <option value="">{{ __('buttons.select') }}</option>
                                                            @foreach($packagePlans as $plan)
                                                            <option value="{{ $plan->id }}" {{ ($item['package_plan_id'] ?? '') == $plan->id ? 'selected' : '' }}>
                                                                {{ optional($plan->package)->title ? optional($plan->package)->title . ' - ' : '' }}{{ $plan->title }} {{ $plan->from }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error("items.$index.package_plan_id")
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <x-custom.form-group class="col-md-3" type="number" name="items[{{ $index }}][amount]" value="{{ old('items.' . $index . '.amount') }}" />

                                                </div>
                                                <div class="form-row">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <template id="payment-item-template">
                                    <div class="payment-item mb-3" data-index="__INDEX__">
                                        <div class="card payment-item-card">
                                            <div class="card-header d-flex justify-content-between align-items-center py-2">
                                                <span><i class="fas fa-list mr-1"></i> {{ __('attributes.payment_item') }} #<span class="item-sequence"></span></span>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-payment-item ml-auto">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-row">
                                                    <div class="form-group col-md-3">
                                                        <label>{{ __('attributes.payment_type') }}</label>
                                                        <select name="items[__INDEX__][payment_type]" class="form-control">
                                                            <option value="cash" selected>Cash</option>
                                                        </select>
                                                    </div>

                                                    <x-custom.form-group class="col-md-3" type="select" name="items[__INDEX__][course_id]" :options="$courses" />

                                                    <div class="form-group col-md-3">
                                                        <label>{{ __('attributes.package_plan') }}</label>
                                                        <select name="items[__INDEX__][package_plan_id]" class="form-control">
                                                            <option value="">{{ __('buttons.select') }}</option>
                                                            @foreach($packagePlans as $plan)
                                                            <option value="{{ $plan->id }}">
                                                                {{ optional($plan->package)->title ? optional($plan->package)->title . ' - ' : '' }}{{ $plan->title }}{{ $plan->from }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <x-custom.form-group class="col-md-3" type="number" name="items[__INDEX__][amount]" />


                                                </div>
                                                <div class="form-row">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> {{ __('buttons.save') ?? __('buttons.submit') }}
                                </button>
                                <a href="{{ route('admin.payment_details.index') }}" class="btn btn-secondary ml-2">
                                    {{ __('buttons.cancel') }}
                                </a>
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
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $.fn.select2 === 'function') {
            $('.select2').select2();
        }

        const itemsWrapper = document.getElementById('payment-items-wrapper');
        const addItemButton = document.getElementById('add-payment-item');
        const template = document.getElementById('payment-item-template').innerHTML.trim();

        let itemIndex = itemsWrapper.querySelectorAll('.payment-item').length;

        function updateItemSequences() {
            itemsWrapper.querySelectorAll('.payment-item').forEach((item, idx) => {
                const sequence = item.querySelector('.item-sequence');
                if (sequence) {
                    sequence.textContent = idx + 1;
                }

                const removeButton = item.querySelector('.remove-payment-item');
                if (removeButton) {
                    removeButton.disabled = itemsWrapper.querySelectorAll('.payment-item').length === 1;
                }
            });
        }

        updateItemSequences();

        addItemButton.addEventListener('click', function() {
            const itemHtml = template.replace(/__INDEX__/g, itemIndex);
            itemsWrapper.insertAdjacentHTML('beforeend', itemHtml);
            itemIndex++;
            updateItemSequences();
        });

        itemsWrapper.addEventListener('click', function(event) {
            if (event.target.closest('.remove-payment-item')) {
                const items = itemsWrapper.querySelectorAll('.payment-item');
                if (items.length <= 1) {
                    return;
                }
                const item = event.target.closest('.payment-item');
                if (item) {
                    item.remove();
                    updateItemSequences();
                }
            }
        });
    });
</script>
@endsection
