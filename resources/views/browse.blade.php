<x-app-layout title="Browse Services">
  <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">
    <!-- Sticky Search + Filters -->
    <div class="sticky top-20 bg-gray-50 z-40 p-4 rounded-b-xl shadow-md flex flex-col md:flex-row gap-4">

      <!-- Top Row: Search + Filter Toggle (Mobile) -->
      <div class="flex-grow flex items-center justify-between">
        <div class="flex-grow flex items-center">
          <form id="searchForm" class="w-full flex">
            <input type="text"
                   id="searchInput"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search services or offers..."
                   class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-600 md:text-base text-xs" />
          </form>
        </div>

        <!-- Mobile filter toggle -->
        <button onclick="document.getElementById('mobileFilters').classList.toggle('hidden')"
                class="sm:hidden ml-3 bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition">
          <x-icons.filter class="w-5 h-5" />
        </button>
      </div>

      <!-- Desktop Filters -->
      <div class="hidden sm:flex gap-2 overflow-x-auto scrollbar-hide text-xs md:text-base">
        <button onclick="setFilter('type', 'all')"
                id="typeAll"
                class="filter-btn flex-shrink-0 {{ request('type', 'all') === 'all' ? 'filter-btn-primary' : 'filter-btn-outline' }}">All</button>

        <button onclick="setFilter('type', 'service')"
                id="typeService"
                class="filter-btn flex-shrink-0 {{ request('type') === 'service' ? 'filter-btn-primary' : 'filter-btn-outline' }}">Services</button>

        <button onclick="setFilter('type', 'offer')"
                id="typeOffer"
                class="filter-btn flex-shrink-0 {{ request('type') === 'offer' ? 'filter-btn-primary' : 'filter-btn-outline' }}">Open Offers</button>

        <div>
            <select id="categoryFilter" onchange="setFilter('category', this.value)" class="filter-select flex-shrink-0">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Region -->
        <div>
            <select id="regionFilter" onchange="handleRegionChange(this.value)" class="filter-select flex-shrink-0">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region['code'] }}" {{ request('region') == $region['code'] ? 'selected' : '' }}>{{ $region['name'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Province -->
        <div>
            <select id="provinceFilter" onchange="handleProvinceChange(this.value)" class="filter-select flex-shrink-0" {{ empty(request('region')) ? 'disabled' : '' }}>
                <option value="">All Provinces</option>
            </select>
        </div>

        <!-- City -->
        <div>
            <select id="cityFilter" onchange="handleCityChange(this.value)" class="filter-select flex-shrink-0" {{ empty(request('province')) && empty(request('region')) ? 'disabled' : '' }}>
                <option value="">All Cities</option>
            </select>
        </div>
      </div>

      <!-- Mobile Filters -->
      <div id="mobileFilters" class="sm:hidden hidden absolute top-full left-0 w-full bg-white border-t shadow-lg mt-2 rounded-b-xl z-50 p-4 space-y-3">
        <button onclick="setFilter('type', 'all')" class="filter-btn w-full {{ request('type', 'all') === 'all' ? 'filter-btn-primary' : 'filter-btn-outline' }}">All</button>
        <button onclick="setFilter('type', 'service')" class="filter-btn w-full {{ request('type') === 'service' ? 'filter-btn-primary' : 'filter-btn-outline' }}">Services</button>
        <button onclick="setFilter('type', 'offer')" class="filter-btn w-full {{ request('type') === 'offer' ? 'filter-btn-primary' : 'filter-btn-outline' }}">Open Offers</button>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select id="categoryFilterMobile" onchange="setFilter('category', this.value)" class="filter-select w-full">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
            <select id="regionFilterMobile" onchange="handleRegionChange(this.value)" class="filter-select w-full">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region['code'] }}" {{ request('region') == $region['code'] ? 'selected' : '' }}>{{ $region['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
            <select id="provinceFilterMobile" onchange="handleProvinceChange(this.value)" class="filter-select w-full" {{ empty(request('region')) ? 'disabled' : '' }}>
                <option value="">All Provinces</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
            <select id="cityFilterMobile" onchange="handleCityChange(this.value)" class="filter-select w-full" {{ empty(request('province')) && empty(request('region')) ? 'disabled' : '' }}>
                <option value="">All Cities</option>
            </select>
        </div>
      </div>
    </div>

    <script>
        const regionData = @json($regions);
        let provincesCache = {};
        let citiesCache = {};

        function applyFilter() {
            const params = new URLSearchParams(window.location.search);
            const searchValue = document.getElementById('searchInput')?.value;

            if (searchValue) {
                params.set('search', searchValue);
            } else {
                params.delete('search');
            }

            params.set('page', '1');
            window.location.search = params.toString();
        }

        function setFilter(key, value, dependentParams = []) {
            const params = new URLSearchParams(window.location.search);

            // Remove dependent params
            dependentParams.forEach(param => params.delete(param));

            if (value === '' || value === 'all') {
                params.delete(key);
                if (key === 'type') {
                    params.delete('type');
                }
            } else {
                params.set(key, value);
            }
            params.set('page', '1');
            window.location.search = params.toString();
        }

        async function fetchProvinces(regionCode) {
            if (!regionCode) return [];
            if (provincesCache[regionCode]) {
                return provincesCache[regionCode];
            }

            try {
                const response = await fetch(`/api/addresses/regions/${regionCode}/provinces`);
                const data = await response.json();
                provincesCache[regionCode] = data || [];
                return provincesCache[regionCode];
            } catch (error) {
                console.error('Error fetching provinces:', error);
                return [];
            }
        }

        async function fetchCities(provinceCode) {
            if (!provinceCode) return [];
            if (citiesCache[provinceCode]) {
                return citiesCache[provinceCode];
            }

            try {
                const response = await fetch(`/api/addresses/provinces/${provinceCode}/cities`);
                const data = await response.json();
                citiesCache[provinceCode] = data || [];
                return citiesCache[provinceCode];
            } catch (error) {
                console.error('Error fetching cities:', error);
                return [];
            }
        }

        async function handleRegionChange(value) {
            // Only process if value actually changed
            if (!value) {
                // Clear all dependent selects
                clearDependentSelects();
                // Remove region, province, and city from query params
                removeFilters(['region', 'province', 'city']);
                return;
            }

            // Sync both desktop and mobile selects
            document.getElementById('regionFilter').value = value;
            document.getElementById('regionFilterMobile').value = value;

            // Clear province and city
            document.getElementById('provinceFilter').value = '';
            document.getElementById('provinceFilterMobile').value = '';
            document.getElementById('cityFilter').value = '';
            document.getElementById('cityFilterMobile').value = '';

            // Fetch and populate provinces
            const provinces = await fetchProvinces(value);
            const provinceHtml = provinces.map(p => `<option value="${p.code}">${p.name}</option>`).join('');
            document.getElementById('provinceFilter').innerHTML = '<option value="">All Provinces</option>' + provinceHtml;
            document.getElementById('provinceFilterMobile').innerHTML = '<option value="">All Provinces</option>' + provinceHtml;

            // Enable province select
            document.getElementById('provinceFilter').disabled = false;
            document.getElementById('provinceFilterMobile').disabled = false;

            // Apply filter and remove dependent params
            setFilter('region', value, ['province', 'city']);
        }

        function clearDependentSelects() {
            document.getElementById('provinceFilter').innerHTML = '<option value="">All Provinces</option>';
            document.getElementById('provinceFilterMobile').innerHTML = '<option value="">All Provinces</option>';
            document.getElementById('cityFilter').innerHTML = '<option value="">All Cities</option>';
            document.getElementById('cityFilterMobile').innerHTML = '<option value="">All Cities</option>';

            document.getElementById('provinceFilter').disabled = true;
            document.getElementById('provinceFilterMobile').disabled = true;
            document.getElementById('cityFilter').disabled = true;
            document.getElementById('cityFilterMobile').disabled = true;
        }

        async function handleProvinceChange(value) {
            // Only process if value actually changed
            if (!value) {
                document.getElementById('cityFilter').innerHTML = '<option value="">All Cities</option>';
                document.getElementById('cityFilterMobile').innerHTML = '<option value="">All Cities</option>';
                document.getElementById('cityFilter').disabled = true;
                document.getElementById('cityFilterMobile').disabled = true;
                // Remove province and city from query params
                removeFilters(['province', 'city']);
                return;
            }

            // Sync both desktop and mobile selects
            document.getElementById('provinceFilter').value = value;
            document.getElementById('provinceFilterMobile').value = value;

            // Clear city
            document.getElementById('cityFilter').value = '';
            document.getElementById('cityFilterMobile').value = '';

            // Fetch and populate cities
            const cities = await fetchCities(value);
            const cityHtml = cities.map(c => `<option value="${c.code}">${c.name}</option>`).join('');
            document.getElementById('cityFilter').innerHTML = '<option value="">All Cities</option>' + cityHtml;
            document.getElementById('cityFilterMobile').innerHTML = '<option value="">All Cities</option>' + cityHtml;

            // Enable city select
            document.getElementById('cityFilter').disabled = false;
            document.getElementById('cityFilterMobile').disabled = false;

            // Apply filter and remove dependent city param
            setFilter('province', value, ['city']);
        }

        function handleCityChange(value) {
            // Sync both desktop and mobile selects
            document.getElementById('cityFilter').value = value;
            document.getElementById('cityFilterMobile').value = value;

            setFilter('city', value);
        }

        function removeFilters(filterNames) {
            const params = new URLSearchParams(window.location.search);
            filterNames.forEach(name => params.delete(name));
            params.set('page', '1');
            window.location.search = params.toString();
        }

        // Initialize provinces and cities if region is selected
        window.addEventListener('DOMContentLoaded', async function() {
            const regionValue = "{{ $selectedRegion }}";
            const provinceValue = "{{ $selectedProvince }}";
            const cityValue = "{{ $selectedCity }}";

            if (regionValue) {
                const provinces = await fetchProvinces(regionValue);
                const provinceHtml = provinces.map(p => `<option value="${p.code}" ${p.code === provinceValue ? 'selected' : ''}>${p.name}</option>`).join('');
                document.getElementById('provinceFilter').innerHTML = '<option value="">All Provinces</option>' + provinceHtml;
                document.getElementById('provinceFilterMobile').innerHTML = '<option value="">All Provinces</option>' + provinceHtml;
                document.getElementById('provinceFilter').disabled = false;
                document.getElementById('provinceFilterMobile').disabled = false;

                if (provinceValue) {
                    const cities = await fetchCities(provinceValue);
                    const cityHtml = cities.map(c => `<option value="${c.code}" ${c.code === cityValue ? 'selected' : ''}>${c.name}</option>`).join('');
                    document.getElementById('cityFilter').innerHTML = '<option value="">All Cities</option>' + cityHtml;
                    document.getElementById('cityFilterMobile').innerHTML = '<option value="">All Cities</option>' + cityHtml;
                    document.getElementById('cityFilter').disabled = false;
                    document.getElementById('cityFilterMobile').disabled = false;
                }
            }
        });
    </script>

    <!-- Cards -->
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

    <div class="mt-8">
        {{ $listings->links() }}
    </div>

  </div>
</x-app-layout>
