<x-app-layout title="Browse Services" cssFiles="browse.css">
  <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">
    <!-- Sticky Search + Filters -->
    <div x-data="filterManager()" x-init="init()"
         class="sticky top-20 bg-gray-50 z-40 p-4 rounded-b-xl shadow-md flex flex-col md:flex-row gap-4">
      
      <!-- Top Row: Search + Filter Toggle (Mobile) -->
      <div class="flex-grow flex items-center justify-between">
        <!-- Search Bar -->
        <div class="flex-grow flex items-center">
          <input type="text" x-model="search" @keydown.enter="filterBySearch" placeholder="Search services or offers..."
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

        <div>
            <label for="desktop_category_filter" class="sr-only">Category</label>
            <select id="desktop_category_filter" @change="filterByCategory" x-model="currentCategory" class="filter-select flex-shrink-0">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Location Filters -->
        <div>
            <label for="desktop_region_filter" class="sr-only">Region</label>
            <select id="desktop_region_filter" x-model="selectedRegion" @change="onRegionChange" class="filter-select flex-shrink-0">
                <option value="">All Regions</option>
                <template x-for="region in regions" :key="region.code">
                    <option :value="region.code" x-text="region.name"></option>
                </template>
            </select>
        </div>
        <div>
            <label for="desktop_province_filter" class="sr-only">Province</label>
            <select id="desktop_province_filter" x-model="selectedProvince" @change="onProvinceChange" class="filter-select flex-shrink-0" :disabled="!selectedRegion || loadingProvinces">
                <option value="">All Provinces</option>
                <template x-for="province in provinces" :key="province.code">
                    <option :value="province.code" x-text="province.name"></option>
                </template>
            </select>
        </div>
        <div>
            <label for="desktop_city_filter" class="sr-only">City</label>
            <select id="desktop_city_filter" x-model="selectedCity" @change="onCityChange" class="filter-select flex-shrink-0" :disabled="!selectedProvince || loadingCities">
                <option value="">All Cities</option>
                <template x-for="city in cities" :key="city.code">
                    <option :value="city.code" x-text="city.name"></option>
                </template>
            </select>
        </div>
      </div>

      <!-- Mobile Dropdown Filters -->
      <div x-show="openFilters"
           x-transition
           @click.away="openFilters = false"
           class="sm:hidden absolute top-full left-0 w-full bg-white border-t border-gray-200 shadow-lg mt-2 rounded-b-xl z-50 p-4 space-y-3">
        <button @click="filterByType('all')" :class="currentType === 'all' ? 'filter-btn-primary' : 'filter-btn-outline'" class="filter-btn w-full">All</button>
        <button @click="filterByType('service')" :class="currentType === 'service' ? 'filter-btn-primary' : 'filter-btn-outline'" class="filter-btn w-full">Services</button>
        <button @click="filterByType('offer')" :class="currentType === 'offer' ? 'filter-btn-primary' : 'filter-btn-outline'" class="filter-btn w-full">Open Offers</button>

        <div>
            <label for="mobile_category_filter" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select id="mobile_category_filter" @change="filterByCategory" x-model="currentCategory" class="filter-select w-full">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Mobile Location Filters -->
        <div>
            <label for="mobile_region_filter" class="block text-sm font-medium text-gray-700 mb-1">Region</label>
            <select id="mobile_region_filter" x-model="selectedRegion" @change="onRegionChange" class="filter-select w-full">
                <option value="">All Regions</option>
                <template x-for="region in regions" :key="region.code">
                    <option :value="region.code" x-text="region.name"></option>
                </template>
            </select>
        </div>
        <div>
            <label for="mobile_province_filter" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
            <select id="mobile_province_filter" x-model="selectedProvince" @change="onProvinceChange" class="filter-select w-full" :disabled="!selectedRegion || loadingProvinces">
                <option value="">All Provinces</option>
                <template x-for="province in provinces" :key="province.code">
                    <option :value="province.code" x-text="province.name"></option>
                </template>
            </select>
        </div>
        <div>
            <label for="mobile_city_filter" class="block text-sm font-medium text-gray-700 mb-1">City/Municipality</label>
            <select id="mobile_city_filter" x-model="selectedCity" @change="onCityChange" class="filter-select w-full" :disabled="!selectedProvince || loadingCities">
                <option value="">All Cities</option>
                <template x-for="city in cities" :key="city.code">
                    <option :value="city.code" x-text="city.name"></option>
                </template>
            </select>
        </div>
      </div>
    </div>

    <script>
        function filterManager() {
            const urlParams = new URLSearchParams(window.location.search);
            return {
                openFilters: false,
                search: urlParams.get('search') || '',
                currentCategory: urlParams.get('category') || '',
                currentType: urlParams.get('type') || 'all',
                
                // Location Properties
                regions: [],
                provinces: [],
                cities: [],
                selectedRegion: '',
                selectedProvince: '',
                selectedCity: urlParams.get('location_code') || '',
                loadingProvinces: false,
                loadingCities: false,

                init() {
                    fetch('/api/addresses/regions')
                        .then(res => res.json())
                        .then(data => {
                            this.regions = data;
                        });
                },

                onRegionChange() {
                    this.provinces = [];
                    this.cities = [];
                    this.selectedProvince = '';
                    this.selectedCity = '';
                    this.updateLocationFilter();
                    if (this.selectedRegion) {
                        this.loadingProvinces = true;
                        fetch(`/api/addresses/regions/${this.selectedRegion}/provinces`)
                            .then(res => res.json())
                            .then(data => {
                                this.provinces = data;
                                this.loadingProvinces = false;
                            });
                    }
                },

                onProvinceChange() {
                    this.cities = [];
                    this.selectedCity = '';
                    this.updateLocationFilter();
                    if (this.selectedProvince) {
                        this.loadingCities = true;
                        fetch(`/api/addresses/provinces/${this.selectedProvince}/cities`)
                            .then(res => res.json())
                            .then(data => {
                                this.cities = data;
                                this.loadingCities = false;
                            });
                    }
                },

                onCityChange() {
                    this.updateLocationFilter();
                },

                updateLocationFilter() {
                    urlParams.set('page', '1');
                    const locationCode = this.selectedCity || this.selectedProvince || this.selectedRegion;
                    if (locationCode) {
                        urlParams.set('location_code', locationCode);
                    } else {
                        urlParams.delete('location_code');
                    }
                    window.location.search = urlParams.toString();
                },
                
                filterBySearch() {
                    urlParams.set('page', '1');
                    if (this.search) {
                        urlParams.set('search', this.search);
                    } else {
                        urlParams.delete('search');
                    }
                    window.location.search = urlParams.toString();
                },

                filterByCategory(event) {
                    const categoryId = event.target.value;
                    urlParams.set('page', '1');
                    if (categoryId) {
                        urlParams.set('category', categoryId);
                    } else {
                        urlParams.delete('category');
                    }
                    window.location.search = urlParams.toString();
                },

                filterByType(type) {
                    urlParams.set('page', '1');
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
