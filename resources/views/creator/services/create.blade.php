<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Service') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                
                <livewire:service-form 
                    :categories="$categories"
                    :workflowTemplates="$workflowTemplates"
                    :addresses="$addresses"
                    :key="'service-form-create'"  {{-- unique key for create --}}
                    
                />

            </div>
        </div>
    </div>
</x-app-layout>