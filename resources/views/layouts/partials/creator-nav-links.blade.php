<!-- Item 1: Dashboard -->
<a href="{{ route('creator.dashboard') }}" class="group flex items-center gap-4 md:justify-center lg:justify-start w-full">
    <div class="w-12 h-12 border rotate-45 flex items-center justify-center transition-colors duration-300 shadow-sm shrink-0
        {{ request()->routeIs('creator.dashboard') ? 'bg-gray-800 border-gray-800' : 'bg-white border-gray-300 group-hover:bg-gray-800 group-hover:border-gray-800' }}">
        <i data-lucide="layout-dashboard" class="-rotate-45 w-5 h-5 {{ request()->routeIs('creator.dashboard') ? 'text-white' : 'text-gray-600 group-hover:text-white' }}"></i>
    </div>
    <span class="font-medium md:hidden lg:block whitespace-nowrap {{ request()->routeIs('creator.dashboard') ? 'text-gray-900' : 'text-gray-600 group-hover:text-gray-900' }}">Dashboard</span>
</a>

<!-- Item 2: Services -->
<a href="{{ route('creator.services.index') }}" class="group flex items-center gap-4 md:justify-center lg:justify-start w-full">
    <div class="w-12 h-12 border rotate-45 flex items-center justify-center transition-colors duration-300 shadow-sm shrink-0
        {{ request()->routeIs('creator.services.*') ? 'bg-gray-800 border-gray-800' : 'bg-white border-gray-300 group-hover:bg-gray-800 group-hover:border-gray-800' }}">
        <i data-lucide="briefcase" class="-rotate-45 w-5 h-5 {{ request()->routeIs('creator.services.*') ? 'text-white' : 'text-gray-600 group-hover:text-white' }}"></i>
    </div>
    <span class="font-medium md:hidden lg:block whitespace-nowrap {{ request()->routeIs('creator.services.*') ? 'text-gray-900' : 'text-gray-600 group-hover:text-gray-900' }}">Services</span>
</a>

<!-- Item 3: Open Offers -->
<div class="relative group flex items-center gap-4 md:justify-center lg:justify-start w-full cursor-pointer"
     @click="togglePopover('bids')">
    <a href="{{ route('creator.openoffers.index') }}" class="w-12 h-12 border rotate-45 flex items-center justify-center hover:bg-gray-800 hover:border-gray-800 transition-colors duration-300 shadow-sm shrink-0
         {{ request()->routeIs('creator.openoffers.*') ? 'bg-gray-800 border-gray-800' : '' }}"
         :class="{'bg-gray-800 border-gray-800': activePopover === 'bids'}">
        <i data-lucide="files" class="-rotate-45 w-5 h-5 {{ request()->routeIs('creator.openoffers.*') ? 'text-white' : 'text-gray-600' }}" :class="{'text-white': activePopover === 'bids'}"></i>
    </a>

    <div class="flex flex-col md:hidden lg:flex whitespace-nowrap">
        <span class="font-medium text-gray-600 group-hover:text-gray-900">Open Offers</span>
        <span class="text-xs text-gray-400 pl-2 border-l-2 border-gray-300 ml-0.5 mt-1">↳ Bids</span>
    </div>

    <!-- Popover -->
    <div x-show="activePopover === 'bids'"
         @click.away="activePopover = null"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-x-2"
         x-transition:enter-end="opacity-100 translate-x-0"
         class="absolute left-[4.5rem] top-0 z-[100] bg-white border border-gray-200 shadow-xl rounded-2xl p-4 w-48 md:block lg:hidden hidden">
        <h3 class="font-bold text-gray-800 mb-2">Bids</h3>
        <ul class="space-y-2 text-sm text-gray-600">
            <li class="hover:bg-gray-50 p-1 rounded cursor-pointer">View active bids</li>
            <li class="hover:bg-gray-50 p-1 rounded cursor-pointer">Create new offer</li>
        </ul>
        <div class="absolute top-4 -left-2 w-4 h-4 bg-white border-l border-b border-gray-200 transform rotate-45"></div>
    </div>
</div>

<!-- Item 4: Orders -->
<a href="{{ route('orders.index') }}" class="group flex items-center gap-4 md:justify-center lg:justify-start w-full">
    <div class="w-12 h-12 border rotate-45 flex items-center justify-center transition-colors duration-300 shadow-sm shrink-0
        {{ request()->routeIs('orders.*') ? 'bg-gray-800 border-gray-800' : 'bg-white border-gray-300 group-hover:bg-gray-800 group-hover:border-gray-800' }}">
        <i data-lucide="shopping-cart" class="-rotate-45 w-5 h-5 {{ request()->routeIs('orders.*') ? 'text-white' : 'text-gray-600 group-hover:text-white' }}"></i>
    </div>
    <span class="font-medium md:hidden lg:block whitespace-nowrap {{ request()->routeIs('orders.*') ? 'text-gray-900' : 'text-gray-600 group-hover:text-gray-900' }}">Orders</span>
</a>

<!-- Item 5: Work/Orders (Integrated) -->
<a href="{{ route('orders.index') }}" class="group flex items-center gap-4 md:justify-center lg:justify-start w-full">
    <div class="w-12 h-12 border rotate-45 flex items-center justify-center transition-colors duration-300 shadow-sm shrink-0
        {{ request()->routeIs('orders.work.*') || request()->routeIs('orders.show') ? 'bg-gray-800 border-gray-800' : 'bg-white border-gray-300 group-hover:bg-gray-800 group-hover:border-gray-800' }}">
        <i data-lucide="zap" class="-rotate-45 w-5 h-5 {{ request()->routeIs('orders.work.*') || request()->routeIs('orders.show') ? 'text-white' : 'text-gray-600 group-hover:text-white' }}"></i>
    </div>
    <span class="font-medium md:hidden lg:block whitespace-nowrap {{ request()->routeIs('orders.work.*') || request()->routeIs('orders.show') ? 'text-gray-900' : 'text-gray-600 group-hover:text-gray-900' }}">Work/Orders</span>
</a>

<!-- Item 6: Workflows -->
<a href="{{ route('creator.workflows.index') }}" class="group flex items-center gap-4 md:justify-center lg:justify-start w-full">
    <div class="w-12 h-12 border rotate-45 flex items-center justify-center transition-colors duration-300 shadow-sm shrink-0
        {{ request()->routeIs('workflows.*') ? 'bg-gray-800 border-gray-800' : 'bg-white border-gray-300 group-hover:bg-gray-800 group-hover:border-gray-800' }}">
        <i data-lucide="git-branch" class="-rotate-45 w-5 h-5 {{ request()->routeIs('workflows.*') ? 'text-white' : 'text-gray-600 group-hover:text-white' }}"></i>
    </div>
    <span class="font-medium md:hidden lg:block whitespace-nowrap {{ request()->routeIs('workflows.*') ? 'text-gray-900' : 'text-gray-600 group-hover:text-gray-900' }}">Workflows</span>
</a>
