<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Hero Section with Animated Background -->
        <div class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <!-- Animated gradient orbs -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
            </div>

            <div class="relative max-w-5xl mx-auto text-center">
                <div class="inline-block mb-4 px-4 py-2 bg-blue-500/10 border border-blue-500/20 rounded-full">
                    <span class="text-blue-400 text-sm font-semibold">Welcome to Serbizyu</span>
                </div>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
                    About <span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">Serbizyu</span>
                </h1>
                <p class="text-xl md:text-2xl text-slate-400 max-w-3xl mx-auto leading-relaxed">
                    Connecting skilled service providers with customers who need them across the Philippines
                </p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                @forelse ($sections as $index => $section)
                    <section class="mb-20 scroll-mt-24" id="section-{{ $index }}">
                        <!-- Section Header with Icon -->
                        <div class="mb-10">
                            <div class="flex items-center gap-4 mb-4">
                                @if(str_contains($section['title'], 'Story'))
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                        <span class="text-2xl">📖</span>
                                    </div>
                                @elseif(str_contains($section['title'], 'Mission'))
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                        <span class="text-2xl">🎯</span>
                                    </div>
                                @elseif(str_contains($section['title'], 'Different'))
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                                        <span class="text-2xl">✨</span>
                                    </div>
                                @elseif(str_contains($section['title'], 'Values'))
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
                                        <span class="text-2xl">💎</span>
                                    </div>
                                @elseif(str_contains($section['title'], 'Team'))
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                                        <span class="text-2xl">👥</span>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-slate-600 to-slate-500 rounded-xl flex items-center justify-center">
                                        <span class="text-2xl">📌</span>
                                    </div>
                                @endif
                                <div>
                                    <h2 class="text-3xl md:text-4xl font-bold text-white">{{ $section['title'] }}</h2>
                                </div>
                            </div>
                            <div class="h-1 w-24 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full ml-16"></div>
                        </div>

                        <!-- Section Content -->
                        <div class="ml-0 md:ml-16">
                            @php
                                $paragraphs = explode("\n\n", $section['content']);
                            @endphp

                            <!-- Check if this is the "What Makes Us Different" section -->
                            @if(str_contains($section['title'], 'Different'))
                                <div class="grid md:grid-cols-2 gap-6">
                                    @foreach ($paragraphs as $paragraph)
                                        @if (str_contains($paragraph, '###'))
                                            @php
                                                preg_match('/###\s*([🎯🔒⚡📱💬⭐])\s*\*\*(.+?)\*\*\s*(.+)/s', $paragraph, $matches);
                                                $emoji = $matches[1] ?? '✨';
                                                $title = $matches[2] ?? '';
                                                $content = $matches[3] ?? $paragraph;
                                            @endphp
                                            <div class="group bg-gradient-to-br from-slate-800/50 to-slate-800/30 border border-slate-700/50 rounded-2xl p-6 hover:border-blue-500/50 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300">
                                                <div class="text-3xl mb-3">{{ $emoji }}</div>
                                                <h3 class="text-xl font-bold text-white mb-3 group-hover:text-blue-400 transition-colors">{{ $title }}</h3>
                                                <p class="text-slate-400 leading-relaxed">{{ trim($content) }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                            <!-- Check if this is the "Our Values" section -->
                            @elseif(str_contains($section['title'], 'Values'))
                                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach ($paragraphs as $paragraph)
                                        @if (str_contains($paragraph, '###'))
                                            @php
                                                preg_match('/###\s*\*\*(.+?)\*\*\s*(.+)/s', $paragraph, $matches);
                                                $title = $matches[1] ?? '';
                                                $content = $matches[2] ?? $paragraph;
                                            @endphp
                                            <div class="bg-slate-800/40 border border-slate-700/50 rounded-xl p-6 hover:border-slate-600 transition-all duration-300">
                                                <h3 class="text-lg font-bold text-white mb-2">{{ $title }}</h3>
                                                <p class="text-slate-400 text-sm leading-relaxed">{{ trim($content) }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                            <!-- Check if this is "The Team" section -->
                            @elseif(str_contains($section['title'], 'Team'))
                                <div class="grid md:grid-cols-2 gap-6">
                                    @foreach ($paragraphs as $paragraph)
                                        @if (str_contains($paragraph, '###'))
                                            @php
                                                preg_match('/###\s*\*\*(.+?)\*\*\s*\*(.+?)\*\s*(.+)/s', $paragraph, $matches);
                                                $name = $matches[1] ?? '';
                                                $role = $matches[2] ?? '';
                                                $bio = $matches[3] ?? '';
                                            @endphp
                                            <div class="bg-gradient-to-br from-slate-800/60 to-slate-800/40 border border-slate-700/50 rounded-2xl p-6 hover:border-blue-500/50 transition-all duration-300">
                                                <div class="flex items-start gap-4">
                                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="text-2xl font-bold text-white">{{ substr($name, 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-xl font-bold text-white mb-1">{{ $name }}</h3>
                                                        <p class="text-blue-400 text-sm font-medium mb-3">{{ $role }}</p>
                                                        <p class="text-slate-400 text-sm leading-relaxed">{{ trim($bio) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                            <!-- Check if this is "Current Features" or "Coming Soon" -->
                            @elseif(str_contains($section['title'], 'Features') || str_contains($section['title'], 'Coming Soon'))
                                <div class="grid md:grid-cols-2 gap-4">
                                    @foreach ($paragraphs as $paragraph)
                                        @if (preg_match('/^[✅🚀]\s*\*\*(.+?)\*\*\s*-\s*(.+)/', $paragraph, $matches))
                                            <div class="flex items-start gap-3 bg-slate-800/30 border border-slate-700/30 rounded-lg p-4 hover:bg-slate-800/50 transition-all duration-200">
                                                <span class="text-2xl flex-shrink-0">{{ str_contains($paragraph, '✅') ? '✅' : '🚀' }}</span>
                                                <div>
                                                    <h4 class="text-white font-semibold mb-1">{{ $matches[1] }}</h4>
                                                    <p class="text-slate-400 text-sm">{{ $matches[2] }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                            <!-- Default paragraph rendering -->
                            @else
                                <div class="space-y-6">
                                    @foreach ($paragraphs as $paragraph)
                                        @if (!empty(trim($paragraph)))
                                            <div class="prose prose-lg prose-invert max-w-none">
                                                <p class="text-slate-300 leading-relaxed">{!! nl2br(e($paragraph)) !!}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </section>
                @empty
                    <div class="text-center py-20">
                        <div class="text-6xl mb-4">📭</div>
                        <p class="text-xl text-slate-400">No content available.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- CTA Section with Enhanced Design -->
        <div class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <!-- Background gradient -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 via-cyan-600/20 to-blue-600/20"></div>
            <div class="absolute inset-0 backdrop-blur-3xl"></div>

            <!-- Border -->
            <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-blue-500/50 to-transparent"></div>

            <div class="relative max-w-4xl mx-auto text-center">
                <div class="mb-6">
                    <span class="text-5xl">🚀</span>
                </div>
                <h3 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Get Started?</h3>
                <p class="text-lg text-slate-400 mb-10 max-w-2xl mx-auto">
                    Join thousands of service providers and customers on Serbizyu today
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('auth.signin') }}"
                        class="group px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-xl transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/50 hover:scale-105">
                        <span class="flex items-center justify-center gap-2">
                            Get Started
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                    </a>
                    <a href="{{ route('browse') }}"
                        class="px-8 py-4 bg-slate-700/50 hover:bg-slate-600/50 border border-slate-600 hover:border-slate-500 text-white font-semibold rounded-xl transition-all duration-300 backdrop-blur-sm">
                        Browse Services
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Stats (Optional - if you want to add) -->
        <div class="border-t border-slate-700/50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-white mb-2">1000+</div>
                        <div class="text-slate-400 text-sm">Active Services</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-white mb-2">5000+</div>
                        <div class="text-slate-400 text-sm">Happy Customers</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-white mb-2">98%</div>
                        <div class="text-slate-400 text-sm">Satisfaction Rate</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-white mb-2">24/7</div>
                        <div class="text-slate-400 text-sm">Support Available</div>
                    </div>
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

        .delay-1000 {
            animation-delay: 1s;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.4;
            }
            50% {
                opacity: 0.6;
            }
        }

        .animate-pulse {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</x-app-layout>
