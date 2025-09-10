<footer class="mt-[--spacing-3]" style="background-color: var(--color-background); border-top: 1px solid var(--color-secondary-dark);">
    <div class="max-w-7xl mx-auto px-[--spacing-3] py-[--spacing-3] flex flex-col md:flex-row justify-between items-center">
        <div class="text-lg font-bold text-[--color-text] mb-[--spacing-2] md:mb-0" style="font-family: var(--font-display);">
            Serbizyu<span style="color: var(--color-primary);">.</span>
        </div>
        <div class="flex space-x-[--spacing-3] text-[--color-text-secondary] mb-[--spacing-2] md:mb-0">
            {{-- <x-nav-link href="{{ route('about') }}">About</x-nav-link> --}}
            {{-- <x-nav-link href="{{ route('categories') }}">Categories</x-nav-link> --}}
        </div>
        <div class="text-[--color-text-secondary]" style="font-size: 0.875rem;">&copy; {{ date('Y') }} Serbizyu. All rights reserved.</div>
    </div>
</footer>