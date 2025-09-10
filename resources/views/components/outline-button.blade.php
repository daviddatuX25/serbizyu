@props(['disabled' => false])

<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline-white', 'disabled' => $disabled]) }}>
    {{ $slot }}
</button>