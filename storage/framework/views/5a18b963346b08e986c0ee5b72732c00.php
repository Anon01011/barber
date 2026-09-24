<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'width' => '60%'  // Default width set to 60%
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
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'width' => '60%'  // Default width set to 60%
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
?>

<div class="modal fade" id="<?php echo e($id ?? 'modal'); ?>" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog <?php echo e($size ?? ''); ?>" style="max-width: <?php echo e($width); ?>; width: 100%;">
    <div class="modal-content" style="backdrop-filter: blur(10px); background: rgba(255,255,255,0.85); border-radius: 1rem;">
      <div class="modal-header <?php echo e($headerClass ?? 'border-0'); ?>">
        <h5 class="modal-title <?php echo e($titleClass ?? ''); ?>"><?php echo e($title ?? ''); ?></h5>
        <button type="button" class="<?php echo e($closeButtonClass ?? 'btn-close'); ?>" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php echo e($slot); ?>

      </div>
      <?php if(isset($footer)): ?>
      <div class="modal-footer border-0">
        <?php echo e($footer); ?>

      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\components\modal.blade.php ENDPATH**/ ?>