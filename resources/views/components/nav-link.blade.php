@props(['active', 'icon' => '', 'modal' => '', 'dropdown' => ''])

@php
$classes = ($active ?? false)
            ? 'nav-link menu-link active'
            : 'nav-link menu-link';
@endphp

<li class="nav-item">
    <a
        {{ $attributes->merge(['class' => $classes]) }}
        @if($modal) data-bs-toggle="modal" data-bs-target="#{{ $modal }}" @endif
        @if($dropdown) href="#{{ $dropdown }}" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="{{ $dropdown }}" @endif
    >
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif

        <span>{{ $slot }}</span>
    </a>
    {{ $content ?? '' }}
</li>
