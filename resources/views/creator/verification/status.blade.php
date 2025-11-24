<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verification Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Session Status -->
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($verification)
                        <h3 class="text-lg font-bold">
                            Your submission status is:
                            <span class="capitalize text-blue-600">{{ $verification->status }}</span>
                        </h3>

                        {{-- PENDING --}}
                        @if ($verification->status == 'pending')
                            <p class="mt-2">Your documents are currently under review. We will notify you once the process is complete.</p>

                        {{-- APPROVED --}}
                        @elseif ($verification->status == 'approved')
                            <p class="mt-2 text-green-600">Congratulations! Your identity has been verified.</p>

                        {{-- REJECTED --}}
                        @elseif ($verification->status == 'rejected')
                            <p class="mt-2 text-red-600">Your verification request was rejected.</p>

                            @if ($verification->rejection_reason)
                                <p class="mt-1"><strong>Reason:</strong> {{ $verification->rejection_reason }}</p>
                            @endif

                            <a href="{{ route('verification.create') }}"
                               class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Submit Again') }}
                            </a>
                        @endif

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if ($idFrontMedia)
                                    <div>
                                        <h4 class="font-semibold text-lg mb-2">ID Front:</h4>
                                        <img src="{{ route('media.serve', ['payload' => Crypt::encryptString(json_encode(['media_id' => $idFrontMedia->id]))]) }}" 
                                            alt="ID Front" 
                                            class="max-w-full h-auto rounded-lg shadow-md">
                                    </div>
                                @endif

                                @if ($idBackMedia)
                                    <div>
                                        <h4 class="font-semibold text-lg mb-2">ID Back:</h4>
                                        <img src="{{ route('media.serve', ['payload' => Crypt::encryptString(json_encode(['media_id' => $idBackMedia->id]))]) }}" 
                                            alt="ID Back" 
                                            class="max-w-full h-auto rounded-lg shadow-md">
                                    </div>
                                @endif
                            </div>
                        </div>

                    @else
                        <h3 class="text-lg font-bold">
                            {{ __("You haven't submitted any documents for verification yet.") }}
                        </h3>

                        <a href="{{ route('verification.create') }}"
                           class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Get Verified Now') }}
                        </a>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
