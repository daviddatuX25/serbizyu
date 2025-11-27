<x-admin-layout>
    <x-slot name="header">
        {{ __('User Details') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- User Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">First Name</label>
                                <p class="mt-1 text-gray-900">{{ $user->firstname }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Last Name</label>
                                <p class="mt-1 text-gray-900">{{ $user->lastname }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Email</label>
                                <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Username</label>
                                <p class="mt-1 text-gray-900">{{ $user->username ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Phone</label>
                                <p class="mt-1 text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Joined</label>
                                <p class="mt-1 text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Roles and Permissions -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Roles & Permissions</h3>
                        <div class="space-y-2">
                            @forelse ($user->getRoleNames() as $role)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ $role }}
                                </span>
                            @empty
                                <p class="text-gray-500">No roles assigned</p>
                            @endforelse
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Account Status -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Status</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Email Verified</label>
                                <p class="mt-1">
                                    @if ($user->email_verified_at)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">ID Verified</label>
                                <p class="mt-1">
                                    @if ($user->verifications()->where('status', 'approved')->exists())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Not Verified
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Creator Flag Stats (if applicable) -->
                    @if ($user->flagStats)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Flag History</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Total Flags</label>
                                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $user->flagStats->total_flags }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Last 30 Days</label>
                                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $user->flagStats->flags_last_30_days }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Last Flagged</label>
                                    <p class="mt-1 text-gray-900">
                                        @if ($user->flagStats->last_flagged_at)
                                            {{ $user->flagStats->last_flagged_at->format('M d, Y H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Escalation Level</label>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $user->flagStats->getEscalationBadgeColor() }}-100 text-{{ $user->flagStats->getEscalationBadgeColor() }}-800">
                                            {{ $user->flagStats->getEscalationLabel() }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-6">
                    @endif

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Edit User
                        </a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Are you sure?')">
                                Delete User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
