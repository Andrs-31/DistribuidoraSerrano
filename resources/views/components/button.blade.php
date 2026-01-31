@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'disabled' => false,
    'href' => null,
])

@php
    $baseClasses = 'btn d-inline-flex align-items-center justify-content-center gap-2 fw-medium';

    $variantClasses = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'success' => 'btn-success',
        'danger' => 'btn-danger',
        'warning' => 'btn-warning',
        'info' => 'btn-info',
        'light' => 'btn-light',
        'dark' => 'btn-dark',
        'outline-primary' => 'btn-outline-primary',
        'outline-secondary' => 'btn-outline-secondary',
        'outline-danger' => 'btn-outline-danger',
        'link' => 'btn-link',
    ];

    $sizeClasses = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];

    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? 'btn-primary') . ' ' . ($sizeClasses[$size] ?? '');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled) aria-disabled="true" tabindex="-1" @endif>
        @if($loading)
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        @elseif($icon && $iconPosition === 'left')
            <i class="bi bi-{{ $icon }}"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right' && !$loading)
            <i class="bi bi-{{ $icon }}"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled || $loading) disabled @endif>
        @if($loading)
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span>Cargando...</span>
        @else
            @if($icon && $iconPosition === 'left')
                <i class="bi bi-{{ $icon }}"></i>
            @endif
            {{ $slot }}
            @if($icon && $iconPosition === 'right')
                <i class="bi bi-{{ $icon }}"></i>
            @endif
        @endif
    </button>
@endif
