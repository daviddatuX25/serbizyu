<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Service Analytics</h3>
        <div class="h-48 bg-gray-100 rounded-lg flex items-center justify-center border">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="ml-2 text-sm text-gray-500">(Chart placeholder)</span>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm font-medium text-gray-500 truncate">Total Revenue</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">
                ₱{{ number_format($analytics['total_revenue'] ?? 0, 2) }}
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm font-medium text-gray-500 truncate">Clicks (Today)</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $analytics['today_clicks'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm font-medium text-gray-500 truncate">Wishlist Adds</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $analytics['wishlist_count'] ?? 0 }}</p>
        </div>
    </div>
</div>
