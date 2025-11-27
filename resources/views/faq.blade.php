<x-app-layout>
    <div class="bg-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">
                    Frequently Asked Questions
                </h1>
                <p class="mt-4 text-xl text-gray-600">
                    Can't find the answer you're looking for? Reach out to our <a href="#" class="text-blue-600 hover:underline">customer support</a> team.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1">
                    <div class="sticky top-24">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                        <ul class="space-y-2">
                            @foreach($faqs->keys() as $key)
                                <li>
                                    <a href="#{{ \Illuminate\Support\Str::slug($key) }}" class="text-gray-600 hover:text-blue-600 transition-colors">{{ $key }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-3">
                    @foreach($faqs as $section => $questions)
                        <div id="{{ \Illuminate\Support\Str::slug($section) }}" class="mb-12">
                            <h2 class="text-2xl font-bold text-gray-900 border-b-2 border-blue-500 pb-2 mb-6">
                                {{ $section }}
                            </h2>
                            <div class="space-y-4">
                                @foreach($questions as $index => $faq)
                                    <div x-data="{ open: false }" class="bg-white shadow-sm rounded-lg overflow-hidden">
                                        <button @click="open = !open" class="w-full text-left flex justify-between items-center p-6 focus:outline-none">
                                            <span class="text-lg font-medium text-gray-800">{{ $faq['question'] }}</span>
                                            <svg :class="{'transform rotate-180': open}" class="w-6 h-6 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div x-show="open" x-collapse class="px-6 pb-6 text-gray-600">
                                            <p class="whitespace-pre-line">{{ $faq['answer'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
