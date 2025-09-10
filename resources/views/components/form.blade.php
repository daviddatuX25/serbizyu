@props(['align' => 'center', 'maxWidth' => 'md'])

@php
$alignment = match ($align) {
    'left' => 'form-wrapper-left',
    'right' => 'form-wrapper-right',
    default => 'form-wrapper-centered',
};

$maxW = match ($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    default => 'sm:max-w-md',
};
@endphp

<div class="form-wrapper {{ $alignment }}">
    <div class="form-container {{ $maxW }}">
        {{ $header ?? '' }}
        <form class="space-y-2">
            {{ $slot }}
        </form>
        {{ $footer ?? '' }}
    </div>
</div>