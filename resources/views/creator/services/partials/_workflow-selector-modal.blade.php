<div 
    x-show="showWorkflowModal"
    @keydown.escape.window="showWorkflowModal = false"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <!-- Modal backdrop -->
    <div 
        x-show="showWorkflowModal"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-800 bg-opacity-75 transition-opacity"
        @click="showWorkflowModal = false"
    ></div>

    <!-- Modal -->
    <div 
        x-show="showWorkflowModal"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative inline-block bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] flex flex-col transform transition-all sm:my-8 sm:align-middle"
    >
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold" id="modal-title">Select a Workflow</h3>
            <button @click="showWorkflowModal = false" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="p-4 overflow-y-auto">
            <div class="space-y-4">
                @forelse ($workflowTemplates as $template)
                    <div 
                        @click="$wire.set('workflow_template_id', {{ $template->id }}); showWorkflowModal = false;"
                        class="p-4 border rounded-lg cursor-pointer hover:bg-gray-100 hover:border-blue-500 {{ $selectedWorkflowId == $template->id ? 'bg-blue-50 border-blue-500' : '' }}"
                    >
                        <h4 class="font-bold text-lg">{{ $template->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $template->description }}</p>
                        @if ($template->workTemplates->count() > 0)
                            <div class="mt-2">
                                <span class="text-xs font-semibold text-gray-700">Steps:</span>
                                <ul class="list-disc list-inside text-sm text-gray-600">
                                    @foreach ($template->workTemplates as $work)
                                        <li>{{ $work->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mt-2">No steps defined for this workflow.</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">You haven't created any workflow templates yet.</p>
                        <a href="{{ route('creator.workflows.create') }}" class="mt-2 inline-block text-blue-500 hover:underline">Create one now</a>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="p-4 border-t bg-gray-50 rounded-b-lg text-right">
            <a href="{{ route('creator.workflows.create') }}" target="_blank" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                Create New Workflow
            </a>
        </div>
    </div>
</div>
