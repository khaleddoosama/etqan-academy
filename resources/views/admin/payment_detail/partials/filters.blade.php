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
        <input type="date"
            id="filter-from"
            class="form-control"
            placeholder="From date"
            value="{{ request('from_paid_at', now()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <input type="date"
            id="filter-to"
            class="form-control"
            placeholder="To date"
            value="{{ request('to_paid_at') }}">
    </div>
    <div class="col-md-3">
        <button id="reset-filters" class="btn btn-secondary">
            {{ __('buttons.clear_filters') }}
        </button>
    </div>
</div>
