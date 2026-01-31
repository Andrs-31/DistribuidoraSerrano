@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'placeholder' => 'Seleccionar...',
    'value' => '',
    'options' => [],
    'required' => false,
    'disabled' => false,
    'hint' => null,
    'optionValue' => 'id',
    'optionLabel' => 'name',
])

@php
    $selectId = $id ?? $name;
    $hasError = $errors->has($name);
    $selectedValue = old($name, $value);
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $selectId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $selectId }}"
        {{ $attributes->merge(['class' => 'form-select' . ($hasError ? ' is-invalid' : '')]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $option)
            @if(is_array($option) || is_object($option))
                @php
                    $optValue = is_array($option) ? ($option[$optionValue] ?? '') : ($option->$optionValue ?? '');
                    $optLabel = is_array($option) ? ($option[$optionLabel] ?? '') : ($option->$optionLabel ?? '');
                @endphp
                <option value="{{ $optValue }}" @if($selectedValue == $optValue) selected @endif>
                    {{ $optLabel }}
                </option>
            @else
                <option value="{{ $option }}" @if($selectedValue == $option) selected @endif>
                    {{ $option }}
                </option>
            @endif
        @endforeach

        {{ $slot }}
    </select>

    @if($hasError)
        <div class="invalid-feedback">
            {{ $errors->first($name) }}
        </div>
    @endif

    @if($hint && !$hasError)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>
