<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Header Section -->
        <div class="relative py-16 px-4 sm:px-6 lg:px-8 border-b border-slate-700/50">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    About Serbizyu
                </h1>
                <p class="text-xl text-slate-400">
                    Connecting skilled service providers with customers who need them
                </p>
            </div>
        </div>

        <!-- Content Sections -->
        <div class="py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto space-y-16">
                @forelse ($sections as $section)
                    <section class="scroll-mt-24">
                        <!-- Section Header -->
                        <div class="mb-8">
                            <h2 class="text-3xl md:text-4xl font-bold text-white mb-2">{{ $section['title'] }}</h2>
                            <div class="h-1 w-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded"></div>
                        </div>

                        <!-- Section Content -->
                        <div class="prose prose-invert max-w-none">
                            <div class="space-y-6 text-slate-300 leading-relaxed">
                                @php
                                    $paragraphs = explode("\n\n", $section['content']);
                                @endphp
                                @foreach ($paragraphs as $paragraph)
                                    @if (str_contains($paragraph, '🎯') || str_contains($paragraph, '🔒') || str_contains($paragraph, '⚡') || str_contains($paragraph, '📱') || str_contains($paragraph, '💬') || str_contains($paragraph, '⭐'))
                                        <!-- Feature item with emoji -->
                                        <div class="bg-slate-700/30 border border-slate-600/50 rounded-lg p-6 hover:border-slate-500 transition-all duration-300">
                                            <p class="text-base text-slate-200 leading-relaxed">{!! nl2br(e($paragraph)) !!}</p>
                                        </div>
                                    @else
                                        <p class="text-base text-slate-300 leading-relaxed">{!! nl2br(e($paragraph)) !!}</p>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </section>
                @empty
                    <div class="text-center py-12">
                        <p class="text-xl text-slate-400">No content available.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-blue-600/20 to-cyan-600/20 border-t border-slate-700/50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h3 class="text-2xl font-bold text-white mb-4">Ready to Get Started?</h3>
                <p class="text-slate-400 mb-8">Join thousands of service providers and customers on Serbizyu</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}"
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-blue-600/30">
                        Get Started
                    </a>
                    <a href="{{ route('browse') }}"
                        class="px-8 py-3 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition-all duration-200">
                        Browse Services
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .prose-invert {
            --tw-prose-body: rgb(203 213 225);
            --tw-prose-headings: rgb(255 255 255);
            --tw-prose-links: rgb(59 130 246);
        }
    </style>
</x-app-layout>
