@props([
    'title' => config('app.name', 'Serbizyu'),
    'cssFiles' => [],
    'jsFiles' => [],
    'header' => null,
])

@php
    $cssFiles = is_array($cssFiles) ? $cssFiles : [$cssFiles];
    $jsFiles = is_array($jsFiles) ? $jsFiles : [$jsFiles];
    if (empty($cssFiles)) $cssFiles = ['app.css'];
    if (empty($jsFiles)) $jsFiles = ['app.js'];

    // 1. GET THE MENUS FROM CONFIG
    $adminMenu = config('navigation.admin'); // For Sidebar
    $adminProfileMenu = config('navigation.admin_profile');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Serbizyu') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    @foreach ($cssFiles as $css)
        @vite("resources/css/{$css}")
    @endforeach
    @stack('styles')

    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full text-gray-700 font-sans antialiased overflow-x-hidden"
      x-data="{
          mobileMenuOpen: false,
          activePopover: null,
          togglePopover(id) { this.activePopover = this.activePopover === id ? null : id; }
      }"
      @open-mobile-menu.window="mobileMenuOpen = true">

    <aside
        x-cloak
        class="fixed top-0 bottom-0 left-0 z-50 flex flex-col overflow-visible
               bg-slate-900 border-r border-slate-700 transition-all duration-300 ease-in-out"
        :class="{
            // MOBILE ONLY
            'translate-x-0 w-64': mobileMenuOpen,
            '-translate-x-full w-64': !mobileMenuOpen,

            // DESKTOP ONLY
            'md:translate-x-0 md:w-20 lg:w-72': true
        }">

        <div x-show="mobileMenuOpen"
             x-transition.opacity
             class="fixed inset-0 z-[-1] lg:hidden bg-black/50"
             @click="mobileMenuOpen = false" x-cloak></div>

        <div class="h-20 flex items-center justify-between px-6 md:px-0 md:justify-center lg:justify-start lg:px-6 border-b border-slate-700 md:border-none">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full border-2 border-blue-400 flex items-center justify-center shrink-0 bg-blue-500/10">
                        <i data-lucide="lock" class="w-6 h-6 text-blue-400"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-white md:hidden lg:block">Admin</span>
                </a>
            </div>
            <button @click="mobileMenuOpen = false" class="md:hidden p-2 text-slate-400 hover:bg-slate-800 rounded-full">
                <i data-lucide="x"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto md:overflow-visible py-6 flex flex-col gap-2 px-4 md:px-0 lg:px-4 relative">
            @foreach($adminMenu as $item)
                @php $isActive = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group md:justify-center lg:justify-start
                   {{ $isActive ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                    <i data-lucide="{{ $item['icon'] }}"
                       class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"></i>

                    <span class="text-sm font-medium whitespace-nowrap md:hidden lg:block">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>

        <div class="p-4 border-t border-slate-700 md:border-none lg:border-t md:p-2 lg:p-4 relative group">
            <div @click="togglePopover('profile')"
                 class="flex items-center gap-3 md:justify-center lg:justify-start p-2 rounded-xl hover:bg-slate-800 cursor-pointer border border-slate-700 md:border-0 lg:border transition-all relative z-10"
                 :class="{'bg-slate-800 border-slate-600': activePopover === 'profile'}">
                <div class="w-10 h-10 rounded-full bg-blue-500/20 overflow-hidden shrink-0 flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-blue-400"></i>
                </div>
                <div class="flex flex-col md:hidden lg:flex whitespace-nowrap">
                    <span class="text-sm font-bold text-white">{{ Auth::user()->firstname ?? 'Admin' }}</span>
                    <span class="text-xs text-slate-400">Administrator</span>
                </div>
            </div>

            <div x-show="activePopover === 'profile'"
                @click.away="activePopover = null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute z-[100] bg-slate-800 border border-slate-700 shadow-xl rounded-2xl p-1 w-full bottom-[calc(100%+0.5rem)] left-0 right-0 md:bottom-2 md:left-[4.5rem] md:w-48 lg:bottom-[calc(100%+0.5rem)] lg:left-0 lg:w-full" x-cloak>

                <div class="flex flex-col gap-1">

                    {{-- DYNAMIC PROFILE LINKS --}}
                    @foreach($adminProfileMenu as $item)
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2 hover:bg-slate-700 rounded-xl text-sm font-medium text-slate-200 transition-colors">
                            <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 text-slate-400"></i>
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <div class="h-px bg-slate-700 my-0.5"></div>

                    {{-- LOGOUT (Kept manual because it needs a form) --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center gap-3 px-3 py-2 hover:bg-red-950 rounded-xl text-sm font-medium text-red-400 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4 text-red-500"></i>
                            Logout
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </aside>


    <main class="min-h-screen transition-all duration-300 ease-in-out md:ml-20 lg:ml-72 bg-slate-950">

        <header class="sticky top-0 z-30 bg-slate-900/95 backdrop-blur-sm px-4 py-4 md:px-8 md:py-6 border-b border-slate-700 md:border-slate-700">
            <div class="flex items-center justify-between relative h-14">

                <div class="flex flex-shrink-1 sm:flex-shrink-0 items-center gap-3 min-w-0">
                    <button @click="$dispatch('open-mobile-menu')" class="p-2 -ml-2 text-slate-400 md:hidden shrink-0 hover:text-slate-200">
                        <i data-lucide="menu"></i>
                    </button>
                    <div class="truncate text-sm md:text-xl font-semibold text-white">
                        {{ $header ?? 'Admin Dashboard' }}
                    </div>
                </div>

                <div class="flex items-center gap-2 md:gap-4 ml-auto">
                    <div class="hidden sm:flex items-center gap-4">
                        <button class="p-2 text-slate-400 hover:bg-slate-800 rounded-full hover:text-slate-200 transition-colors">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                        </button>
                        <div class="w-px h-6 bg-slate-700"></div>
                        <button class="p-2 text-slate-400 hover:bg-slate-800 rounded-full hover:text-slate-200 transition-colors">
                            <i data-lucide="settings" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-4 md:p-8">
            {{ $slot }}
        </div>

    </main>

    @livewireScripts

    @foreach ($jsFiles as $js)
        @vite("resources/js/{$js}")
    @endforeach

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        document.addEventListener('livewire:navigated', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
