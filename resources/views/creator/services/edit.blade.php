<x-creator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Services') }}
            </h2>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:service-form 
                :service="$service"
                :categories="$categories"
                :addresses="$addresses"
                :key="'service-form-'.$service->id"
            />
        </div>
    </div>
</x-creator-layout>
