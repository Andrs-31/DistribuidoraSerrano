@props([
    'id' => 'modal',
    'title' => '',
    'size' => 'md',
    'static' => false,
    'centered' => true,
    'scrollable' => false,
])

@php
    $sizeClasses = [
        'sm' => 'modal-sm',
        'md' => '',
        'lg' => 'modal-lg',
        'xl' => 'modal-xl',
    ];

    $dialogClasses = 'modal-dialog';
    $dialogClasses .= ' ' . ($sizeClasses[$size] ?? '');
    $dialogClasses .= $centered ? ' modal-dialog-centered' : '';
    $dialogClasses .= $scrollable ? ' modal-dialog-scrollable' : '';
@endphp

<div
    class="modal fade"
    id="{{ $id }}"
    tabindex="-1"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
    @if($static) data-bs-backdrop="static" data-bs-keyboard="false" @endif
>
    <div class="{{ $dialogClasses }}">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if(isset($footer))
                <div class="modal-footer border-top">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
