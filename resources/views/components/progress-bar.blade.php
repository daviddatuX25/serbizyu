@props([
    'percentage' => 0,
    'height' => 'h-3',
    'barColor' => 'bg-blue-600',
])

<div class="w-full bg-gray-200 rounded-full {{ $height }} overflow-hidden">
    <div class="{{ $barColor }} {{ $height }} rounded-full transition-all duration-500" 
         @style("width: {$percentage}%")>
    </div>
</div>
