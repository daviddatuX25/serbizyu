@props([
    'title' => config('app.name', 'Serbizyu'),
    'cssFiles' => [],
    'jsFiles' => [],
])

@php
    $cssFiles = is_array($cssFiles) ? $cssFiles : [$cssFiles];
    $jsFiles = is_array($jsFiles) ? $jsFiles : [$jsFiles];
    if (empty($cssFiles)) $cssFiles = ['app.css'];
    if (empty($jsFiles)) $jsFiles = ['app.js'];

    // 1. GET THE MENUS FROM CONFIG
    $creatorMenu = config('navigation.creator'); // For Sidebar
    $mainMenu = config('navigation.main');       // For Header
    $creatorProfileMenu = config('navigation.creator_profile');
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
<body class="h-full text-gray-700 font-sans antialiased overflow-x-hidden">

    <aside 
        x-data="{ 
            mobileMenuOpen: false, 
            activePopover: null,
            togglePopover(id) { this.activePopover = this.activePopover === id ? null : id; }
        }"
        @open-mobile-menu.window="mobileMenuOpen = true"
        class="fixed top-0 bottom-0 left-0 z-50 bg-white border-r border-green-200 transition-all duration-300 ease-in-out flex flex-col overflow-visible"
        :class="{
            'translate-x-0 w-64': mobileMenuOpen,
            '-translate-x-full w-64': !mobileMenuOpen,
            'md:translate-x-0 md:w-20 lg:w-72': true
        }">
        
        <div x-show="mobileMenuOpen"
             x-transition.opacity
             class="fixed inset-0z-[-1] lg:hidden"
             @click="mobileMenuOpen = false" x-cloak></div>

        <div class="h-20 flex items-center justify-between px-6 md:px-0 md:justify-center lg:justify-start lg:px-6 border-b border-green-100 md:border-none">
            <div class="flex items-center gap-2">
                <a href="{{ route('creator.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full border-2 border-green-800 flex items-center justify-center shrink-0">
                        <div class="w-6 h-6 bg-green-800 rounded-full"></div>
                    </div>
                    <span class="font-bold text-xl tracking-tight md:hidden lg:block">Serbizyu</span>
                </a>
            </div>
            <button @click="mobileMenuOpen = false" class="md:hidden p-2 text-gray-500 hover:bg-green-100 rounded-full">
                <i data-lucide="x"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto md:overflow-visible py-6 flex flex-col gap-2 px-4 md:px-0 lg:px-4 relative">
            @foreach($creatorMenu as $item)
                @php $isActive = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group md:justify-center lg:justify-start
                   {{ $isActive ? 'bg-green-800 text-white shadow-md' : 'text-gray-600 hover:bg-green-100 hover:text-gray-900' }}">
                    
                    <i data-lucide="{{ $item['icon'] }}" 
                       class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-gray-500 group-hover:text-gray-900' }}"></i>
                    
                    <span class="text-sm font-medium whitespace-nowrap md:hidden lg:block">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>

        <div class="p-4 border-t border-green-100 md:border-none lg:border-t md:p-2 lg:p-4 relative group">
            <div @click="togglePopover('profile')"
                 class="flex items-center gap-3 md:justify-center lg:justify-start p-2 rounded-xl hover:bg-green-50 cursor-pointer border border-green-200 md:border-0 lg:border transition-all relative z-10"
                 :class="{'bg-green-50 border-green-300': activePopover === 'profile'}">
                <div class="w-10 h-10 rounded-full bg-green-200 overflow-hidden shrink-0">
                      <svg class="w-full h-full text-green-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                      </svg>
                </div>
                <div class="flex flex-col md:hidden lg:flex whitespace-nowrap">
                    <span class="text-sm font-bold text-gray-900">{{ Auth::user()->firstname ?? 'Guest' }}</span>
                    <span class="text-xs text-gray-500">Greetings</span>
                </div>
            </div>

            <div x-show="activePopover === 'profile'"
                @click.away="activePopover = null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute z-[100] bg-white border border-green-200 shadow-xl rounded-2xl p-1 w-full bottom-[calc(100%+0.5rem)] left-0 right-0 md:bottom-2 md:left-[4.5rem] md:w-48 lg:bottom-[calc(100%+0.5rem)] lg:left-0 lg:w-full" x-cloak>
                
                <div class="flex flex-col gap-1">
                    
                    {{-- DYNAMIC PROFILE LINKS --}}
                    @foreach($creatorProfileMenu as $item)
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2 hover:bg-green-50 rounded-xl text-sm font-medium text-gray-700 transition-colors">
                            <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 text-gray-500"></i>
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <div class="h-px bg-green-100 my-0.5"></div>

                    {{-- LOGOUT (Kept manual because it needs a form) --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center gap-3 px-3 py-2 hover:bg-red-50 rounded-xl text-sm font-medium text-red-600 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4 text-red-500"></i>
                            Logout
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </aside>


    <main class="min-h-screen transition-all duration-300 ease-in-out md:ml-20 lg:ml-72">
        
        <header x-data="{ searchActive: false, mobileNavOpen: false }"
                class="sticky top-0 z-30 bg-green-50/90 backdrop-blur-sm px-4 py-4 md:px-8 md:py-6 border-b border-green-100 md:border-none">
            <div class="flex items-center justify-between relative h-14">

                <div class="flex flex-shrink-1 sm:flex-shrink-0 items-center gap-3 min-w-0 transition-opacity duration-300" :class="{'opacity-0 pointer-events-none': searchActive}">
                    <button @click="$dispatch('open-mobile-menu')" class="p-2 -ml-2 text-gray-600 md:hidden shrink-0">
                        <i data-lucide="menu"></i>
                    </button>
                    <div class="truncate text-sm md:text-xl">
                        {{ $header ?? '' }}
                    </div>
                </div>

                <div class="flex items-center gap-2 md:gap-4 transition-all duration-500 z-20 ml-auto"
                     :class="{'w-[95%] justify-end': searchActive, 'w-auto justify-end': !searchActive}">
                    
                    <div class="relative flex items-center transition-all duration-500 ease-out shrink-0"
                        :class="{'flex-1 h-12 bg-white border-2 border-green-800 rounded-full px-4 shadow-sm': searchActive, 'bg-white border-2 border-green-800 rounded-full px-1 py-1': !searchActive}">

                        <button x-show="!searchActive"
                                @click="searchActive = true; mobileNavOpen = false; $nextTick(() => $refs.searchInput.focus())"
                                class="p-2 hover:bg-green-100 rounded-full transition-colors shrink-0">
                            <i data-lucide="sparkles" class="w-5 h-5 md:w-6 md:h-6 text-green-900"></i>
                        </button>

                        <button x-show="!searchActive"
                                @click="mobileNavOpen = !mobileNavOpen"
                                class="p-2 hover:bg-green-100 rounded-full transition-colors shrink-0 sm:hidden ml-1">
                            <i data-lucide="layout-grid" class="w-5 h-5 text-gray-600"></i>
                        </button>

                        <div x-show="searchActive" x-cloak class="flex-1 flex items-center h-full overflow-hidden w-full">
                            <i data-lucide="sparkles" class="w-5 h-5 text-green-800 mr-3 shrink-0"></i>
                            <div class="flex-1 flex flex-col justify-center h-full">
                                <input x-ref="searchInput" type="text" placeholder="Generate insight..." class="w-full bg-transparent focus:outline-none focus:ring-0 border-none text-gray-700 placeholder-gray-500 font-medium h-full">
                            </div>
                            <button @click.stop="searchActive = false" class="ml-2 p-1 rounded-full hover:bg-green-100 text-gray-500">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <div x-show="!searchActive" class="hidden sm:flex items-center">
                            @foreach($mainMenu as $item)
                                @if(isset($item['auth']) && $item['auth'])
                                    @auth
                                        @if(!Str::startsWith(request()->route()->getName(), 'creator.'))
                                            <a href="{{ route($item['route']) }}" 
                                               class="px-5 py-2 rounded-full text-sm font-medium transition-all shrink-0 whitespace-nowrap ml-1
                                               {{ request()->routeIs($item['route']) ? 'bg-green-800 text-white' : 'hover:bg-green-100 text-gray-600' }}">
                                                {{ $item['label'] }}
                                            </a>
                                        @endif
                                    @endauth
                                @else
                                    <a href="{{ route($item['route']) }}" 
                                       class="px-5 py-2 rounded-full text-sm font-medium transition-all shrink-0 whitespace-nowrap ml-1
                                       {{ request()->routeIs($item['route']) ? 'bg-green-800 text-white' : 'hover:bg-green-100 text-gray-600' }}">
                                        {{ $item['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        <div x-show="mobileNavOpen" 
                             @click.away="mobileNavOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute top-full right-0 mt-3 w-56 bg-white border border-green-200 shadow-xl rounded-2xl p-2 z-50 sm:hidden" x-cloak>
                            
                            <div class="flex flex-col gap-1">
                                @foreach($mainMenu as $item)
                                    <a href="{{ route($item['route']) }}" 
                                       class="px-4 py-2.5 rounded-xl text-sm font-medium
                                       {{ request()->routeIs($item['route']) ? 'bg-green-800 text-white' : 'hover:bg-green-50 text-gray-600' }}">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <!-- AI Prompt Suggestions (Dropdown) -->
                <div x-show="searchActive" 
                     x-transition:enter="transition ease-out duration-300 delay-100"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute top-20 left-1/2 transform -translate-x-1/2 bg-white border border-gray-200 shadow-xl rounded-2xl p-6 w-[90%] md:w-[600px] z-20" x-cloak>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Suggested Prompts</h4>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer group">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100">
                                <i data-lucide="trending-up" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <span class="text-sm text-gray-700">Show me sales trends for Q3</span>
                        </li>
                        <li class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer group">
                            <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center group-hover:bg-purple-100">
                                <i data-lucide="users" class="w-4 h-4 text-purple-600"></i>
                            </div>
                            <span class="text-sm text-gray-700">Analyze new user acquisition</span>
                        </li>
                    </ul>
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