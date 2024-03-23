{{--  --}}

@props(['name', 'type', 'value', 'COLINPUT', 'COLLABEL'])

@php
    $inputId = 'input-' . $name;
    $inputLabel = __('attributes.' . $name);
    $errorsForName = $errors->get($name);
@endphp

<div {{ $attributes->merge(['class' => 'form-group row']) }}>
    <x-input-label for="{{ $inputId }}"
        class="{{ $COLLABEL ?? 'col-sm-12' }} col-form-label">{{ $inputLabel }}</x-input-label>

    <div class="{{ $COLINPUT ?? 'col-sm-12' }}">
        <select class="form-control select2" style="width: 100%;" name="{{ $name }}">
            opt
            @foreach ($options as $option)
                <option value="{{ $option->id }}">{{ $option->name }}</option>
            @endforeach
        </select>
    </div>

    <x-input-error :messages="$errorsForName" />
</div>
