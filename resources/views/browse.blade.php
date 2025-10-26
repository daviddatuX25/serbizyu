<x-app-layout title="Browse Services" cssFiles="browse.css">
  <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">
    <!-- Sticky Search + Filters -->
    <div x-data="{ openFilters: false }"
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
        <button class="filter-btn filter-btn-primary flex-shrink-0">All</button>
        <button class="filter-btn filter-btn-outline flex-shrink-0">Services</button>
        <button class="filter-btn filter-btn-outline flex-shrink-0">Open Offers</button>

        <select class="filter-select flex-shrink-0">
          <option>All Categories</option>
          <option>Catering</option>
          <option>Construction</option>
          <option>Repair</option>
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
        <button class="filter-btn filter-btn-primary w-full">All</button>
        <button class="filter-btn filter-btn-outline w-full">Services</button>
        <button class="filter-btn filter-btn-outline w-full">Open Offers</button>

        <select class="filter-select w-full">
          <option>All Categories</option>
          <option>Catering</option>
          <option>Construction</option>
          <option>Repair</option>
        </select>

        <select class="filter-select w-full">
          <option>All Locations</option>
          <option>Tagudin</option>
          <option>Sta. Cruz</option>
          <option>Candon</option>
        </select>
      </div>
    </div>

    <!-- Card Grid -->
    <div class="browse-grid">
      <!-- Service Card -->
      <article class="listing-card">
        <div class="card-top">
          <span class="badge-service">Service</span>
          <span class="rating">★★★★★</span>
        </div>
        <h3 class="card-title">Arnel’s Plumbing</h3>
        <p class="card-desc">Diagnose &gt; Buy materials &gt; On field &gt; Finish</p>
        <p class="card-meta">Rate: ₱200/hr</p>
        <p class="card-meta">Location: Tagudin, Ilocos Sur</p>
        <div class="card-footer">
          <span class="text-xs text-text-secondary">Verified Servicer</span>
          <div class="card-avatar">👤</div>
        </div>
      </article>

      <!-- Open Offer Card -->
      <article class="listing-card">
        <div class="card-top">
          <span class="badge-offer">Open Offer</span>
          <span class="text-sm text-text-secondary">Budget: ₱5,000</span>
        </div>
        <h3 class="card-title">Looking for Catering Service</h3>
        <p class="card-desc">Event for 50 guests</p>
        <p class="card-meta">Sta. Cruz, Ilocos Sur</p>
        <div class="card-footer">
          <span class="text-xs text-text-secondary">Posted 2h ago</span>
          <div class="card-avatar">📝</div>
        </div>
      </article>

      <!-- Service Card -->
      <article class="listing-card">
        <div class="card-top">
          <span class="badge-service">Service</span>
          <span class="rating">★★★★☆</span>
        </div>
        <h3 class="card-title">General Home Repairs</h3>
        <p class="card-desc">Diagnose &gt; Buy materials &gt; On field &gt; Finish</p>
        <p class="card-meta">Rate: ₱150/hr</p>
        <p class="card-meta">Candon, Ilocos Sur</p>
        <div class="card-footer">
          <span class="text-xs text-text-secondary">Trusted Local</span>
          <div class="card-avatar">👤</div>
        </div>
      </article>

      <!-- Open Offer Card -->
      <article class="listing-card">
        <div class="card-top">
          <span class="badge-offer">Open Offer</span>
          <span class="text-sm text-text-secondary">Budget: ₱12,000</span>
        </div>
        <h3 class="card-title">Need House Painting</h3>
        <p class="card-desc">2-story house, labor only</p>
        <p class="card-meta">Luna, La Union</p>
        <div class="card-footer">
          <span class="text-xs text-text-secondary">Posted 1d ago</span>
          <div class="card-avatar">📝</div>
        </div>
      </article>
    </div>
  </div>
</x-app-layout>

