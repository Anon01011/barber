@props([
    'variant' => 'primary', // primary, outline, dark, ghost, secondary, success, danger, light
    'size' => null,         // sm, lg, or null for normal
    'type' => 'button',     // button, submit, reset
    'href' => null,         // if provided, renders an <a> tag styled as button
    'icon' => null,         // FontAwesome icon class e.g. "fas fa-plus"
    'iconPosition' => 'left', // left or right
    'loading' => false,     // boolean for spinner
    'disabled' => false,    // disabled attribute
    'block' => false,       // w-100 full width
])

@php
    $variantClasses = [
        'primary' => 'btn-luxury btn-primary',
        'outline' => 'btn-luxury-outline btn-outline-primary',
        'dark' => 'btn-luxury-dark',
        'ghost' => 'btn-luxury-ghost',
        'secondary' => 'btn-luxury-secondary btn-outline-secondary',
        'success' => 'btn-luxury-success btn-success',
        'danger' => 'btn-luxury-danger btn-danger',
        'light' => 'btn-light text-dark',
    ];

    $sizeClasses = [
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
    ];

    $classes = 'btn ' . ($variantClasses[$variant] ?? 'btn-luxury');
    if ($size && isset($sizeClasses[$size])) {
        $classes .= ' ' . $sizeClasses[$size];
    }
    if ($block) {
        $classes .= ' w-100';
    }
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled) tabindex="-1" aria-disabled="true" style="pointer-events: none; opacity: 0.65;" @endif>
        @if($loading)
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        @elseif($icon && $iconPosition === 'left')
            <i class="{{ $icon }} me-1"></i>
        @endif

        {{ $slot }}

        @if(!$loading && $icon && $iconPosition === 'right')
            <i class="{{ $icon }} ms-1"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled || $loading) disabled @endif>
        @if($loading)
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        @elseif($icon && $iconPosition === 'left')
            <i class="{{ $icon }} me-1"></i>
        @endif

        {{ $slot }}

        @if(!$loading && $icon && $iconPosition === 'right')
            <i class="{{ $icon }} ms-1"></i>
        @endif
    </button>
@endif
