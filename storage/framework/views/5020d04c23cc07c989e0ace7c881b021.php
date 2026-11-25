<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary', // primary, outline, dark, ghost, secondary, success, danger, light
    'size' => null,         // sm, lg, or null for normal
    'type' => 'button',     // button, submit, reset
    'href' => null,         // if provided, renders an <a> tag styled as button
    'icon' => null,         // FontAwesome icon class e.g. "fas fa-plus"
    'iconPosition' => 'left', // left or right
    'loading' => false,     // boolean for spinner
    'disabled' => false,    // disabled attribute
    'block' => false,       // w-100 full width
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'variant' => 'primary', // primary, outline, dark, ghost, secondary, success, danger, light
    'size' => null,         // sm, lg, or null for normal
    'type' => 'button',     // button, submit, reset
    'href' => null,         // if provided, renders an <a> tag styled as button
    'icon' => null,         // FontAwesome icon class e.g. "fas fa-plus"
    'iconPosition' => 'left', // left or right
    'loading' => false,     // boolean for spinner
    'disabled' => false,    // disabled attribute
    'block' => false,       // w-100 full width
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<?php if($href): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?> <?php if($disabled): ?> tabindex="-1" aria-disabled="true" style="pointer-events: none; opacity: 0.65;" <?php endif; ?>>
        <?php if($loading): ?>
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        <?php elseif($icon && $iconPosition === 'left'): ?>
            <i class="<?php echo e($icon); ?> me-1"></i>
        <?php endif; ?>

        <?php echo e($slot); ?>


        <?php if(!$loading && $icon && $iconPosition === 'right'): ?>
            <i class="<?php echo e($icon); ?> ms-1"></i>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button type="<?php echo e($type); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?> <?php if($disabled || $loading): ?> disabled <?php endif; ?>>
        <?php if($loading): ?>
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        <?php elseif($icon && $iconPosition === 'left'): ?>
            <i class="<?php echo e($icon); ?> me-1"></i>
        <?php endif; ?>

        <?php echo e($slot); ?>


        <?php if(!$loading && $icon && $iconPosition === 'right'): ?>
            <i class="<?php echo e($icon); ?> ms-1"></i>
        <?php endif; ?>
    </button>
<?php endif; ?>
<?php /**PATH C:\Ketan\projects\barber\resources\views/components/btn.blade.php ENDPATH**/ ?>