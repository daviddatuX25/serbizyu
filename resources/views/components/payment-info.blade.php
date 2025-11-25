@props(['paymentMethod' => 'any', 'payFirst' => false, 'compact' => false])

@php
    $icons = [
        'cash' => '💵',
        'online' => '💳',
        'any' => '🔄',
    ];
    
    $colors = [
        'cash' => 'text-blue-600',
        'online' => 'text-green-600',
        'any' => 'text-purple-600',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'payment-info ' . ($compact ? 'flex items-center gap-2' : 'space-y-2')]) }}>
    @if($payFirst)
        <div class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">
            <span class="mr-1">⚠️</span>
            Payment Required
        </div>
    @endif
    
    <div class="inline-flex items-center gap-1">
        <span class="text-lg">{{ $icons[$paymentMethod] ?? $icons['any'] }}</span>
        <span class="text-sm {{ $colors[$paymentMethod] ?? $colors['any'] }}">
            @switch($paymentMethod)
                @case('cash')
                    Cash
                    @break
                @case('online')
                    Online
                    @break
                @default
                    Any Method
            @endswitch
        </span>
    </div>
</div>
