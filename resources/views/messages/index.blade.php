<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">Your Message Threads</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Select a thread to view messages.
                    </p>

                    {{-- Placeholder for a list of message threads --}}
                    <div class="mt-6">
                        <p>No message threads yet.</p>
                        {{-- <ul class="divide-y divide-gray-200">
                            @foreach ($threads as $thread)
                                <li>
                                    <a href="{{ route('messages.show', $thread) }}" class="block hover:bg-gray-50">
                                        <div class="px-4 py-4 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-indigo-600 truncate">{{ $thread->title ?? 'No Title' }}</p>
                                                <div class="ml-2 flex-shrink-0 flex">
                                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ $thread->updated_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-2 sm:flex sm:justify-between">
                                                <div class="sm:flex">
                                                    <p class="flex items-center text-sm text-gray-500">
                                                        From: {{ $thread->creator->name }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
