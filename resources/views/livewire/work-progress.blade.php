<div>
    <ul role="list" class="divide-y divide-gray-100">
        @foreach ($workInstance->workInstanceSteps as $step)
            <li class="flex justify-between gap-x-6 py-5">
                <div class="flex min-w-0 gap-x-4">
                    <div class="min-w-0 flex-auto">
                        <p class="text-sm font-semibold leading-6 text-gray-900">Step {{ $step->step_index + 1 }}: {{ $step->workTemplate->name ?? 'N/A' }}</p>
                        <p class="mt-1 truncate text-xs leading-5 text-gray-500">Status: {{ $step->status }}</p>
                    </div>
                </div>
                <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                    @if ($step->status === 'pending')
                        <button wire:click="startStep({{ $step->id }})" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Start Step
                        </button>
                    @elseif ($step->status === 'in_progress')
                        <button wire:click="completeStep({{ $step->id }})" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Complete Step
                        </button>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</div>
