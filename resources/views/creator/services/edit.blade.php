<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Edit Service
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">

                <livewire:service-form 
                    :service="$service"
                    :categories="$categories"
                    :workflowTemplates="$workflowTemplates"
                />

            </div>
        </div>
    </div>
</x-app-layout>
