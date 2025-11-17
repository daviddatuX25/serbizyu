<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Services') }}
            </h2>
            <a href="{{ route('creator.services.create') }}" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Service</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($services->isNotEmpty())
                <!-- Card Grid Layout (Mobile First) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($services as $service)
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

                                <!-- Status Badge -->
                                <div class="absolute top-2 right-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full shadow {{ $service->is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Service Details -->
                            <div class="p-4 space-y-3">
                                <h3 class="text-lg font-bold text-gray-800 line-clamp-2">{{ $service->title }}</h3>
                                
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ $service->category->name ?? 'N/A' }}</span>
                                    <span class="font-bold text-gray-800">${{ number_format($service->price, 2) }}</span>
                                </div>

                                <!-- Quick Stats -->
                                <div class="grid grid-cols-3 gap-2 pt-2 border-t text-center">
                                    <div>
                                        <p class="text-xs text-gray-500">Orders</p>
                                        <p class="text-lg font-semibold">0</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Views</p>
                                        <p class="text-lg font-semibold">0</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Rating</p>
                                        <p class="text-lg font-semibold">-</p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-2 pt-3 border-t">
                                    <a href="{{ route('creator.services.manage', $service) }}" 
                                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-center py-2 rounded-lg text-sm font-medium transition">
                                        Manage
                                    </a>
                                    <a href="{{ route('creator.services.edit', $service) }}" 
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-lg text-sm font-medium transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('creator.services.destroy', $service) }}" method="POST" class="flex-1" 
                                        onsubmit="return confirm('Are you sure you want to delete this service?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg text-sm font-medium transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-300 shadow-md p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-gray-500 text-lg mb-4">No services found.</p>
                    <a href="{{ route('creator.services.create') }}" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create your first service
                    </a>
                </div>
            @endif

            <!-- Pagination -->
            @if($services->hasPages())
                <div class="mt-8">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

