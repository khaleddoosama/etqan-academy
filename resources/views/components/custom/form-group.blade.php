@props(['name', 'type', 'value', 'COLINPUT', 'COLLABEL', 'options', 'selected', 'multiple' => false])

@php
    //example sections[0][title] must be transformed to sections_title
    $baseKey = preg_replace('/\[[^\]]*\]/', '', $name, 1); // Removes numeric indices.

    $index = preg_match('/\[[^\]]+\]/', $name, $matches) ? $matches[0] : '';
    $index = substr($index, 1, -1);

    $transformedKey = str_replace(['[', ']'], '_', $baseKey);
    $transformedKey = strtolower($transformedKey);
    $transformedKey = rtrim($transformedKey, '_');

    $inputLabel = __('attributes.' . $transformedKey);

    if (preg_match('/\[[^\]]+\]/', $name)) {
        $inputLabel .= '(' . $index . ')';
    }

    $inputId = 'input-' . $name;
    // remove [] from id
    $inputId = str_replace('[]', '', $inputId);

    //example sections[0][title] must be transformed to sections.0.title
    $error = str_replace(['[', ']'], '.', $name);
    $error = rtrim($error, '.');
    $error = str_replace('..', '.', $error);

    $errorsForName = $errors->get($error);

    // selected
    if (isset($selected)) {
        $selectedArray = is_array($selected) ? $selected : explode(',', $selected);
    } else {
        $selectedArray = [];
    }
@endphp

<div {{ $attributes->merge(['class' => 'form-group row']) }}>
    <x-input-label for="{{ $inputId }}"
        class="{{ $COLLABEL ?? 'col-sm-12' }} col-form-label">{{ $inputLabel }}</x-input-label>

    @if ($type === 'file')
        <div class="input-group {{ $COLINPUT ?? 'col-sm-12' }}">
            <div class="custom-file">
                <input type="{{ $type }}" name="{{ $name }}" id="{{ $inputId }}"
                    class="custom-file-input" {{ $multiple ? 'multiple' : '' }}>
                <x-input-label for="{{ $inputId }}" class="custom-file-label col-form-label"
                    data-browse="{{ __('buttons.browse') }}">{{ __('buttons.choose') }}</x-input-label>
            </div>
        </div>
    @elseif ($type === 'select')
        <div class="{{ $COLINPUT ?? 'col-sm-12' }}">
            <select class="form-control select2" style="width: 100%;" name="{{ $name }}"
                {{ $multiple ? 'multiple' : '' }}>
                <option {{ $multiple ? '' : 'selected' }} disabled>{{ __('buttons.choose') }}</option>
                @foreach ($options as $option)
                    <option value="{{ $option->id }}" {{-- check if selected is exist --}}
                        {{ in_array($option->id, $selectedArray) ? 'selected' : '' }}>


                        {{ $option->name }}</option>
                @endforeach
            </select>

        </div>
    @elseif ($type === 'textarea')
        <div class="{{ $COLINPUT ?? 'col-sm-12' }}">
            <textarea name="{{ $name }}" id="{{ $inputId }}" class="form-control" rows="1">{{ old($name) ?? ($value ?? '') }}</textarea>
        </div>
    @else
        <div class="{{ $COLINPUT ?? 'col-sm-12' }}">
            <x-text-input type="{{ $type }}" name="{{ $name }}" id="{{ $inputId }}"
                value="{{ old($name) ?? ($value ?? '') }}" class="form-control" />
        </div>
    @endif

    <x-input-error :messages="$errorsForName" style="padding: 0 7.5px;margin: 0;" />
</div>
