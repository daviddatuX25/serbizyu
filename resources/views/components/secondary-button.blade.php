@props(['disabled' => false])

<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-secondary', 'disabled' => $disabled]) }}>
    {{ $slot }}
</button>