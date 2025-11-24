@props(['status', 'type' => 'status'])

@php
    $statusColors = [
        'status' => [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'disputed' => 'bg-orange-100 text-orange-800',
        ],
        'payment' => [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
        ]
    ];
    
    $colors = $statusColors[$type][$status] ?? 'bg-gray-100 text-gray-800';
    $label = ucfirst(str_replace('_', ' ', $status));
@endphp

<span {{ $attributes->merge(['class' => "px-3 py-1 rounded-full text-sm font-medium $colors"]) }}>
    {{ $label }}
</span>
