@props(['align' => 'left'])

<div class="flex items-center {{ $align === 'center' ? 'justify-center' : 'justify-start' }}">
    <a href="{{ route('home') }}" class="text-2xl font-bold hover:opacity-50 transition-opacity">
        <span class="text-text dark:text-text-secondary">Serbizyu</span><span class="text-brand">.</span>
    </a>
</div>