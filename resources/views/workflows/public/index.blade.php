<x-app-layout title="Browse Public Workflows">
  <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">
    
    <!-- Sticky Search + Filters -->
    <div x-data="filterManager()"
         class="sticky top-20 bg-gray-50 z-40 p-4 rounded-b-xl shadow-md flex flex-col md:flex-row gap-4">
      
      <!-- Top Row: Search + Filter Toggle (Mobile) -->
      <div class="flex-grow flex items-center justify-between">
        <!-- Search Bar -->
        <div class="flex-grow flex items-center">
          <input type="text" x-model="search" @keydown.enter="filterBySearch" placeholder="Search public workflows..."
            class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600 md:text-base text-xs" />
        </div>

        <!-- Filter Toggle (Mobile only) -->
        <button @click="openFilters = !openFilters"
                class="sm:hidden ml-3 bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition">
          <x-icons.filter class="w-5 h-5" />
        </button>
      </div>

      <!-- Scrollable Filters (Desktop) -->
      <div class="hidden sm:flex gap-2 overflow-x-auto scrollbar-hide text-xs md:text-base">
        <select @change="filterByCategory" x-model="currentCategory" class="filter-select flex-shrink-0  rounded-lg">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
      </div>

      <!-- Mobile Dropdown Filters -->
      <div x-show="openFilters"
           x-transition
           @click.away="openFilters = false"
           class="sm:hidden absolute top-full left-0 w-full bg-white border-t border-gray-200 shadow-lg mt-2 rounded-b-xl z-50 p-4 space-y-3">
        <select @change="filterByCategory" x-model="currentCategory" class="filter-select w-full">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
      </div>
    </div>

    <script>
        function filterManager() {
            const urlParams = new URLSearchParams(window.location.search);
            return {
                openFilters: false,
                search: urlParams.get('search') || '',
                currentCategory: urlParams.get('category') || '',
                
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
                }
            }
        }
    </script>

    <!-- Session Messages -->
    <div class="py-6">
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-200 text-green-800 rounded-md p-3 text-sm mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('info'))
            <div class="bg-blue-100 border border-blue-200 text-blue-800 rounded-md p-3 text-sm mb-4">
                {{ session('info') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-200 text-red-800 rounded-md p-3 text-sm mb-4">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Workflow Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($workflows as $workflow)
            @include('workflows.partials.workflow-card', ['workflow' => $workflow])
        @empty
            <p class="text-center text-gray-500 col-span-full">No public workflows found matching your criteria.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $workflows->links() }}
    </div>
  </div>
</x-app-layout>
