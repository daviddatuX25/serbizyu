<x-admin-layout>
    <x-slot name="header">
        {{ __('Flag #' . $flag->id) }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Flag Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Flag Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Flag ID</label>
                                <p class="mt-1 text-gray-900">#{{ $flag->id }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Status</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $flag->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $flag->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $flag->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $flag->status === 'resolved' ? 'bg-blue-100 text-blue-800' : '' }}
                                    ">
                                        {{ $flag->status }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Category</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $flag->category }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Created</label>
                                <p class="mt-1 text-gray-900">{{ $flag->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Reporter Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reporter</h3>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Reported By</label>
                            <p class="mt-1 text-gray-900">
                                {{ $flag->user->firstname }} {{ $flag->user->lastname }}
                                <br><small class="text-gray-500">{{ $flag->user->email }}</small>
                            </p>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Flagged Content -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Flagged Content</h3>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Type</label>
                            <p class="mt-1 text-gray-900">{{ class_basename($flag->flaggable_type) }}</p>
                        </div>
                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-700">Content</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                @if ($flag->flaggable)
                                    <p class="text-gray-900">{{ $flag->flaggable->title ?? $flag->flaggable->name ?? 'N/A' }}</p>
                                    <small class="text-gray-500">
                                        By: {{ $flag->flaggable->creator->firstname ?? 'Unknown' }} {{ $flag->flaggable->creator->lastname ?? '' }}
                                    </small>
                                @else
                                    <p class="text-gray-500">Content has been deleted</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Report Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Report Details</h3>
                        <div class="mb-4">
                            <label class="text-sm font-medium text-gray-700">Reason</label>
                            <p class="mt-1 text-gray-900">{{ $flag->reason }}</p>
                        </div>
                        @if ($flag->evidence)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Evidence</label>
                                <p class="mt-1 text-gray-900">{{ $flag->evidence }}</p>
                            </div>
                        @endif
                    </div>

                    <hr class="my-6">

                    <!-- Admin Review -->
                    @if ($flag->admin)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Review</h3>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-700">Reviewed By</label>
                                <p class="mt-1 text-gray-900">
                                    {{ $flag->admin->firstname }} {{ $flag->admin->lastname }}
                                    <br><small class="text-gray-500">{{ $flag->admin->email }}</small>
                                </p>
                            </div>
                            @if ($flag->admin_notes)
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Admin Notes</label>
                                    <p class="mt-1 text-gray-900">{{ $flag->admin_notes }}</p>
                                </div>
                            @endif
                            @if ($flag->reviewed_at)
                                <div class="mt-4">
                                    <label class="text-sm font-medium text-gray-700">Reviewed At</label>
                                    <p class="mt-1 text-gray-900">{{ $flag->reviewed_at->format('M d, Y H:i') }}</p>
                                </div>
                            @endif
                        </div>

                        <hr class="my-6">
                    @endif

                    <!-- Actions -->
                    <div class="space-y-4">
                        @if ($flag->status === 'pending')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <h4 class="font-semibold text-yellow-900 mb-3">Review Flag</h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <form action="{{ route('admin.flags.approve', $flag) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="approve_notes" class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                                            <textarea name="admin_notes" id="approve_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"></textarea>
                                        </div>
                                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            Approve Flag
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.flags.reject', $flag) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="reject_notes" class="block text-sm font-medium text-gray-700">Notes (required)</label>
                                            <textarea name="admin_notes" id="reject_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200" required></textarea>
                                        </div>
                                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                            Reject Flag
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <form action="{{ route('admin.flags.resolve', $flag) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                        Mark as Resolved
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
