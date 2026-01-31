@props([
    'type' => 'text',
    'name' => '',
    'id' => null,
    'label' => null,
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'hint' => null,
    'icon' => null,
    'iconPosition' => 'left',
])

@php
    $inputId = $id ?? $name;
    $hasError = $errors->has($name);
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($icon)
        <div class="input-group">
            @if($iconPosition === 'left')
                <span class="input-group-text">
                    <i class="bi bi-{{ $icon }}"></i>
                </span>
            @endif

            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $inputId }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->merge(['class' => 'form-control' . ($hasError ? ' is-invalid' : '')]) }}
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            >

            @if($iconPosition === 'right')
                <span class="input-group-text">
                    <i class="bi bi-{{ $icon }}"></i>
                </span>
            @endif

            @if($hasError)
                <div class="invalid-feedback">
                    {{ $errors->first($name) }}
                </div>
            @endif
        </div>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'form-control' . ($hasError ? ' is-invalid' : '')]) }}
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
        >

        @if($hasError)
            <div class="invalid-feedback">
                {{ $errors->first($name) }}
            </div>
        @endif
    @endif

    @if($hint && !$hasError)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>
