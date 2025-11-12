<div>
    <!-- Workflow Details Section -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Workflow Details</h3>

            <div class="space-y-4">
                <div>
                    <label for="workflow_name" class="block text-sm font-medium text-gray-700">Workflow Name</label>
                    <input
                        type="text"
                        id="workflow_name"
                        wire:model.defer="name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                        focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="workflow_description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea
                        id="workflow_description"
                        wire:model.defer="description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                        focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <label for="is_public" class="flex items-center">
                        <input id="is_public" type="checkbox" wire:model.defer="is_public" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Make Public</span>
                    </label>
                    <button
                        wire:click="save"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Save Workflow
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow Steps Section -->
    <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Workflow Steps</h3>
                <div>
                    <button
                        wire:click="addStep"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Add New Step
                    </button>
                    <button
                        wire:click="openCatalog"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 ml-2">
                        Add From Catalog
                    </button>
                </div>
            </div>

            <!-- SORTABLE LIST - Corrected for Livewire 4 -->
            <ul wire:sortable="updateStepOrder" class="space-y-4">
                @forelse ($workTemplates as $workTemplate)
                    <li
                        wire:sortable.item="{{ $workTemplate['id'] }}"
                        wire:key="step-{{ $workTemplate['id'] }}"
                        class="p-4 bg-gray-50 rounded-lg shadow-sm flex justify-between items-center hover:shadow-md transition-shadow">
                        <div class="flex items-center flex-1">
                            <span wire:sortable.handle class="cursor-grab active:cursor-grabbing text-gray-400 mr-4 text-lg font-bold">
                                ⋮⋮
                            </span>
                            <div>
                                <p class="font-semibold">{{ $workTemplate['name'] }}</p>
                                <p class="text-sm text-gray-600">{{ $workTemplate['description'] }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-4">
                            <button
                                wire:click="editStep({{ $workTemplate['id'] }})"
                                class="text-indigo-600 hover:text-indigo-900 font-medium">
                                Edit
                            </button>
                            <button
                                wire:click="deleteStep({{ $workTemplate['id'] }})"
                                class="text-red-600 hover:text-red-900 font-medium">
                                Delete
                            </button>
                        </div>
                    </li>
                @empty
                    <li class="text-center text-gray-500 py-8">
                        No steps yet. Add one to get started.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Work Catalog Modal -->
    @if($showCatalogModal)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Add Step From Catalog</h3>
                </div>
                <div class="p-6 max-h-96 overflow-y-auto">
                    <ul class="space-y-4">
                        @forelse ($workCatalogs as $catalog)
                            <li class="p-4 bg-gray-50 rounded-lg shadow-sm flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">{{ $catalog->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $catalog->description }}</p>
                                </div>
                                <div>
                                    <button
                                        wire:click="addStepFromCatalog({{ $catalog->id }})"
                                        class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                                        Add
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li class="text-center text-gray-500 py-4">
                                No items in catalog.
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="p-4 bg-gray-50 border-t flex justify-end">
                    <button
                        wire:click="closeCatalog"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Step Modal -->
    @if($showEditStepModal)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Edit Step</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="editing_step_name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            type="text"
                            id="editing_step_name"
                            wire:model.defer="editingStep.name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="editing_step_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            id="editing_step_description"
                            wire:model.defer="editingStep.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 border-t flex justify-end space-x-2">
                    <button
                        wire:click="closeEditStepModal"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Cancel
                    </button>
                    <button
                        wire:click="updateStep"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>