<x-app-layout>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Left Column: Service Images and Details --}}
            <div class="md:col-span-2">
                {{-- Service Images --}}
                <div class="mb-4">
                    @if($service->images->isNotEmpty())
                        {{-- Main Image --}}
                        <img src="{{ $service->images->firstWhere('is_primary', true)->path ?? $service->images->first()->path }}" alt="{{ $service->name }}" class="w-full h-auto object-cover rounded-lg shadow-md">
                        
                        {{-- Thumbnails --}}
                        @if($service->images->count() > 1)
                            <div class="grid grid-cols-5 gap-2 mt-2">
                                @foreach($service->images as $image)
                                    <div>
                                        <img src="{{ $image->path }}" alt="Thumbnail for {{ $service->name }}" class="w-full h-20 object-cover rounded-md cursor-pointer border-2 hover:border-blue-500">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        {{-- Placeholder --}}
                        <div class="w-full h-80 bg-gray-200 flex items-center justify-center rounded-lg shadow-md">
                            <span class="text-gray-500">No Image Available</span>
                        </div>
                    @endif
                </div>

                {{-- Service Details --}}
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $service->name }}</h1>
                    
                    <p class="text-gray-700 text-lg mb-4">
                        {{ $service->description }}
                    </p>

                    {{-- Workflow Steps --}}
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Workflow</h3>
                        <div class="flex flex-wrap items-center text-sm font-medium text-gray-600">
                            @forelse($service->workflowTemplate->workTemplates as $step)
                                <span>{{ $step->name }}</span>
                                @if(!$loop->last)
                                    <span class="mx-2 text-gray-400">></span>
                                @endif
                            @empty
                                <p class="text-gray-500">No workflow steps defined.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Reviews Section --}}
                    <div class="mt-8">
                        <h3 class="text-2xl font-semibold text-gray-700 mb-4">Reviews</h3>
                        <div class="space-y-4">
                            {{-- Dummy review data as example --}}
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <div class="flex justify-between items-center mb-1">
                                    <h4 class="font-semibold text-gray-800">David Datu Sarmiento</h4>
                                    <span class="text-yellow-500">★★★★★</span>
                                </div>
                                <p class="text-gray-600 mt-1">
                                    I found that transacting with serbisyo makes my work easier
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Creator Info and Actions --}}
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Service by</h2>
                    <div class="flex items-center mb-4">
                        <img src="{{ $service->creator->profile_photo_url }}" alt="{{ $service->creator->name }}" class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">{{ $service->creator->name }}</h3>
                            <p class="text-gray-600">Member since {{ $service->creator->created_at->format('M Y') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-gray-700 text-lg">
                            <span class="font-semibold">Rate:</span> ${{ number_format($service->price, 2) }}/hr
                        </p> 
                        <p class="text-gray-700 text-lg">
                            <span class="font-semibold">Location:</span> {{ $service->address->town }}, {{ $service->address->province }}
                        </p>
                    </div>

                    <div class="mt-6">
                        <button class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300 mb-2">
                            Proceed to Order
                        </button>
                        <button class="w-full bg-gray-200 text-gray-700 font-bold py-3 px-6 rounded-lg hover:bg-gray-300 transition duration-300">
                            Add to wishlist
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>