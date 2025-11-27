@props(['authProfileData'])

<div x-data="{ open: false }" class="relative">
  {{-- Desktop trigger + dropdown (visible on md and up) --}}
  <div class="hidden md:flex items-center">
    <button @click="open = !open" class="navbar-profile-trigger" aria-expanded="false">
      @if($authProfileData?->media() && $authProfileData->media()->where('tag', 'profile_image')->first())
        <img src="{{ $authProfileData->media()->where('tag', 'profile_image')->first()->getUrl() }}" alt="Profile" class="navbar-profile-img" />
      @else
          <div class="bg-brand-100">
            <x-icons.profile class="w-8 h-8 text-green-500" />
        </div>
      @endif
    </button>

    {{-- dropdown (desktop) --}}
    <div
      x-show="open"
      @click.away="open = false"
      x-cloak
      x-transition
      class="navbar-profile-dropdown"
      style="display: none;"
    >
      <div class="px-4 py-2 border-b">
        <span class="font-semibold text-sm">Hi, {{ $authProfileData?->firstname ?? 'User' }}!</span>
        @if($authProfileData?->email)
          <div class="text-xs text-text-secondary truncate">{{ $authProfileData->email }}</div>
        @endif
      </div>

      <a href="{{ route('profile.edit') }}" class="navbar-profile-item">Profile</a>
      <a href="{{ route('creator.dashboard') }}" class="navbar-profile-item">Creator Space</a>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="navbar-profile-item w-full text-left">Logout</button>
      </form>
    </div>
  </div>

  {{-- Mobile stacked UI (visible under md) --}}
  <div class="md:hidden">
    <div class="flex flex-col items-center space-y-3">
      {{-- profile icon --}}
      <div>
        @if($authProfileData?->media() && $authProfileData->media()->where('tag', 'profile_image')->first())
          <img src="{{ $authProfileData->media()->where('tag', 'profile_image')->first()->getUrl() }}" alt="Profile" class="navbar-profile-img" />
        @else
            <a class="p-5 bg-brand-100" href="{{ route('profile.edit') }}" class="w-full">
                <x-icons.profile class="w-6 h-6 text-green-500" />
            </a>
        @endif
      </div>



      <a href="{{ route('creator.dashboard') }}" class="w-full">
        <button class="navbar-creator-btn w-full">Creator Space</button>
      </a>

      <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit" class="navbar-mobile-button w-full">Logout</button>
      </form>
    </div>
  </div>
</div>
