{{-- Filters --}}
<div class="row mb-3">
    <div class="col-md-3">
        <select id="filter-user" class="form-control select2">
            <option value="">{{ __('main.all_users') }}</option>
            @foreach($users as $user)
            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                {{ $user->id }}- {{ $user->name }} - {{ $user->email }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <select id="filter-gateway" class="form-control">
            <option value="">All Gateways</option>
            @foreach($gateways as $key => $gateway)
            <option value="{{ $key }}" {{ request('gateway') == $key ? 'selected' : '' }}>
                {{ $gateway }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <select id="filter-status" class="form-control">
            <option value="">{{ __('main.all_statuses') }}</option>
            @foreach($paymentStatuses as $status)
            <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                {{ ucfirst($status->value) }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <select id="filter-coupon" class="form-control">
            <option value="">{{ __('main.all_coupons') }}</option>
            @foreach($coupons as $coupon)
            <option value="{{ $coupon->id }}" {{ request('coupon_id') == $coupon->id ? 'selected' : '' }}>
                {{ $coupon->code }}
            </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label for="filter-from-created" class="form-label small text-muted mb-1">{{ __('attributes.created_at') }}
            ({{ __('attributes.from_date') }})</label>
        <input type="date"
            id="filter-from-created"
            class="form-control"
            value="{{ request('from_created_at') }}">
    </div>
    <div class="col-md-3">
        <label for="filter-to-created" class="form-label small text-muted mb-1">{{ __('attributes.created_at') }}
            ({{ __('attributes.to_date') }})</label>
        <input type="date"
            id="filter-to-created"
            class="form-control"
            value="{{ request('to_created_at') }}">
    </div>
    <div class="col-md-3">
        <label for="filter-from-paid" class="form-label small text-muted mb-1">{{ __('attributes.paid_at') }}
            ({{ __('attributes.from_date') }})</label>
        <input type="date"
            id="filter-from-paid"
            class="form-control"
            value="{{ request('from_paid_at', now()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label for="filter-to-paid" class="form-label small text-muted mb-1">{{ __('attributes.paid_at') }}
            ({{ __('attributes.to_date') }})</label>
        <input type="date"
            id="filter-to-paid"
            class="form-control"
            value="{{ request('to_paid_at') }}">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <button id="reset-filters" class="btn btn-secondary" type="button">
            {{ __('buttons.clear_filters') }}
        </button>
    </div>
</div>
