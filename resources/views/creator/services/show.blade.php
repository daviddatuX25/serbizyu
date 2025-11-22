<x-app-layout>
    <div x-data="{ activeTab: 'overview' }">
        {{-- Header --}}
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold leading-tight text-gray-800">
                            Manage Service
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $service->title }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                         <a href="{{ route('services.show', $service) }}" target="_blank"
                             class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                             <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            Preview
                        </a>
                        <a href="{{ route('creator.services.edit', $service) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                    
                    {{-- Left Sidebar Navigation --}}
                    <div class="lg:col-span-3">
                        <nav class="space-y-1">
                            <button @click="activeTab = 'overview'" :class="{'bg-gray-200 text-gray-900': activeTab === 'overview', 'text-gray-600 hover:bg-gray-50': activeTab !== 'overview'}"
                                class="group w-full flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                Overview
                            </button>
                            <button @click="activeTab = 'orders'" :class="{'bg-gray-200 text-gray-900': activeTab === 'orders', 'text-gray-600 hover:bg-gray-50': activeTab !== 'orders'}"
                                class="group w-full flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                Orders
                            </button>
                             <button @click="activeTab = 'reviews'" :class="{'bg-gray-200 text-gray-900': activeTab === 'reviews', 'text-gray-600 hover:bg-gray-50': activeTab !== 'reviews'}"
                                class="group w-full flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                Reviews
                            </button>
                        </nav>
                    </div>

                    {{-- Right Content Area --}}
                    <div class="lg:col-span-9">
                        <div x-show="activeTab === 'overview'" x-cloak>
                            @include('creator.services.partials.overview-tab', ['service' => $service, 'analytics' => $analytics])
                        </div>
                        <div x-show="activeTab === 'orders'" x-cloak>
                           @include('creator.services.partials.orders-tab', ['orders' => $orders])
                        </div>
                         <div x-show="activeTab === 'reviews'" x-cloak>
                           @include('creator.services.partials.reviews-tab', ['reviews' => $reviews])
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
