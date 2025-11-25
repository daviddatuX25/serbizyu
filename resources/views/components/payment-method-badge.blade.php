@props(['method' => 'any', 'showDescription' => false])

@php
    $colors = [
        'cash' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-800', 'border' => 'border-blue-200', 'icon' => '💵'],
        'online' => ['bg' => 'bg-green-50', 'text' => 'text-green-800', 'border' => 'border-green-200', 'icon' => '💳'],
        'any' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-800', 'border' => 'border-purple-200', 'icon' => '🔄'],
    ];
    
    $labels = [
        'cash' => 'Cash Payment',
        'online' => 'Online Payment',
        'any' => 'Any Method',
    ];
    
    $descriptions = [
        'cash' => 'Pay in person or via local method',
        'online' => 'Pay via online (card, e-wallet, bank)',
        'any' => 'Buyer can choose payment method',
    ];
    
    $color = $colors[$method] ?? $colors['any'];
    $label = $labels[$method] ?? $labels['any'];
    $description = $descriptions[$method] ?? $descriptions['any'];
@endphp

<div class="inline-flex flex-col">
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $color['bg'] }} {{ $color['text'] }} border {{ $color['border'] }}">
        <span class="mr-1">{{ $color['icon'] }}</span>
        {{ $label }}
    </span>
    @if($showDescription)
        <span class="text-xs text-gray-600 mt-1">{{ $description }}</span>
    @endif
</div>
