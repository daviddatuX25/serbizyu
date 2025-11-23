<x-creator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Services') }}
            </h2>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:service-form 
                :categories="$categories"
                :workflowTemplates="$workflowTemplates"
                :addresses="$addresses"
                :key="'service-form-create'"
            />
        </div>
    </div>
</x-creator-layout>