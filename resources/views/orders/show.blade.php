<x-creator-layout title="Order Details">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
                            <p class="text-gray-600 mt-1">{{ $order->created_at->format('F j, Y, g:i a') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-600">Status</p>
                            <x-order-status-badge :status="$order->status" type="status" class="mt-1" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Navigation Tabs -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-b border-gray-200">
                        <div class="flex border-b border-gray-200">
                            <button class="tab-button flex-1 py-4 px-6 text-center font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition active" data-tab="details">
                                <i class="fas fa-info-circle mr-2"></i> Details
                            </button>
                            <button class="tab-button flex-1 py-4 px-6 text-center font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" data-tab="messages">
                                <i class="fas fa-comments mr-2"></i> Messages
                                @php
                                    $unreadCount = $order->messageThread?->messages()->where('read_at', null)->where('sender_id', '!=', Auth::id())->count() ?? 0;
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="ml-2 inline-block bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                                @endif
                            </button>
                        </div>
                    </div>

                    <!-- Details Tab -->
                    <div id="details-tab" class="tab-content space-y-6">
                        <!-- Service Details -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Service Details</h2>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm text-gray-600">Service</p>
                                            <p class="text-lg font-medium text-gray-900">{{ $order->service->name }}</p>
                                        </div>
                                        <p class="text-2xl font-bold text-gray-900">${{ number_format($order->price, 2) }}</p>
                                    </div>
                                    @if($order->service->description)
                                        <div>
                                            <p class="text-sm text-gray-600">Description</p>
                                            <p class="text-gray-700">{{ $order->service->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Billing</h2>
                            <dl class="mt-2 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Price:</dt>
                                    <dd class="font-medium text-gray-800">₱{{ number_format($order->price, 2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Platform Fee:</dt>
                                    <dd class="font-medium text-gray-800">₱{{ number_format($order->platform_fee, 2) }}</dd>
                                </div>
                                <div class="flex justify-between font-bold text-base">
                                    <dt class="text-gray-800">Total:</dt>
                                    <dd class="text-green-600">₱{{ number_format($order->total_amount, 2) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Work Progress -->
                        @if($order->workInstance)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6 bg-white border-b border-gray-200">
                                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Work Progress</h2>
                                    <div class="space-y-3">
                                        <div>
                                            @php
                                                $totalSteps = $order->workInstance->workInstanceSteps->count();
                                                $currentStep = $order->workInstance->current_step_index + 1;
                                                $progress = ($currentStep / $totalSteps) * 100;
                                            @endphp
                                            <p class="text-sm text-gray-600 mb-2">
                                                Progress: <span class="font-medium text-gray-900">{{ $currentStep }} / {{ $totalSteps }}</span>
                                            </p>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            Current Status: <span class="font-medium text-gray-900 capitalize">{{ $order->workInstance->status }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Messages Tab -->
                    <div id="messages-tab" class="tab-content hidden">
                        @if($order->messageThread)
                            <livewire:order-chat :order="$order" />
                        @else
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6 text-center">
                                    <p class="text-gray-600">No messages yet. Start a conversation!</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    </div>

                    <!-- Participants -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Buyer -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Buyer</h3>
                                <div class="space-y-2">
                                    <p class="font-medium text-gray-900">{{ $order->buyer->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->buyer->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Seller -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Seller</h3>
                                <div class="space-y-2">
                                    <p class="font-medium text-gray-900">{{ $order->seller->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->seller->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Payment Summary -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>
                            <div class="space-y-3 border-b border-gray-200 pb-4 mb-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service Price</span>
                                    <span class="font-medium">${{ number_format($order->price, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Platform Fee (5%)</span>
                                    <span class="font-medium">${{ number_format($order->platform_fee, 2) }}</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-semibold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Payment Status</p>
                                <x-order-status-badge :status="$order->payment_status" type="payment" class="w-full text-center" />
                            </div>

                            @if($order->payment_status === 'pending' && $order->status === 'pending')
                                <a href="{{ route('payments.checkout', $order) }}" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition text-center block">
                                    Proceed to Payment
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($order->status === 'pending' && Auth::user()->id === $order->buyer_id)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase mb-3">Actions</h3>
                                <form action="{{ route('orders.cancel', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-700 font-medium py-2 px-4 rounded-lg transition" onclick="return confirm('Are you sure you want to cancel this order?')">
                                        Cancel Order
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Activity Log (if available) -->
                    @if(isset($order->activity))
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase mb-3">Recent Activity</h3>
                                <div class="space-y-2 text-sm">
                                    <p class="text-gray-600">Order created</p>
                                    <p class="text-gray-600">{{ $order->created_at->format('M d, Y \a\t g:i a') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');

                // Hide all tabs
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Remove active state from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.classList.remove('border-blue-500');
                    btn.classList.add('border-transparent');
                });

                // Show selected tab
                document.getElementById(tabName + '-tab').classList.remove('hidden');

                // Add active state to clicked button
                this.classList.add('active');
                this.classList.add('border-blue-500');
                this.classList.remove('border-transparent');
            });
        });
    });
</script>
