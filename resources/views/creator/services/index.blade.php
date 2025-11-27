<x-creator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Services') }}
            </h2>
        </div>
    </x-slot>
    <a href="{{ route('creator.services.create') }}" 
        class="w-[200px] px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Add New Service</span>
    </a>
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
            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($services as $service)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $service->title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">₱{{ number_format($service->price, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $service->category->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('services.show', $service) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('creator.services.edit', $service) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                            @if(!$service->is_active)
                                                <form action="{{ route('creator.services.publish', $service) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to publish this service?');">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Publish</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('creator.services.destroy', $service) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="md:hidden grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach($services as $service)
                    <x-resource-card
                        :title="$service->title"
                        :status="$service->is_active ? 'Active' : 'Inactive'"
                        :statusClass="$service->is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white'"
                        :details="[
                            'Category' => $service->category->name ?? 'N/A',
                            'Price' => '₱' . number_format($service->price, 2),
                        ]"
                        :actions="[
                            ['label' => 'Manage', 'url' => route('creator.services.manage', $service), 'class' => 'bg-gray-100 hover:bg-gray-200'],
                            ['label' => 'Edit', 'url' => route('creator.services.edit', $service), 'class' => 'bg-blue-600 hover:bg-blue-700 text-white'],
                        ]"
                    />
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
</x-creator-layout>
