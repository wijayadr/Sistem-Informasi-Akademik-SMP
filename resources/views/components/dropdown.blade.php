@props(['active', 'id' => ''])

@php
$classes = ($active ?? false)
            ? 'show'
            : '';
@endphp

<div class="collapse menu-dropdown {{ $classes }}" id="{{ $id }}">
    <ul class="nav nav-sm flex-column">
        {{ $slot }}
    </ul>
</div>
