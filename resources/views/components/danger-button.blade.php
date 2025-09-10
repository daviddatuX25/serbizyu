@props(['disabled' => false])

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger', 'disabled' => $disabled]) }}>
    {{ $slot }}
</button>