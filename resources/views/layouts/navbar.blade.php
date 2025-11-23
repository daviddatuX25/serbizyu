@props([
  // AUTOMATICALLY pull from config if not provided
  'navItems' => config('navigation.main'), 
  'authProfileData' => Auth::user() 
])

<header class="navbar" x-data="{ open: false }">
  <div class="navbar-inner">
    
    <a href="{{ route('home') }}" class="navbar-brand">
        <img src="{{ asset('img/logo.png') }}" alt="Serbizyu" class="h-8 w-auto">
    </a>

    <nav class="navbar-nav">
      @foreach($navItems as $item)
        <a 
          href="{{ $item['route'] ? route($item['route']) : '#' }}" 
          class="navbar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}"
        >
          {{ $item['label'] }}
        </a>
      @endforeach

      <div class="navbar-auth">
        @guest
            <a href="{{ route('auth.signin') }}" class="navbar-auth-link {{ request()->routeIs('auth.signin') ? 'active' : '' }}">
                Sign In
            </a>
            <a href="{{ route('auth.join') }}">
                <button class="navbar-auth-button">Join</button>
            </a>
        @endguest

        @auth
    <a href="{{ route('creator.dashboard') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors shrink-0">
        <i data-lucide="layout-dashboard" class="w-5 h-5 md:w-6 md:h-6 text-gray-900 dark:text-gray-100"></i>
    </a>
    <x-nav.profile-dropdown :authProfileData="$authProfileData" />
@endauth
      </div>
    </nav>

    <button @click="open = !open" class="navbar-toggle text-2xl">
      <span x-show="!open">☰</span>
      <span x-show="open">✕</span>
    </button>
  </div>

  <div x-show="open" x-cloak
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 -translate-y-full"
       x-transition:enter-end="opacity-100 translate-y-0"
       class="navbar-mobile dark:bg-background">
       
    <nav class="flex flex-col space-y-2">
      @foreach($navItems as $item)
        <a 
          href="{{ $item['route'] ? route($item['route']) : '#' }}" 
          class="navbar-mobile-link {{ request()->routeIs($item['route']) ? 'active' : '' }}"
        >
          {{ $item['label'] }}
        </a>
      @endforeach

      <div class="navbar-mobile-auth">
        @guest
            <a href="{{ route('auth.signin') }}" class="block text-center navbar-mobile-link">Sign In</a>
            <a href="{{ route('auth.join') }}">
                <button class="navbar-mobile-button w-full">Join</button>
            </a>
        @endguest
        
        @auth
           <div class="border-t pt-2">
            <a href="{{ route('creator.dashboard') }}" class="navbar-creator-link">Creator Space</a>
               <span class="block px-3 py-2 text-sm text-gray-500">{{ $authProfileData->name ?? 'User' }}</span>
               
               <form method="POST" action="{{ route('logout') }}">
                   @csrf
                   <button type="submit" class="w-full text-left px-3 py-2 text-red-600">Logout</button>
               </form>
           </div>
        @endauth
      </div>
    </nav>
  </div>
</header>