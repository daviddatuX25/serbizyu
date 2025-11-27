<x-app-layout>
    <div class="bg-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                @foreach($about as $title => $content)
                    <div class="mb-12">
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-4">{{ $title }}</h2>
                        <div class="prose prose-lg text-gray-600">
                            {!! \Illuminate\Support\Str::markdown($content) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
