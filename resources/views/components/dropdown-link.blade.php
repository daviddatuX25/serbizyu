@props(['active' => false])

<a {{ $attributes->merge(['class' => "dropdown-link {{ $active ? 'dropdown-active' : 'dropdown-inactive' }}"]) }}>
    {{ $slot }}
</a>