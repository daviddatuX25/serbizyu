<!-- Workflow Section -->
<div>
    <label for="workflow_template_id" class="block text-sm font-medium text-gray-700 mb-1">
        Workflow @if(isset($is_optional) && $is_optional) (Optional) @endif
    </label>
    <select wire:model.live="workflow_template_id" id="workflow_template_id"
        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <option value="">Select a workflow...</option>
        @foreach ($workflowTemplates as $template)
            <option value="{{ $template['id'] }}">{{ $template['name'] }}</option>
        @endforeach
    </select>
    @error('workflow_template_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    
    @if($workflow_template_id)
        @php
            $selectedWorkflow = collect($workflowTemplates)->firstWhere('id', $workflow_template_id);
        @endphp
        @if($selectedWorkflow)
            <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                <p class="text-sm text-gray-700"><strong>{{ $selectedWorkflow['name'] }}</strong></p>
                <p class="text-xs text-gray-600 mt-1">{{ $selectedWorkflow['description'] }}</p>
                @if (count($selectedWorkflow['workTemplates']) > 0)
                    <div class="mt-2">
                        <span class="text-xs font-semibold text-gray-700">Steps:</span>
                        <ul class="list-disc list-inside text-xs text-gray-600">
                            @foreach ($selectedWorkflow['workTemplates'] as $work)
                                <li>{{ $work['name'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    @endif
    
    <div class="mt-2 text-right">
        <a href="{{ route('creator.workflows.create') }}" target="_blank" class="text-xs text-gray-600 hover:text-blue-700">
            + Create New Workflow
        </a>
    </div>
</div>
