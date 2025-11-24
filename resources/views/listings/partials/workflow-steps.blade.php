{{-- resources/views/listings/partials/workflow-steps.blade.php --}}
@if ($workflowTemplate && $workflowTemplate->workTemplates->count() > 0)
    <div class="pt-4 border-t" x-data="{ expanded: false }">
        <div class="flex justify-between items-center">
            <h3 class="font-bold text-xl text-gray-800">What to Expect (Workflow)</h3>
            <button @click="expanded = !expanded" class="text-sm font-medium text-blue-600 hover:underline">
                <span x-show="!expanded">Show Details</span>
                <span x-show="expanded" x-cloak>Hide Details</span>
            </button>
        </div>
        
        <div class="mt-3 text-sm text-gray-600">
            <p x-show="!expanded" class="line-clamp-2">
                {{ $workflowTemplate->workTemplates->pluck('name')->take(3)->implode(', ') }}
                {{ $workflowTemplate->workTemplates->count() > 3 ? '…' : '' }}
            </p>
        </div>

        <div x-show="expanded" x-collapse x-cloak>
            <ol class="relative border-l border-gray-200 dark:border-gray-700 mt-4">
                @foreach($workflowTemplate->workTemplates as $index => $step)
                <li class="mb-6 ml-4">
                    <div class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                    <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Step {{ $index + 1 }}</time>
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ $step->name }}</h4>
                    @if($step->description)
                        <p class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ $step->description }}</p>
                    @endif
                </li>
                @endforeach
            </ol>
        </div>
    </div>
@endif
