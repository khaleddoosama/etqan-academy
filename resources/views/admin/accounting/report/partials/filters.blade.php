{{-- Report Filters Section --}}
<div class="filter-section">
    <div class="row">
        <div class="col-md-3">
            <label>{{ __('attributes.from_date') }}</label>
            <input type="date" id="filter-from-date" class="form-control" value="{{ $fromDate }}">
        </div>
        <div class="col-md-3">
            <label>{{ __('attributes.to_date') }}</label>
            <input type="date" id="filter-to-date" class="form-control" value="{{ $toDate }}">
        </div>
        <div class="col-md-3">
            <label>{{ __('attributes.category') }}</label>
            <select id="filter-category" class="form-control">
                <option value="">{{ __('main.all_categories') }}</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type?->value }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>{{ __('attributes.type') }}</label>
            <select id="filter-type" class="form-control">
                <option value="">{{ __('main.all_types') }}</option>
                @foreach($types as $type)
                <option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <button id="apply-filters" class="btn btn-primary">
                <i class="fas fa-search"></i> {{ __('buttons.apply_filters') }}
            </button> <button id="reset-filters" class="btn btn-secondary ml-2">
                <i class="fas fa-times"></i> {{ __('buttons.reset_filters') }}
            </button>
            <div id="filter-status" class="mt-2" style="display: none;">
                <small class="text-muted">
                    <i class="fas fa-filter"></i>
                    <span id="filter-status-text"></span>
                </small>
            </div>
        </div>
    </div>
</div>