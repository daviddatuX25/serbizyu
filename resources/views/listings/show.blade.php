<x-app-layout>
    {{-- Set a max-width for a mobile-centric view, matching the wireframe's single column --}}
    <div class="container mx-auto max-w-lg">
        <div class="pb-24"> {{-- Padding-bottom to avoid overlap with fixed footer --}}

            {{-- Service Images --}}
            <div class="mb-4">
                @if($service->images->isNotEmpty())
                    {{-- Main Image --}}
                    <img src="{{ $service->images->firstWhere('is_primary', true)->path ?? $service->images->first()->path }}" alt="{{ $service->title }}" class="w-full h-auto object-cover"> [cite: 2]
                    
                    {{-- Thumbnails --}}
                    @if($service->images->count() > 1)
                        <div class="grid grid-cols-5 gap-2 mt-2"> [cite: 3]
                            @foreach($service->images as $image)
                                <div>
                                    <img src="{{ $image->path }}" alt="Thumbnail for {{ $service->title }}" class="w-full h-20 object-cover rounded-md cursor-pointer border-2 hover:border-blue-500"> [cite: 4]
                                </div>
                            @endforeach [cite: 5]
                        </div>
                    @endif
                @else
                    {{-- Placeholder --}}
                    <div class="w-full h-80 bg-gray-200 flex items-center justify-center rounded-lg"> [cite: 6]
                        <span class="text-gray-500">No Image Available</span>
                    </div>
                @endif [cite: 7]
            </div>

            {{-- Service Details --}}
            <div class="px-4">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $service->title }}</h1> [cite: 7]
                
                {{-- Details from wireframe (using data from original sidebar) --}}
                <p class="text-gray-700 text-lg">
                    Rate: ${{ number_format($service->price, 2) }}/hr
                </p> 
                <p class="text-gray-700 text-lg mb-4">
                    Location: Magsaysay Tagudin Ilocos Sur {{-- Placeholder location from wireframe --}}
                </p>

                {{-- Workflow Steps (Reformatted horizontally) --}}
                <div class="mb-6">
                    <div class="flex flex-wrap items-center text-sm font-medium text-gray-600">
                        @forelse($service->workflowTemplate->workTemplates as $step) [cite: 8]
                            <span>{{ $step->title }}</span> [cite: 10]
                            @if(!$loop->last)
                                <span class="mx-2 text-gray-400">></span>
                            @endif
                        @empty
                            <p class="text-gray-500">No workflow steps defined.</p> [cite: 11]
                        @endforelse
                    </div>
                </div>

                {{-- Reviews Section (New from wireframe) --}}
                <div class="mt-8">
                    <h3 class="text-2xl font-semibold text-gray-700 mb-4">Reviews</h3>
                    <div class="space-y-4">
                        {{-- Dummy review data as example --}}
                        <div class="p-4 border rounded-lg bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-1">
                                <h4 class="font-semibold text-gray-800">David Datu Sarmiento</h4>
                                <span class="text-yellow-500">★★★★★</span>
                            </div>
                            <p class="text-gray-600 mt-1">
                                I found that transacting with serbisyo makes my work easier
                            </p>
                        </div>
                        
                        {{-- Add a loop for actual reviews here --}}
                        {{-- @forelse($service->reviews as $review)
                            <div class="p-4 border rounded-lg bg-white shadow-sm">
                                <div class="flex justify-between items-center mb-1">
                                    <h4 class="font-semibold text-gray-800">{{ $review->user->name }}</h4>
                                    <span class="text-yellow-500">... star rating ...</span>
                                </div>
                                <p class="text-gray-600 mt-1">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500">No reviews for this service yet.</p>
                        @endforelse --}}
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Fixed Bottom Action Bar (New from wireframe) --}}
    <div class="fixed bottom-0 left-0 right-0 w-full max-w-lg mx-auto bg-white border-t p-4 flex justify-between items-center shadow-inner">
        <button class="font-semibold text-gray-700 hover:text-blue-600 transition duration-300">
            Add to wishlist
        </button>
        <button class="bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300">
            Proceed to Order
        </button>
    </div>
</x-app-layout>