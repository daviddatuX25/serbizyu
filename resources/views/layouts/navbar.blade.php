@props([
  'navItems' => [
    ['label' => 'Home', 'route' => 'home'],
    ['label' => 'Browse', 'route' => 'browse'],
    ['label' => 'FAQ', 'route' => 'faq'],
    ['label' => 'About', 'route' => 'about']
  ]
])

<header class="navbar" x-data="{ open: false }">
  <div class="navbar-inner">
    
    <!-- Brand -->
    <a href="{{ route('home') }}" class="navbar-brand">
      <span class="navbar-brand-text">Serbizyu</span>
      <span class="navbar-brand-accent">.</span>
    </a>

    <!-- Desktop Nav -->
    <nav class="navbar-nav">
      @foreach($navItems as $item)
        @php $route = $item['route']; @endphp
        <a 
          href="{{ $route ? route($route) : '#' }}" 
          class="navbar-link {{ request()->routeIs($route) ? 'active' : '' }}"
        >
          {{ $item['label'] }}
        </a>
      @endforeach

      {{-- show only when on auth check else show profile bar with logout button --}}
      <div class="navbar-auth">
        @guest
            <a href="{{ route('auth.signin') }}" 
              class="navbar-auth-link {{ request()->routeIs('auth.signin') ? 'active' : '' }}">
                Sign In
            </a>
            <a href="{{ route('auth.join') }}">
                <button class="navbar-auth-button">Join</button>
            </a>
        @endguest

       @auth
            <x-nav.profile-dropdown :authProfileData="$authProfileData" />
        @endauth


      </div>
    </nav>

    <!-- Mobile Hamburger -->
    <button @click="open = !open" class="navbar-toggle text-2xl">
      <span x-show="!open">☰</span>
      <span x-show="open">✕</span>
    </button>
  </div>

  <!-- Mobile Menu -->
  <div
    x-show="open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-full"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-full"
    class="navbar-mobile"
  >
    <nav class="flex flex-col space-y-2">
      @foreach($navItems as $item)
        @php $route = $item['route']; @endphp
        <a 
          href="{{ $route ? route($route) : '#' }}" 
          class="navbar-mobile-link {{ request()->routeIs($route) ? 'active' : '' }}"
        >
          {{ $item['label'] }}
        </a>
      @endforeach

      <div class="navbar-mobile-auth">
        @guest
            <a href="{{ route('auth.signin') }}" 
              class="block text-center navbar-mobile-link {{ request()->routeIs('auth.signin') ? 'active' : '' }}">
                Sign In
            </a>
            <a href="{{ route('auth.join') }}">
                <button class="navbar-mobile-button">Join</button>
            </a>
        @endguest

       @auth
          <x-nav.profile-dropdown :authProfileData="$authProfileData"/>
      @endauth
      </div>
    </nav>
  </div>
</header>
