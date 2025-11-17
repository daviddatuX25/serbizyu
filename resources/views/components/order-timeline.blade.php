<div class="relative">
    <div class="absolute left-0 h-full border-l-2 border-gray-200"></div>
    @foreach (\App\Enums\OrderStatus::cases() as $status)
        <div class="relative mb-8 pl-8">
            <div class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full bg-white border-2 {{ $order->status->value === $status->value ? 'border-blue-500' : 'border-gray-300' }}">
                @if ($order->status->value === $status->value)
                    <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                @else
                    <span class="h-3 w-3 rounded-full bg-gray-300"></span>
                @endif
            </div>
            <div class="flex items-center">
                <p class="text-md font-medium text-gray-900">{{ ucfirst($status->value) }}</p>
                @if ($order->status->value === $status->value)
                    <span class="ml-2 text-sm text-gray-500">- Current Status</span>
                @endif
            </div>
        </div>
    @endforeach
</div>
