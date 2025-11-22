<div class="bg-white rounded-lg shadow-md">
    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border-b border-green-200">
            <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border-b border-red-200">
            <p class="text-red-700 text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @if ($bids->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No bids yet</h3>
            <p class="mt-1 text-sm text-gray-500">When a provider places a bid, it will appear here.</p>
        </div>
    @else
        <div class="divide-y divide-gray-200">
            @foreach ($bids as $bid)
                <div class="p-6">
                    @if ($editingBidId === $bid->id)
                        {{-- Editing State --}}
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-gray-900">Edit Your Bid</h4>
                            <div>
                                <label for="editingBidAmount" class="block text-sm font-medium text-gray-700">New Bid Amount</label>
                                <input type="number" step="0.01" id="editingBidAmount" wire:model.defer="editingBidAmount"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                @error('editingBidAmount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="editingBidMessage" class="block text-sm font-medium text-gray-700">Message (optional)</label>
                                <textarea id="editingBidMessage" wire:model.defer="editingBidMessage" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"></textarea>
                                @error('editingBidMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex items-center space-x-3">
                                <button wire:click="updateBid" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md">Save Changes</button>
                                <button wire:click="cancelEditing" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md">Cancel</button>
                            </div>
                        </div>
                    @else
                        {{-- Display State --}}
                        <div class="flex flex-col md:flex-row justify-between">
                            <div class="flex items-start">
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $bid->user->profile_photo_url }}" alt="{{ $bid->user->name }}">
                                <div class="ml-4">
                                    <p class="text-sm font-bold text-gray-900">{{ $bid->user->name }}</p>
                                    @if ($bid->service)
                                        <p class="text-xs text-gray-600">Proposing Service: <span class="font-medium">{{ $bid->service->title }}</span></p>
                                    @endif
                                     <p class="text-xs text-gray-500 mt-0.5">Bidded on: {{ $bid->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0 text-left md:text-right">
                                <p class="text-2xl font-bold text-gray-800">₱{{ number_format($bid->amount, 2) }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @switch($bid->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('accepted') bg-green-100 text-green-800 @break
                                        @case('rejected') bg-red-100 text-red-800 @break
                                    @endswitch">
                                    {{ ucfirst($bid->status) }}
                                </span>
                            </div>
                        </div>
                         @if ($bid->message)
                            <div class="mt-4 pl-14">
                                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $bid->message }}</p>
                            </div>
                        @endif
                        <div class="mt-4 pl-14 flex items-center space-x-4">
                            @can('accept', $bid)
                                <button wire:click="acceptBid({{ $bid->id }})"
                                        onclick="return confirm('Are you sure? This will close the offer to other bids.');"
                                        class="text-sm font-medium text-green-600 hover:text-green-800">
                                    Accept
                                </button>
                            @endcan
                             @can('reject', $bid)
                                <button wire:click="rejectBid({{ $bid->id }})"
                                        onclick="return confirm('Are you sure?');"
                                        class="text-sm font-medium text-red-600 hover:text-red-800">
                                    Reject
                                </button>
                            @endcan
                            @can('update', $bid)
                                <button wire:click="editBid({{ $bid->id }})" class="text-sm font-medium text-gray-500 hover:text-gray-900">Edit</button>
                            @endcan
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
