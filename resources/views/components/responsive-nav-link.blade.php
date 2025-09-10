@props(['active' => false])

@php
    $classes = 'responsive-nav-link' . ($active ? ' active' : '');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
