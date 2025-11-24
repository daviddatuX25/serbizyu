<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Seller Work Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">Your Active Work Instances</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Here you can manage your ongoing work for various orders.
                    </p>

                    <div class="mt-6">
                        @if ($workInstances->isEmpty())
                            <p>You have no active work instances.</p>
                        @else
                            <ul role="list" class="divide-y divide-gray-100">
                                @foreach ($workInstances as $workInstance)
                                    <li class="flex justify-between gap-x-6 py-5">
                                        <div class="flex min-w-0 gap-x-4">
                                            <div class="min-w-0 flex-auto">
                                                <p class="text-sm font-semibold leading-6 text-gray-900">Work Instance #{{ $workInstance->id }}</p>
                                                <p class="mt-1 truncate text-xs leading-5 text-gray-500">Order #{{ $workInstance->order->id ?? 'N/A' }} - Status: {{ $workInstance->status }}</p>
                                            </div>
                                        </div>
                                        <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                            <a href="{{ route('work-instances.show', $workInstance) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900">View Work</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
