<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Work Instance Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">Work Instance #{{ $workInstance->id }}</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Details of the work instance for Order #{{ $workInstance->order->id ?? 'N/A' }}.
                    </p>

                    <div class="mt-6 border-t border-gray-100">
                        <dl class="divide-y divide-gray-100">
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Status</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $workInstance->status }}</dd>
                            </div>
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Current Step</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $workInstance->current_step_index + 1 }}</dd>
                            </div>
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Started At</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $workInstance->started_at }}</dd>
                            </div>
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Completed At</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $workInstance->completed_at }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900">Work Steps</h4>
                        <livewire:work-progress :workInstance="$workInstance" />
                    </div>

                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900">Activity Thread</h4>
                        <livewire:activity-thread :activityThread="$workInstance->activityThread" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
