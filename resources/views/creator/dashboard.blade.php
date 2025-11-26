<x-creator-layout title="Creator Dashboard">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Creator Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Top Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Orders</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $orderStats['total'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4v11H3zM21 3v18h-4V3zM11 7h4v14h-4z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Pending Orders</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $orderStats['pending'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Completed Orders</p>
                            <p class="text-3xl font-bold text-green-600">{{ $orderStats['completed'] ?? 0 }}</p>
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
                            <p class="text-sm text-gray-600 font-medium">Your Work Items</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $workStats['total'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Orders with Activities -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Orders</h3>
                        @if(isset($orders) && $orders->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($orders as $order)
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:shadow-md transition">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">Order #{{ $order->id }}</a>
                                                <p class="text-xs text-gray-600">{{ $order->service?->title ?? 'Service' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                                <p class="text-xs text-gray-500 mt-1">₱{{ number_format($order->total_amount, 2) }}</p>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-600 mb-2">
                                            Buyer: <span class="font-medium">{{ $order->buyer->name ?? 'N/A' }}</span> |
                                            Seller: <span class="font-medium">{{ $order->seller->name ?? 'N/A' }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $order->created_at?->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No recent orders</p>
                        @endif
                        <div class="mt-4 text-right">
                            <a href="{{ route('orders.index') }}" class="inline-flex px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">View all orders</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Work Items with Steps & Activities -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Work Items</h3>
                        @if(isset($workInstances) && $workInstances->isNotEmpty())
                            <div class="space-y-5">
                                @foreach($workInstances as $work)
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:shadow-md transition">
                                        <!-- Work Header -->
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <a href="{{ route('orders.work.show', $work->order) }}" class="text-sm font-semibold text-gray-900 hover:text-purple-600">Order #{{ $work->order->id }}</a>
                                                <p class="text-xs text-gray-600">{{ $work->order->service?->title ?? 'Service' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded {{ $work->status === 'completed' ? 'bg-green-100 text-green-800' : ($work->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $work->status)) }}
                                                </span>
                                                <p class="text-xs text-gray-500 mt-1">{{ $work->getProgressPercentage() ?? 0 }}% complete</p>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mb-3">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-xs font-medium text-gray-600">Progress</span>
                                                <span class="text-xs text-gray-500">{{ $work->workInstanceSteps()->count() }} steps</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $work->getProgressPercentage() ?? 0 }}%"></div>
                                            </div>
                                        </div>

                                        <!-- Work Steps -->
                                        @if($work->workInstanceSteps()->exists())
                                            <div class="mb-3 space-y-2">
                                                <p class="text-xs font-semibold text-gray-700">Steps:</p>
                                                @foreach($work->workInstanceSteps as $step)
                                                    <div class="bg-white border border-gray-100 rounded p-2 ml-2">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-2">
                                                                @if($step->status === 'completed')
                                                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                                                @elseif($step->status === 'in_progress')
                                                                    <svg class="w-4 h-4 text-blue-600 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg>
                                                                @else
                                                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 7a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" /></svg>
                                                                @endif
                                                                <span class="text-xs font-medium text-gray-800">{{ $step->workTemplate?->name ?? 'Step ' . ($step->step_index + 1) }}</span>
                                                            </div>
                                                            <span class="text-xs text-gray-500">{{ ucfirst($step->status) }}</span>
                                                        </div>

                                                        <!-- Step Activities -->
                                                        @if($step->activityThread && $step->activityThread->messages->isNotEmpty())
                                                            <div class="mt-2 pt-2 border-t border-gray-100">
                                                                <p class="text-xs text-gray-600 mb-1">Activity:</p>
                                                                @foreach($step->activityThread->messages->take(2) as $message)
                                                                    <p class="text-xs text-gray-600 line-clamp-2">💬 {{ $message->content }}</p>
                                                                @endforeach
                                                                @if($step->activityThread->messages->count() > 2)
                                                                    <p class="text-xs text-gray-500 italic">+{{ $step->activityThread->messages->count() - 2 }} more</p>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="mt-3 text-right">
                                            <a href="{{ route('orders.work.show', $work->order) }}" class="text-xs text-purple-600 hover:text-purple-700 font-medium">View full details →</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No recent work items</p>
                        @endif
                        <div class="mt-4 text-right">
                            <a href="{{ route('orders.index') }}" class="inline-flex px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">View all work in orders</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
