<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:service-form 
                :service="$service"
                :categories="$categories"
                :workflowTemplates="$workflowTemplates"
                :addresses="$addresses"
                :key="'service-form-'.$service->id"
            />
        </div>
    </div>
</x-app-layout>
