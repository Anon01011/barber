@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'width' => '60%'  // Default width set to 60%
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div class="modal fade" id="{{ $id ?? 'modal' }}" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog {{ $size ?? '' }}" style="max-width: {{ $width }}; width: 100%;">
    <div class="modal-content" style="backdrop-filter: blur(10px); background: rgba(255,255,255,0.85); border-radius: 1rem;">
      <div class="modal-header {{ $headerClass ?? 'border-0' }}">
        <h5 class="modal-title {{ $titleClass ?? '' }}">{{ $title ?? '' }}</h5>
        <button type="button" class="{{ $closeButtonClass ?? 'btn-close' }}" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        {{ $slot }}
      </div>
      @if(isset($footer))
      <div class="modal-footer border-0">
        {{ $footer }}
      </div>
      @endif
    </div>
  </div>
</div>
