@props([
    'title',
    'status' => null,
    'statusClass' => '',
    'details' => [],
    'actions' => [],
])

<div class="bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden hover:shadow-xl transition-shadow">
    <div class="p-4 space-y-3">
        <div class="flex justify-between items-start">
            <h3 class="text-lg font-bold text-gray-800 line-clamp-2">{{ $title }}</h3>
            @if($status)
                <span class="px-3 py-1 text-xs font-semibold rounded-full shadow {{ $statusClass }}">
                    {{ $status }}
                </span>
            @endif
        </div>
        
        <div class="grid grid-cols-2 gap-2 pt-2 border-t text-sm">
            @foreach($details as $label => $value)
                <div>
                    <p class="text-xs text-gray-500">{{ $label }}</p>
                    <p class="font-semibold">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="flex space-x-2 pt-3 border-t">
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" 
                   class="flex-1 text-center py-2 rounded-lg text-sm font-medium transition {{ $action['class'] ?? '' }}">
                    {{ $action['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>
