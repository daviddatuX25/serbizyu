<x-creator-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}

                    <div class="mt-4">
                        @if (Auth::user()->is_verified)
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Not Verified
                            </span>
                            <a href="{{ route('verification.create') }}" class="ml-4 text-sm text-indigo-600 hover:text-indigo-900">Get Verified Now</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
