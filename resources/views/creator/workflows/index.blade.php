<x-creator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Workflows') }}
            </h2>
            
        </div>
    </x-slot>
    <a href="{{ route('creator.workflows.create') }}" 
        class="w-[200px] px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Create New Workflow</span>
    </a>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($workflowTemplates as $workflow)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $workflow->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $workflow->description }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($workflow->is_public)
                                                @can('duplicate', $workflow)
                                                    <form action="{{ route('creator.workflows.duplicate', $workflow) }}" method="POST" class="inline-block ml-4">
                                                        @csrf
                                                        <button type="submit" class="text-blue-600 hover:text-blue-900">Duplicate</button>
                                                    </form>
                                                @endcan
                                            @endif
                                            @if($workflow->creator_id === auth()->id())
                                                <a href="{{ route('creator.workflows.edit', $workflow) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                <form action="{{ route('creator.workflows.destroy', $workflow) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to delete this workflow?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            No workflows found.
                                            <a href="{{ route('creator.workflows.create') }}" class="text-indigo-600 hover:text-indigo-900 underline">Create one now</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="md:hidden grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach($workflowTemplates as $workflow)
                    <x-resource-card
                        :title="$workflow->name"
                        :details="[
                            'Description' => $workflow->description,
                        ]"
                        :actions="[
                            ['label' => 'Edit', 'url' => route('creator.workflows.edit', $workflow), 'class' => 'bg-blue-600 hover:bg-blue-700 text-white'],
                        ]"
                    />
                @endforeach
            </div>
        </div>
    </div>
</x-creator-layout>