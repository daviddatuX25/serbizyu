<x-creator-layout title="Work Dashboard">
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Work Dashboard') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Manage your ongoing work and orders
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Orders</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $workInstances->count() }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9a2 2 0 012-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">In Progress</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $workInstances->where('status', 'in_progress')->count() }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Completed</p>
                            <p class="text-3xl font-bold text-green-600">{{ $workInstances->where('status', 'completed')->count() }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Not Started</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $workInstances->where('status', 'pending')->count() }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Instances List -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Your Work Orders</h3>

                    @if ($workInstances->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">No work orders yet</h3>
                            <p class="mt-1 text-sm text-gray-500">You don't have any active work orders at the moment.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($workInstances as $workInstance)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-1">
                                                <h4 class="font-semibold text-gray-900">
                                                    {{ $workInstance->order->service?->title ?? 'Service' }}
                                                </h4>
                                                <span class="px-2 py-1 text-xs font-medium rounded {{ $workInstance->status === 'completed' ? 'bg-green-100 text-green-800' : ($workInstance->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $workInstance->status)) }}
                                                </span>
                                                @php
                                                    $isSeller = auth()->id() === $workInstance->order->seller_id;
                                                    $isBuyer = auth()->id() === $workInstance->order->buyer_id;
                                                @endphp
                                                @if($isSeller)
                                                    <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800">Seller</span>
                                                @elseif($isBuyer)
                                                    <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Buyer</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                Order #{{ $workInstance->order->id }} • Buyer: {{ $workInstance->order->buyer->name }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-gray-900">₱{{ number_format($workInstance->order->service?->price ?? 0, 2) }}</p>
                                            <p class="text-xs text-gray-500">{{ $workInstance->started_at?->format('M d, Y') ?? 'Not started' }}</p>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="mb-3">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs font-medium text-gray-600">Progress</span>
                                            <span class="text-xs font-semibold text-gray-900">{{ $workInstance->getProgressPercentage() }}%</span>
                                        </div>
                                        <x-progress-bar :percentage="$workInstance->getProgressPercentage()" height="h-2" barColor="bg-green-500" />
                                    </div>

                                    <!-- Current Step -->
                                    @if($currentStep = $workInstance->getCurrentStep())
                                        <div class="mb-3 p-2 bg-blue-50 rounded border border-blue-200">
                                            <p class="text-xs font-medium text-blue-900">
                                                Current: {{ $currentStep->workTemplate?->name ?? 'Step ' . ($currentStep->step_index + 1) }}
                                            </p>
                                            @if($isSeller)
                                                <div class="flex gap-2 mt-2">
                                                    @if(!$currentStep->isInProgress())
                                                        <form action="{{ route('orders.work.steps.start', [$workInstance->order, $currentStep]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 text-xs bg-blue-600 text-white font-medium rounded hover:bg-blue-700 transition">
                                                                Start
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($currentStep->isInProgress())
                                                        <form action="{{ route('orders.work.steps.complete', [$workInstance->order, $currentStep]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white font-medium rounded hover:bg-green-700 transition">
                                                                Complete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @elseif($isBuyer)
                                                <p class="text-xs text-gray-600 mt-2 italic">👀 Waiting for seller to complete this step</p>
                                            @endif
                                        </div>
                                    @elseif($workInstance->isCompleted())
                                        <div class="mb-3 p-2 bg-green-50 rounded border border-green-200">
                                            <p class="text-xs font-medium text-green-900">✓ All steps completed</p>
                                        </div>
                                    @endif

                                    <!-- Step Summary -->
                                    <div class="flex justify-between text-xs text-gray-600 mb-3 pb-3 border-b border-gray-100">
                                        <span>{{ $workInstance->workInstanceSteps()->count() }} total steps</span>
                                        <span>{{ $workInstance->getCompletedSteps()->count() }} completed</span>
                                        <span>{{ $workInstance->workInstanceSteps()->where('status', '!=', 'completed')->count() }} remaining</span>
                                    </div>

                                    <!-- Action Button -->
                                    <a href="{{ route('orders.work.show', $workInstance->order) }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline transition">
                                        View Full Details →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
