@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') { show = true; }"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') { show = false; }"
    x-on:keydown.escape.window="if (show) { show = false; }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div 
        x-show="show"
        x-transition.opacity
        class="fixed inset-0 bg-black/50"
        @click="show = false"
    ></div>

    {{-- Modal Panel --}}
    <div 
        x-show="show"
        x-transition.scale.origin.center
        @click.stop
        class="relative min-h-screen flex items-center justify-center p-4"
    >
        <div class="modal-content {{ $maxWidth }} bg-background dark:bg-secondary-dark rounded-xl shadow-xl">
            {{ $slot }}
        </div>
    </div>
</div>