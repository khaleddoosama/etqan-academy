@props(['inputs', 'types', 'names'])

<div class="col-md-12">
    <h4>{{ __('attributes.sections') }}</h4>
    <div class="row" id="sections">
        @for ($i = 0; $i < $inputs; $i++)
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sections">{{ __('attributes.section_name') }} {{ $i + 1 }}</label>
                    <input type="{{ $types[$i] ?? 'text' }}" name="sections[{{ $i }}][name]"
                        class="form-control" id="sections" placeholder="{{ $names[$i] ?? 'Section Name' }}" required
                        autocomplete="sections">
                </div>
            </div>
        @endfor
        {{-- plus button --}}
        <div class="mb-3 col-md-1">
            <button type="button" class="btn btn-primary " onclick="addSection()">+</button>
        </div>
    </div>
</div>
