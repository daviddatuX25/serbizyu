<x-admin-layout>
    <x-slot name="header">
        {{ __('Services Management') }}
    </x-slot>

    <style>
        @media (max-width: 768px) {
            .responsive-table thead {
                display: none;
            }
            .responsive-table tbody,
            .responsive-table tr,
            .responsive-table td {
                display: block;
                width: 100%;
            }
            .responsive-table tr {
                margin-bottom: 1rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                overflow: hidden;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }
            .responsive-table td {
                padding-left: 50%;
                position: relative;
                text-align: left;
                white-space: normal;
                border-bottom: 1px solid #e2e8f0;
            }
            .responsive-table td:last-child {
                border-bottom: none;
            }
            .responsive-table td:before {
                position: absolute;
                left: 0.75rem;
                width: 45%;
                padding-right: 0.75rem;
                white-space: nowrap;
                font-weight: 600;
                content: attr(data-label);
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('admin.listings.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by title..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                                <option value="">All Categories</option>
                                @foreach (\App\Domains\Listings\Models\Category::all() as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 responsive-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Title
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Creator
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Price
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Flag</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 md:divide-y-0">
                            @forelse ($services as $service)
                                <tr>
                                    <td data-label="Title" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $service->title }}</div>
                                    </td>
                                    <td data-label="Creator" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $service->creator->firstname }} {{ $service->creator->lastname }}</div>
                                    </td>
                                    <td data-label="Category" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $service->category->name ?? 'N/A' }}</div>
                                    </td>
                                    <td data-label="Price" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">₱{{ number_format($service->price, 2) }}</div>
                                    </td>
                                    <td data-label="Status" class="px-6 py-4 whitespace-nowrap">
                                        @if (!$service->trashed())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Deleted</span>
                                        @endif
                                    </td>
                                    <td data-label="Actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('services.show', $service) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                    <td data-label="Flag" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="openFlagModal({{ $service->id }}, '{{ addslashes($service->title) }}')" class="text-red-600 hover:text-red-900">Flag</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No services found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flag Modal -->
    <div x-data="{ showModal: false, flaggedId: null, flaggedTitle: '', category: '', reason: '' }"
         @open-flag-modal.window="showModal = true; flaggedId = $event.detail.id; flaggedTitle = $event.detail.title"
         class="fixed inset-0 z-50 overflow-y-auto" x-show="showModal" style="display: none;">

        <!-- Backdrop -->
        <div @click="showModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>

        <!-- Modal Content -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-slate-800 rounded-lg shadow-xl max-w-md w-full border border-slate-700" @click.stop>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Flag Service</h3>

                    <form method="POST" action="{{ route('admin.flags.store') }}" @submit="showModal = false">
                        @csrf

                        <input type="hidden" name="flaggable_type" value="App\Domains\Listings\Models\Service">
                        <input type="hidden" name="flaggable_id" :value="flaggedId">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Service Title</label>
                            <p class="text-white bg-slate-700 rounded px-3 py-2" x-text="flaggedTitle"></p>
                        </div>

                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-slate-300 mb-2">Category</label>
                            <select
                                name="category"
                                id="category"
                                x-model="category"
                                class="w-full rounded-lg bg-slate-700 border border-slate-600 text-white px-3 py-2"
                                required>
                                <option value="">Select a category...</option>
                                <option value="spam">Spam</option>
                                <option value="inappropriate">Inappropriate</option>
                                <option value="fraud">Fraud</option>
                                <option value="misleading_info">Misleading Information</option>
                                <option value="copyright_violation">Copyright Violation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="reason" class="block text-sm font-medium text-slate-300 mb-2">Reason for Flagging</label>
                            <textarea
                                name="reason"
                                id="reason"
                                x-model="reason"
                                class="w-full rounded-lg bg-slate-700 border border-slate-600 text-white placeholder-slate-400 px-3 py-2"
                                placeholder="Describe why this service should be flagged..."
                                rows="4"
                                required></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                Flag
                            </button>
                            <button type="button" @click="showModal = false" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openFlagModal(id, title) {
            window.dispatchEvent(new CustomEvent('open-flag-modal', {
                detail: { id, title }
            }));
        }
    </script>
</x-admin-layout>