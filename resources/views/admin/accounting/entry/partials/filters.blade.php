{{-- Filters --}}
<div class="row mb-3">
    <div class="col-md">
        <select id="filter-category" class="form-control">
            <option value="">{{ __('main.all_categories') }}</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-md">
        <select id="filter-type" class="form-control">
            <option value="">All Types</option>
            @foreach($types as $type)
            <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>
                {{ ucfirst($type->value) }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-md">
        <input type="date"
            id="filter-from"
            class="form-control"
            placeholder="From date"
            value="{{ request('from_date') }}">
    </div>

    <div class="col-md">
        <input type="date"
            id="filter-to"
            class="form-control"
            placeholder="To date"
            value="{{ request('to_date') }}">
    </div>

    <div class="col-md">
        <button id="reset-filters" class="btn btn-secondary">
            {{ __('buttons.clear_filters') }}
        </button>
    </div>
</div>