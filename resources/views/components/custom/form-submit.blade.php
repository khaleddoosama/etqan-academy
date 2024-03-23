@props(['text', 'class', 'COLOFFSET' => '', 'attr' => ''])
<div class="mb-0 form-group row">
    <div class="{{ $COLOFFSET }} col-sm-12">
        <button type="submit" class="btn {{ $class }}" {{ $attr }}>{{ $text }}</button>
    </div>
</div>
