<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Work Purchases') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <a href="?status=" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            All Work
                        </a>
                        <a href="?status=in_progress" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'in_progress' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            In Progress
                        </a>
                        <a href="?status=completed" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'completed' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Completed
                        </a>
                        <a href="?status=pending" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Not Started
                        </a>
                    </div>
                </div>
            </div>

            <!-- Work Instances List -->
            @forelse($workInstances as $work)
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-4">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $work->order->service?->title ?? 'Service' }}
                                    </h3>
                                    <x-order-status-badge :status="$work->status" />
                                </div>
                                <p class="text-sm text-gray-600">
                                    Order #{{ $work->order->id }} • Started {{ $work->started_at?->format('M d, Y') ?? 'Not started' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900">₱{{ number_format($work->order->service?->price ?? 0, 2) }}</p>
                                <p class="text-sm text-gray-600">Payment: <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $work->order->payment_status)) }}</span></p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $work->getProgressPercentage() }}%</span>
                            </div>
                            <x-progress-bar :percentage="$work->getProgressPercentage()" height="h-2" barColor="bg-green-600" />
                        </div>

                        <!-- Step Summary -->
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-3 gap-4 text-center text-sm">
                                <div>
                                    <p class="text-xl font-bold text-blue-600">{{ $work->workInstanceSteps()->count() }}</p>
                                    <p class="text-gray-600">Total Steps</p>
                                </div>
                                <div>
                                    <p class="text-xl font-bold text-green-600">{{ $work->getCompletedSteps()->count() }}</p>
                                    <p class="text-gray-600">Completed</p>
                                </div>
                                <div>
                                    <p class="text-xl font-bold text-amber-600">{{ $work->workInstanceSteps()->where('status', '!=', 'completed')->count() }}</p>
                                    <p class="text-gray-600">Remaining</p>
                                </div>
                            </div>
                        </div>

                        <!-- Current Step Info -->
                        @if($currentStep = $work->getCurrentStep())
                            <div class="mb-4 p-3 border border-blue-200 bg-blue-50 rounded-lg">
                                <p class="text-sm font-medium text-blue-900">
                                    {{ $currentStep->workTemplate?->name ?? 'Step ' . ($currentStep->step_index + 1) }}
                                </p>
                                <p class="text-xs text-blue-700 mt-1">
                                    {{ $currentStep->workTemplate?->description ?? 'In progress' }}
                                </p>
                                <p class="text-xs text-blue-600 mt-2">
                                    Started: {{ $currentStep->started_at?->format('M d, H:i') ?? 'Just now' }}
                                </p>
                            </div>
                        @else
                            @if($work->isCompleted())
                                <div class="mb-4 p-3 border border-green-200 bg-green-50 rounded-lg">
                                    <p class="text-sm font-medium text-green-900">
                                        ✓ All steps completed
                                    </p>
                                    <p class="text-xs text-green-700 mt-1">
                                        Completed on {{ $work->completed_at?->format('M d, Y \a\t H:i') }}
                                    </p>
                                </div>
                            @endif
                        @endif

                        <!-- Seller Info -->
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600 font-medium mb-2">SERVICE PROVIDER</p>
                            <div class="flex items-center">
                                <img src="{{ $work->order->seller->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($work->order->seller->name) }}"
                                     alt="{{ $work->order->seller->name }}"
                                     class="h-8 w-8 rounded-full mr-2">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $work->order->seller->name }}</p>
                                    <p class="text-xs text-gray-600">@{{ $work->order->seller->username }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="flex gap-2">
                            <a href="{{ route('orders.work.show', $work->order) }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                View Details
                            </a>
                            @if($work->order->payment_status === 'paid' && $work->status === 'completed')
                                <button class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                    Leave Review
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No work purchases yet</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't purchased any services yet.</p>
                    <div class="mt-6">
                        <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            Browse Services
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
