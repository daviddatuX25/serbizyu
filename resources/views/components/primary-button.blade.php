@props(['disabled' => false])

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary ms-4', 'disabled' => $disabled]) }}>
    {{ $slot }}
</button>