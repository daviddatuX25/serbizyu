<x-admin-layout>
    <x-slot name="header">
        {{ __('Open Offers Management') }}
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
                border: 1px solid #475569; /* slate-600 */
                border-radius: 0.5rem;
                overflow: hidden;
                background-color: #1e293b; /* slate-800 */
            }
            .responsive-table td {
                padding-left: 50%;
                position: relative;
                text-align: left;
                white-space: normal;
                border-bottom: 1px solid #334155; /* slate-700 */
                color: #cbd5e1; /* slate-300 */
            }
            .responsive-table tr:last-child td:last-child {
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
                color: #94a3b8; /* slate-400 */
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-slate-700">
                <div class="p-6 bg-slate-800">
                    <!-- Search and Filter -->
                    <div class="flex flex-col gap-4 mb-6">
                        <form method="GET" class="flex gap-4">
                            <input
                                type="text"
                                name="search"
                                placeholder="Search open offers..."
                                value="{{ request('search') }}"
                                class="flex-1 rounded-lg bg-slate-700 border border-slate-600 text-white placeholder-slate-400 px-4 py-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                Search
                            </button>
                        </form>
                    </div>

                    <!-- Table -->
                    @if($openOffers->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full responsive-table">
                                <thead class="bg-slate-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Creator</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="md:divide-y md:divide-slate-700">
                                    @foreach($openOffers as $offer)
                                        <tr class="hover:bg-slate-700/50 transition-colors">
                                            <td data-label="Title" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                                {{ $offer->title }}
                                            </td>
                                            <td data-label="Creator" class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                                {{ $offer->creator?->firstname ?? 'N/A' }} {{ $offer->creator?->lastname ?? '' }}
                                            </td>
                                            <td data-label="Category" class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                                {{ $offer->category?->name ?? 'N/A' }}
                                            </td>
                                            <td data-label="Status" class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                    {{ $offer->status === \App\Enums\OpenOfferStatus::OPEN ? 'bg-green-900 text-green-200' : 'bg-yellow-900 text-yellow-200' }}">
                                                    {{ $offer->status->value }}
                                                </span>
                                            </td>
                                            <td data-label="Actions" class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                                <a href="{{ route('openoffers.show', ['openoffer' => $offer]) }}" class="text-blue-400 hover:text-blue-300">View</a>
                                                <button @click="openFlagModal({{ $offer->id }}, '{{ addslashes($offer->title) }}')" class="text-red-400 hover:text-red-300">Flag</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $openOffers->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-slate-400">No open offers found.</p>
                        </div>
                    @endif
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
                    <h3 class="text-lg font-semibold text-white mb-4">Flag Open Offer</h3>

                    <form method="POST" action="{{ route('admin.flags.store') }}" @submit="showModal = false">
                        @csrf

                        <input type="hidden" name="flaggable_type" value="App\Domains\Listings\Models\OpenOffer">
                        <input type="hidden" name="flaggable_id" :value="flaggedId">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Offer Title</label>
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
                                placeholder="Describe why this offer should be flagged..."
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