<x-app-layout title="Browse Services" cssFiles="browse.css">
  <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">
    <!-- Sticky Search + Filters -->
    <div x-data="filterManager()"
         class="sticky top-20 bg-gray-50 z-40 p-4 rounded-b-xl shadow-md flex flex-col md:flex-row gap-4">
      
      <!-- Top Row: Search + Filter Toggle (Mobile) -->
      <div class="flex-grow flex items-center justify-between">
        <!-- Search Bar -->
        <div class="flex-grow flex items-center">
          <input type="text" placeholder="Search services or offers..."
            class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600 md:text-base text-xs" />
        </div>

        <!-- Filter Toggle (Mobile only) -->
        <button @click="openFilters = !openFilters"
                class="sm:hidden ml-3 bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition">
          <x-icons.filter class="w-5 h-5" />
        </button>
      </div>

      <!-- Scrollable Filters -->
      <div class="hidden sm:flex gap-2 overflow-x-auto scrollbar-hide text-xs md:text-base">
        <button @click="filterByType('all')" :class="currentType === 'all' ? 'filter-btn-primary' : 'filter-btn-outline'" class="filter-btn flex-shrink-0">All</button>
        <button @click="filterByType('service')" :class="currentType === 'service' ? 'filter-btn-primary' : 'filter-btn-outline'" class="filter-btn flex-shrink-0">Services</button>
        <button @click="filterByType('offer')" :class="currentType === 'offer' ? 'filter-btn-primary' : 'filter-btn-outline'" class="filter-btn flex-shrink-0">Open Offers</button>

        <select @change="filterByCategory" x-model="currentCategory" class="filter-select flex-shrink-0">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <select class="filter-select flex-shrink-0">
          <option>All Locations</option>
          <option>Tagudin</option>
          <option>Sta. Cruz</option>
          <option>Candon</option>
        </select>
      </div>

      <!-- Mobile Dropdown Filters -->
      <div x-show="openFilters"
           x-transition
           @click.away="openFilters = false"
           class="sm:hidden absolute top-full left-0 w-full bg-white border-t border-gray-200 shadow-lg mt-2 rounded-b-xl z-50 p-4 space-y-3">
        <button @click="filterByType('all')" :class="currentType === 'all' ? 'filter-btn-primary' : 'filter-btn-outline'" class="filter-btn w-full">All</button>
        <button @click="filterByType('service')" :class="currentType === 'service' ? 'filter-btn-primary' : 'filter-btn-outline'" class="filter-btn w-full">Services</button>
        <button @click="filterByType('offer')" :class="currentType === 'offer' ? 'filter-btn-primary' : 'filter-btn-outline'" class="filter-btn w-full">Open Offers</button>

        <select @change="filterByCategory" x-model="currentCategory" class="filter-select w-full">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <select class="filter-select w-full">
          <option>All Locations</option>
          <option>Tagudin</option>
          <option>Sta. Cruz</option>
          <option>Candon</option>
        </select>
      </div>
    </div>

    <script>
        function filterManager() {
            const urlParams = new URLSearchParams(window.location.search);
            return {
                openFilters: false,
                currentCategory: urlParams.get('category') || '',
                currentType: urlParams.get('type') || 'all',
                filterByCategory(event) {
                    const categoryId = event.target.value;
                    urlParams.set('page', '1'); // Reset to page 1 when filter changes
                    if (categoryId) {
                        urlParams.set('category', categoryId);
                    } else {
                        urlParams.delete('category');
                    }
                    window.location.search = urlParams.toString();
                },
                filterByType(type) {
                    urlParams.set('page', '1'); // Reset to page 1 when filter changes
                    if (type === 'all') {
                        urlParams.delete('type');
                    } else {
                        urlParams.set('type', type);
                    }
                    window.location.search = urlParams.toString();
                }
            }
        }
    </script>

    <!-- Card Grid -->
    <div class="browse-grid">
        @forelse($listings as $listing)
            @if($listing instanceof \App\Domains\Listings\Models\Service)
                @include('listings.partials.service-card', ['service' => $listing])
            @elseif($listing instanceof \App\Domains\Listings\Models\OpenOffer)
                @include('listings.partials.offer-card', ['offer' => $listing])
            @endif
        @empty
            <p class="text-center text-gray-500 col-span-full">No listings found at the moment.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $listings->links() }}
    </div>
  </div>
</x-app-layout>
