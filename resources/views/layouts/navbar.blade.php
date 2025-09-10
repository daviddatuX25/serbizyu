<header class="navbar bg-white shadow-md" x-data="{ open: false }">
  <div class="navbar-inner max-w-7xl mx-auto flex items-center justify-between px-6 py-4">

    <!-- Brand -->
    <a href="{{ route('home') }}" class="navbar-brand flex items-center">
      <span class="navbar-brand-text text-2xl font-bold">Serbizyu</span>
      <span class="navbar-brand-accent text-green-600 text-3xl ml-1">.</span>
    </a>

    <!-- Desktop Nav -->
    <nav class="hidden md:flex items-center space-x-6">
      <a href="{{ route('home') }}" 
         class="navbar-link {{ request()->routeIs('home') ? 'active' : '' }}">
         Home
      </a>
      <a href="{{ route('browse') }}" 
         class="navbar-link {{ request()->routeIs('browse') ? 'active' : '' }}">
         Browse
      </a>
      <a href="{{ route('create') }}" 
         class="navbar-link {{ request()->routeIs('create') ? 'active' : '' }}">
         Create
      </a>
      <a href="{{ route('faq') }}" 
         class="navbar-link {{ request()->routeIs('faq') ? 'active' : '' }}">
         FAQ
      </a>

      <div class="flex items-center space-x-3 ml-6">
        <a href="{{ route('signin') }}" class="navbar-auth-link">Sign In</a>
        <a href="{{ route('signup') }}">
          <button class="navbar-auth-button border-none bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
            Join
          </button>
        </a>
      </div>
    </nav>

    <!-- Mobile Hamburger -->
    <button @click="open = !open" class="md:hidden text-2xl focus:outline-none">
      <span x-show="!open">☰</span>
      <span x-show="open">✕</span>
    </button>
  </div>

  <!-- Mobile Menu -->
  <div x-show="open" x-transition class="md:hidden bg-white shadow-md">
    <nav class="flex flex-col space-y-2 px-4 py-4">
      <a href="{{ route('home') }}" class="navbar-mobile-link {{ request()->routeIs('home') ? 'active' : '' }}">
        Home
      </a>
      <a href="{{ route('browse') }}" class="navbar-mobile-link {{ request()->routeIs('browse') ? 'active' : '' }}">
        Browse
      </a>
      <a href="{{ route('create') }}" class="navbar-mobile-link {{ request()->routeIs('create') ? 'active' : '' }}">
        Create
      </a>
      <a href="{{ route('faq') }}" class="navbar-mobile-link {{ request()->routeIs('faq') ? 'active' : '' }}">
        FAQ
      </a>

      <div class="flex flex-col mt-4 space-y-2">
        <a href="{{ route('signin') }}" class="block text-center text-gray-700 hover:text-green-600">Sign In</a>
        <a href="{{ route('signup') }}">
          <button class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700 transition">
            Join
          </button>
        </a>
      </div>
    </nav>
  </div>
</header>
