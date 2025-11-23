<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-semibold mb-4">Place Your Bid</h3>
    <p class="mb-4 text-gray-700">You are placing a bid for the offer: <span class="font-medium">{{ $openoffer->title }}</span></p>

    <form action="{{ route('creator.openoffers.bids.store', $openoffer) }}" method="POST">
        @csrf

        <!-- Service -->
        <div class="mb-4">
            <label for="service_id" class="block text-sm font-medium text-gray-700">Select your service for this bid</label>
            <select name="service_id" id="service_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                @foreach($userServices as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->title }}
                    </option>
                @endforeach
            </select>
            @error('service_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Amount -->
        <div class="mb-4">
            <label for="amount" class="block text-sm font-medium text-gray-700">Bid Amount</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input type="number" name="amount" id="amount" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" value="{{ old('amount') }}" placeholder="0.00" step="0.01" required>
            </div>
            @error('amount')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Message -->
        <div class="mb-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Message (Optional)</label>
            <textarea name="message" id="message" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('message') }}</textarea>
            @error('message')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-6">
            <a href="{{ route('openoffers.show', $openoffer) }}" class="text-gray-600 hover:text-gray-900 mr-4">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                Place Bid
            </button>
        </div>
    </form>
</div>
