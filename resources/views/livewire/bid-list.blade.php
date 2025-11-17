<div>
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <h3 class="text-lg font-semibold mb-3">Bids ({{ $bids->count() }})</h3>

    @if ($bids->isEmpty())
        <p class="text-gray-600">No bids yet.</p>
    @else
        <div class="space-y-4">
            @foreach ($bids as $bid)
                <div class="bg-white p-4 rounded-lg shadow-md border {{ $bid->status === 'accepted' ? 'border-green-500' : ($bid->status === 'rejected' ? 'border-red-500' : 'border-gray-200') }}">
                    @if ($editingBidId === $bid->id)
                        <div>
                            <div class="mb-4">
                                <label for="editingBidAmount" class="block text-sm font-medium text-gray-700">Bid Amount</label>
                                <input type="number" step="0.01" id="editingBidAmount" wire:model.defer="editingBidAmount"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('editingBidAmount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="editingBidMessage" class="block text-sm font-medium text-gray-700">Message (optional)</label>
                                <textarea id="editingBidMessage" wire:model.defer="editingBidMessage" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                @error('editingBidMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex space-x-2">
                                <button wire:click="updateBid"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save
                                </button>
                                <button wire:click="cancelEditing"
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <p class="text-gray-800 font-medium">Bidder: {{ $bid->bidder->username }}</p>
                                <p class="text-xl font-bold text-indigo-600">${{ number_format($bid->amount, 2) }}</p>
                            </div>
                            <div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if ($bid->status === 'accepted') bg-green-200 text-green-800
                                    @elseif ($bid->status === 'rejected') bg-red-200 text-red-800
                                    @else bg-blue-200 text-blue-800 @endif">
                                    {{ ucfirst($bid->status) }}
                                </span>
                            </div>
                        </div>
                        @if ($bid->message)
                            <p class="text-gray-700 text-sm mb-2">{{ $bid->message }}</p>
                        @endif
                        <p class="text-gray-500 text-xs">Bidded on: {{ $bid->created_at->format('M d, Y H:i') }}</p>

                        <div class="mt-3 flex space-x-2">
                            @can('accept', $bid)
                                <button wire:click="acceptBid({{ $bid->id }})"
                                        onclick="return confirm('Are you sure you want to accept this bid? This will close the offer to other bids.');"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Accept
                                </button>
                            @endcan
                            @can('reject', $bid)
                                <button wire:click="rejectBid({{ $bid->id }})"
                                        onclick="return confirm('Are you sure you want to reject this bid?');"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Reject
                                </button>
                            @endcan
                            @can('update', $bid)
                                <button wire:click="editBid({{ $bid->id }})"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    Edit
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
