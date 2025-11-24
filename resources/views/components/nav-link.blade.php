@props(['item', 'mobile' => false])

@php
    $active = request()->routeIs($item['route']);
    // Base classes
    $classes = $mobile 
        ? 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors ' 
        : 'px-5 py-2 rounded-full text-sm font-medium transition-all shrink-0 whitespace-nowrap flex items-center gap-2 ';
    
    // Active/Inactive styles
    if ($active) {
        $classes .= $mobile ? 'bg-gray-900 text-white' : 'bg-gray-800 text-white';
    } else {
        $classes .= $mobile ? 'hover:bg-gray-50 text-gray-600' : 'hover:bg-gray-100 text-gray-600';
    }
@endphp

<a href="{{ route($item['route']) }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($item['icon']) && $mobile)
        <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 {{ $active ? 'text-white' : 'text-gray-500' }}"></i>
    @endif
    {{ $item['label'] }}
</a>