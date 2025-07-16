{{-- Filters --}}
<div class="row mb-3">
    <div class="col-md-2">
        <select id="filter-user" class="form-control">
            <option value="">{{ __('main.all_users') }}</option>
            @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->id }}- {{ $user->name }} - {{ $user->email }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select id="filter-gateway" class="form-control">
            <option value="">All Gateways</option>
            @foreach($gateways as $key => $gateway)
            <option value="{{ $key }}">{{ $gateway }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select id="filter-status" class="form-control">
            <option value="">{{ __('main.all_statuses') }}</option>
            @foreach($paymentStatuses as $status)
            <option value="{{ $status->value }}">
                {{ ucfirst($status->value) }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <input type="date" id="filter-from" class="form-control" placeholder="From date" value="{{ now()->format('Y-m-d') }}">
    </div>
    <div class="col-md-2">
        <input type="date" id="filter-to" class="form-control" placeholder="To date" >
    </div>
    <div class="col-md-2">
        <button id="reset-filters" class="btn btn-secondary">
            {{ __('buttons.clear_filters') }}
        </button>
    </div>
</div>
