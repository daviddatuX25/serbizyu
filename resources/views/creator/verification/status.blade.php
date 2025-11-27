<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verification Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                    <p class="text-green-700 font-medium">✓ {{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <p class="text-red-700 font-medium">✗ {{ session('error') }}</p>
                </div>
            @endif

            @if ($verification)
                <!-- Status Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gradient-to-r {{ $verification->status == 'approved' ? 'from-green-50 to-green-100' : ($verification->status == 'rejected' ? 'from-red-50 to-red-100' : 'from-blue-50 to-blue-100') }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Verification Status</h3>
                                <div class="flex items-center gap-2">
                                    @if ($verification->status == 'pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-200 text-blue-800">
                                            ⏳ Pending Review
                                        </span>
                                    @elseif ($verification->status == 'approved')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-200 text-green-800">
                                            ✓ Verified
                                        </span>
                                    @elseif ($verification->status == 'rejected')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-200 text-red-800">
                                            ✗ Rejected
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right text-sm text-gray-600">
                                Submitted: {{ $verification->created_at->format('M d, Y') }}
                                @if ($verification->reviewed_at)
                                    <br>Reviewed: {{ $verification->reviewed_at->format('M d, Y') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200">
                        {{-- PENDING --}}
                        @if ($verification->status == 'pending')
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-blue-900">Your documents are under review. We'll notify you once the process is complete, usually within 24-48 hours.</p>
                            </div>

                        {{-- APPROVED --}}
                        @elseif ($verification->status == 'approved')
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p class="text-green-900 font-semibold">🎉 Congratulations! Your identity has been successfully verified.</p>
                                <p class="text-green-800 text-sm mt-2">You now have full access to all features on our platform.</p>
                            </div>

                        {{-- REJECTED --}}
                        @elseif ($verification->status == 'rejected')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                                <p class="text-red-900 font-semibold mb-3">Your verification request was not approved</p>
                                @if ($verification->rejection_reason)
                                    <div class="bg-white rounded p-3 border border-red-100 mb-4">
                                        <p class="text-sm text-gray-700"><strong>Reason:</strong></p>
                                        <p class="text-red-800 font-medium">{{ $verification->rejection_reason }}</p>
                                    </div>
                                @endif
                                <p class="text-red-800 text-sm">Please review the requirements and submit again with clear, legible documents.</p>
                            </div>

                            <a href="{{ route('verification.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Try Again') }}
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Documents Section -->
                @if ($idFrontMedia || $idBackMedia)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-6">Submitted Documents</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if ($idFrontMedia)
                                    <div class="border rounded-lg overflow-hidden">
                                        <div class="bg-gray-100 aspect-video flex items-center justify-center">
                                            <img src="{{ route('media.serve', ['payload' => Crypt::encryptString(json_encode(['media_id' => $idFrontMedia->id]))]) }}"
                                                alt="ID Front"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="p-3 bg-gray-50 border-t">
                                            <p class="text-sm font-medium text-gray-900">Front Side</p>
                                            <p class="text-xs text-gray-600">{{ $idFrontMedia->created_at->format('M d, Y g:i A') }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($idBackMedia)
                                    <div class="border rounded-lg overflow-hidden">
                                        <div class="bg-gray-100 aspect-video flex items-center justify-center">
                                            <img src="{{ route('media.serve', ['payload' => Crypt::encryptString(json_encode(['media_id' => $idBackMedia->id]))]) }}"
                                                alt="ID Back"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="p-3 bg-gray-50 border-t">
                                            <p class="text-sm font-medium text-gray-900">Back Side</p>
                                            <p class="text-xs text-gray-600">{{ $idBackMedia->created_at->format('M d, Y g:i A') }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            @else
                <!-- No Verification -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">🆔</div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Not Yet Verified</h3>
                            <p class="text-gray-600 mb-8 max-w-md mx-auto">To unlock all features, please complete your identity verification. It takes just a few minutes and your documents are securely encrypted.</p>

                            <a href="{{ route('verification.create') }}"
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Get Verified Now') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-creator-layout>
