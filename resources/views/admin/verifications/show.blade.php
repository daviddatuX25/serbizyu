<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Review Verification for {{ $verification->user->firstname }} {{ $verification->user->lastname }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-bold">User Details</h3>
                            <p><strong>Name:</strong> {{ $verification->user->firstname }} {{ $verification->user->lastname }}</p>
                            <p><strong>Email:</strong> {{ $verification->user->email }}</p>
                            <p><strong>ID Type:</strong> {{ $verification->id_type }}</p>
                            <p><strong>Submitted At:</strong> {{ $verification->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Actions</h3>
                            <div class="mt-4 flex space-x-4">
                                <!-- Approve Form -->
                                <form method="POST" action="{{ route('admin.verifications.approve', $verification) }}">
                                    @csrf
                                    <x-primary-button>Approve</x-primary-button>
                                </form>

                                <!-- Reject Form -->
                                <form method="POST" action="{{ route('admin.verifications.reject', $verification) }}" x-data="{ showRejectReason: false }">
                                    @csrf
                                    <x-danger-button @click.prevent="showRejectReason = !showRejectReason">Reject</x-danger-button>

                                    <div x-show="showRejectReason" class="mt-4">
                                        <x-input-label for="rejection_reason" :value="__('Reason for Rejection')" />
                                        <textarea id="rejection_reason" name="rejection_reason" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required></textarea>
                                        @error('rejection_reason')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                        <x-primary-button class="mt-2">Confirm Rejection</x-primary-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-bold">Submitted Documents</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <h4 class="font-semibold">Front of ID</h4>
                                @if ($idFrontMedia)
                                    <img src="{{ route('media.serve', ['payload' => Crypt::encryptString(json_encode(['media_id' => $idFrontMedia->id]))]) }}" alt="Front of ID" class="mt-2 border rounded-md">
                                @else
                                    <p>No image uploaded.</p>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-semibold">Back of ID</h4>
                                @if ($idBackMedia)
                                    <img src="{{ route('media.serve', ['payload' => Crypt::encryptString(json_encode(['media_id' => $idBackMedia->id]))]) }}" alt="Back of ID" class="mt-2 border rounded-md">
                                @else
                                    <p>No image uploaded.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
