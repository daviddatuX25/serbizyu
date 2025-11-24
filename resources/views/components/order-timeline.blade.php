@props(['order'])

<div class="flow-root">
    <ul role="list" class="-mb-8">
        @foreach (['pending', 'in_progress', 'completed', 'cancelled', 'disputed'] as $status)
            @php
                $isCurrent = $order->status === $status;
                $isCompleted = array_search($status, ['pending', 'in_progress', 'completed', 'cancelled', 'disputed']) < array_search($order->status, ['pending', 'in_progress', 'completed', 'cancelled', 'disputed']);
            @endphp
            <li>
                <div class="relative pb-8">
                    @if (!$loop->last)
                        <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    @endif
                    <div class="relative flex space-x-3">
                        <div>
                            @if ($isCompleted)
                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @elseif ($isCurrent)
                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13.25a.75.75 0 00-1.5 0V9.5a.75.75 0 00.75.75h3.25a.75.75 0 000-1.5h-2.5V4.75z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @else
                                <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94l-1.72-1.72z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                        <div class="flex min-w-0 flex-1 justify-between pt-1.5">
                            <div>
                                <p class="text-sm text-gray-500">
                                    <a href="#" class="font-medium text-gray-900">{{ ucfirst($status) }}</a>
                                </p>
                            </div>
                            <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                @if ($isCurrent)
                                    <time datetime="2023-01-23T10:32">Current</time>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
