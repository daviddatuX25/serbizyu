<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Work Instance #' . $workInstance->id) }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Order #{{ $workInstance->order->id }}
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $workInstance->status === 'completed' ? 'bg-green-100 text-green-800' : ($workInstance->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                {{ ucfirst(str_replace('_', ' ', $workInstance->status)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Overview -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Overall Progress</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Progress</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $workInstance->getProgressPercentage() }}%</span>
                        </div>
                        <x-progress-bar :percentage="$workInstance->getProgressPercentage()" height="h-3" barColor="bg-blue-600" />
                        <div class="grid grid-cols-3 gap-4 mt-6 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-600">{{ $workInstance->workInstanceSteps()->count() }}</p>
                                <p class="text-sm text-gray-600">Total Steps</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">{{ $workInstance->getCompletedSteps()->count() }}</p>
                                <p class="text-sm text-gray-600">Completed</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-amber-600">{{ $workInstance->workInstanceSteps()->where('status', '!=', 'completed')->count() }}</p>
                                <p class="text-sm text-gray-600">Remaining</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline View -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-6 text-gray-900">Work Steps Timeline</h3>
                    
                    <div class="relative">
                        @forelse($workInstance->workInstanceSteps as $step)
                            <div class="mb-8 relative pl-12">
                                <!-- Timeline connector -->
                                @if(!$loop->last)
                                    <div class="absolute left-4 top-10 w-1 h-full {{ $step->isCompleted() ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                                @endif

                                <!-- Timeline dot -->
                                <div class="absolute left-0 top-1 w-8 h-8 rounded-full flex items-center justify-center {{ $step->isCompleted() ? 'bg-green-500' : ($step->isCurrent() ? 'bg-blue-500' : 'bg-gray-300') }} text-white font-semibold">
                                    @if($step->isCompleted())
                                        ✓
                                    @else
                                        {{ $step->step_index + 1 }}
                                    @endif
                                </div>

                                <!-- Step Card -->
                                <div class="bg-gray-50 rounded-lg p-4 border {{ $step->isCurrent() ? 'border-blue-300 bg-blue-50' : ($step->isCompleted() ? 'border-green-300 bg-green-50' : 'border-gray-200') }}">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">
                                                {{ $step->workTemplate?->name ?? 'Step ' . ($step->step_index + 1) }}
                                            </h4>
                                            @if($step->workTemplate?->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $step->workTemplate->description }}</p>
                                            @endif
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded {{ $step->isCompleted() ? 'bg-green-200 text-green-800' : ($step->isCurrent() ? 'bg-blue-200 text-blue-800' : 'bg-gray-200 text-gray-800') }}">
                                            {{ ucfirst($step->status) }}
                                        </span>
                                    </div>

                                    <!-- Step Details -->
                                    <div class="grid grid-cols-3 gap-4 text-sm text-gray-600 mb-4 pb-4 border-b border-gray-200">
                                        <div>
                                            <span class="font-medium">Duration:</span>
                                            {{ $step->getDurationMinutes() }} min
                                        </div>
                                        <div>
                                            <span class="font-medium">Started:</span>
                                            {{ $step->started_at?->format('M d, H:i') ?? 'Not started' }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Completed:</span>
                                            {{ $step->completed_at?->format('M d, H:i') ?? 'Pending' }}
                                        </div>
                                    </div>

                                    <!-- Activity Thread -->
                                    @if($step->activityThread)
                                        <div class="bg-white rounded border border-gray-200 p-3 mb-4">
                                            <div class="flex justify-between items-center mb-3">
                                                <h5 class="font-medium text-gray-900 text-sm">
                                                    {{ $step->activityThread->title ?? 'Step Discussion' }}
                                                </h5>
                                                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                                    {{ $step->activityThread->getMessageCount() }} message{{ $step->activityThread->getMessageCount() !== 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                            
                                            @if($step->activityThread->description)
                                                <p class="text-xs text-gray-600 mb-2">{{ $step->activityThread->description }}</p>
                                            @endif

                                            @if($step->activityThread->getLatestMessage())
                                                <div class="text-xs text-gray-500 italic border-l-2 border-gray-300 pl-2">
                                                    <strong>Latest:</strong> {{ $step->activityThread->getLatestMessage()->user->name }} said "{{ Str::limit($step->activityThread->getLatestMessage()->content, 80) }}"
                                                    <span class="text-gray-400">{{ $step->activityThread->getLatestMessage()->created_at->diffForHumans() }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400 italic">No activity thread yet</div>
                                    @endif

                                    <!-- Step Actions (for seller) -->
                                    @if(auth()->id() === $workInstance->order->seller_id && !$step->isCompleted())
                                        <div class="flex gap-2 pt-2">
                                            @if(!$step->isInProgress())
                                                <form action="{{ route('work-instances.steps.start', [$workInstance, $step]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                                                        Start Step
                                                    </button>
                                                </form>
                                            @endif
                                            @if($step->isInProgress())
                                                <form action="{{ route('work-instances.steps.complete', [$workInstance, $step]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition">
                                                        Complete Step
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">
                                <p>No steps defined for this work instance</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Order Info Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Order Details</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-4">Service</h4>
                            <p class="font-semibold text-gray-900">{{ $workInstance->order->service?->title ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($workInstance->order->service?->description ?? 'N/A', 150) }}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 mb-4">Price</h4>
                            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($workInstance->order->service?->price ?? 0, 2) }}</p>
                            <p class="text-sm text-gray-600 mt-1">Payment: <x-order-status-badge :status="$workInstance->order->payment_status" /></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Participants</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Seller</h4>
                            <div class="flex items-center">
                                <img src="{{ $workInstance->order->seller->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($workInstance->order->seller->name) }}" 
                                     alt="{{ $workInstance->order->seller->name }}" 
                                     class="h-10 w-10 rounded-full mr-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $workInstance->order->seller->name }}</p>
                                    <p class="text-sm text-gray-600">@{{ $workInstance->order->seller->username }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Buyer</h4>
                            <div class="flex items-center">
                                <img src="{{ $workInstance->order->buyer->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($workInstance->order->buyer->name) }}" 
                                     alt="{{ $workInstance->order->buyer->name }}" 
                                     class="h-10 w-10 rounded-full mr-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $workInstance->order->buyer->name }}</p>
                                    <p class="text-sm text-gray-600">@{{ $workInstance->order->buyer->username }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
