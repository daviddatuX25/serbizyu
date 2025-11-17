<x-app-layout>
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
</x-app-layout>