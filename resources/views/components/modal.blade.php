@props([
    'name',
    'title',
    'maxWidth' => 'md'
])

@php
$maxWidth = [
    'sm' => 'modal-sm',
    'md' => 'modal-md',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    'fullscreen' => 'modal-fullscreen',
][$maxWidth];
@endphp

<div wire:ignore.self class="modal zoomIn" id="{{ $name }}" tabindex="-1" aria-labelledby="masterModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered {{ $maxWidth }}">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header bg-light p-3">
                <h4 class="card-title mb-0" id="masterModal">{{ $title }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="cancelEdit"></button>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
