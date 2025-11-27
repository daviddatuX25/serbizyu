<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search Results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('search.index') }}" method="GET" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div x-data="{ query: '{{ $query }}', suggestions: [], open: false }" @click.away="open = false">
    <input type="text" name="query" x-model="query" @input.debounce.300ms="
        fetch(`{{ route('search.suggestions') }}?query=${query}`)
            .then(response => response.json())
            .then(data => {
                suggestions = data;
                open = true;
            })
    " placeholder="Search for services..." class="md:col-span-2 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" autocomplete="off">
    <div x-show="open && suggestions.length > 0" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1">
        <template x-for="suggestion in suggestions" :key="suggestion">
            <a @click.prevent="query = suggestion; open = false" href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" x-text="suggestion"></a>
        </template>
    </div>
</div>

                            <select name="category_id" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected($categoryId == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <input type="text" name="address_id" value="{{ $addressId }}" placeholder="Address ID" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                            <input type="number" name="min_price" value="{{ $minPrice }}" placeholder="Min Price" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                            <input type="number" name="max_price" value="{{ $maxPrice }}" placeholder="Max Price" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                            <select name="sort_by" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="created_at" @selected($sortBy == 'created_at')>Date</option>
                                <option value="price" @selected($sortBy == 'price')>Price</option>
                                <option value="title" @selected($sortBy == 'title')>Title</option>
                            </select>

                            <select name="sort_direction" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="desc" @selected($sortDirection == 'desc')>Descending</option>
                                <option value="asc" @selected($sortDirection == 'asc')>Ascending</option>
                            </select>

                            <button type="submit" class="md:col-span-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Search</button>
                        </div>
                    </form>

                    @if ($services->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($services as $service)
                                <div class="bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                                    <!-- Service Image -->
                                    <div class="relative h-48 bg-gray-200">
                                        @if($service->media->isNotEmpty())
                                            <img src="{{ $service->media->first()->getUrl() }}"
                                                alt="{{ $service->title }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center h-full">
                                                <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Service Details -->
                                    <div class="p-4 space-y-3">
                                        <h3 class="text-lg font-bold text-gray-800 line-clamp-2">{{ $service->title }}</h3>

                                        <div class="flex items-center justify-between text-sm mb-2">
                                            <span class="text-gray-600">{{ $service->category->name ?? 'N/A' }}</span>
                                            <span class="font-bold text-gray-800">₱{{ number_format($service->price, 2) }}</span>
                                        </div>

                                        @if($service->address)
                                            <div class="flex items-center text-xs text-gray-500 space-x-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="truncate">{{ $service->address->full_address }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $services->links() }}
                        </div>
                    @else
                        <p>No services found for your search query.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
