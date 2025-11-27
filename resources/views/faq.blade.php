<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Header Section -->
        <div class="relative py-16 px-4 sm:px-6 lg:px-8 border-b border-slate-700/50">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    Frequently Asked Questions
                </h1>
                <p class="text-xl text-slate-400 mb-8">
                    Find answers to common questions about Serbizyu
                </p>

                <!-- Search Bar -->
                <div class="relative max-w-2xl mx-auto">
                    <input type="text" id="searchInput" placeholder="Search FAQs..."
                        class="w-full px-6 py-3 bg-slate-700/50 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    <svg class="absolute right-4 top-3.5 w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Categories Navigation -->
        <div class="sticky top-0 z-40 bg-slate-800/80 backdrop-blur-md border-b border-slate-700/50 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="flex overflow-x-auto gap-3 py-4 no-scrollbar">
                    <button onclick="filterCategory('all')"
                        class="category-btn active px-6 py-2 rounded-full font-semibold whitespace-nowrap transition-all duration-300 bg-blue-600 text-white">
                        All
                    </button>
                    @foreach ($categories as $category)
                        <button onclick="filterCategory('{{ $loop->index }}')"
                            class="category-btn px-6 py-2 rounded-full font-semibold whitespace-nowrap transition-all duration-300 bg-slate-700/50 text-slate-300 hover:bg-slate-600">
                            {{ $category['icon'] }} {{ $category['category'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- FAQ Content -->
        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                @forelse ($categories as $categoryIndex => $category)
                    <div class="category-section mb-12" data-category="{{ $loop->index }}">
                        <!-- Category Header -->
                        <div class="mb-6 flex items-center gap-3">
                            <span class="text-3xl">{{ $category['icon'] }}</span>
                            <h2 class="text-3xl font-bold text-white">{{ $category['category'] }}</h2>
                        </div>

                        <!-- Questions Accordion -->
                        <div class="space-y-3">
                            @forelse ($category['questions'] as $qIndex => $item)
                                <div class="faq-item bg-slate-700/30 border border-slate-600/50 rounded-lg overflow-hidden hover:border-slate-500 transition-all duration-300"
                                    data-question="{{ strtolower($item['question']) }}{{ strtolower($item['answer']) }}">
                                    <button onclick="toggleAccordion(this)"
                                        class="w-full px-6 py-4 flex items-start justify-between hover:bg-slate-700/50 transition-colors duration-200">
                                        <span class="text-lg font-semibold text-white text-left">{{ $item['question'] }}</span>
                                        <svg class="accordion-icon w-5 h-5 text-slate-400 flex-shrink-0 ml-4 transition-transform duration-300"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                        </svg>
                                    </button>

                                    <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                        <div class="px-6 py-4 border-t border-slate-600/50 bg-slate-800/50 text-slate-300 leading-relaxed whitespace-pre-wrap">
                                            {{ nl2br($item['answer']) }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-slate-400">No questions in this category.</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <p class="text-xl text-slate-400">No FAQs available.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Support CTA -->
        <div class="bg-gradient-to-r from-blue-600/20 to-cyan-600/20 border-t border-slate-700/50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h3 class="text-2xl font-bold text-white mb-4">Didn't find your answer?</h3>
                <p class="text-slate-400 mb-6">Contact our support team for immediate assistance</p>
                <a href="#"
                    class="inline-block px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-blue-600/30">
                    Contact Support
                </a>
            </div>
        </div>
    </div>

    <style>
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .accordion-content {
            max-height: 0;
        }

        .accordion-content.open {
            max-height: 800px;
        }

        .accordion-icon.open {
            transform: rotate(180deg);
        }
    </style>

    <script>
        const searchInput = document.getElementById('searchInput');
        const faqItems = document.querySelectorAll('.faq-item');
        const categoryButtons = document.querySelectorAll('.category-btn');
        const categorySections = document.querySelectorAll('.category-section');

        // Accordion Toggle
        function toggleAccordion(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('.accordion-icon');

            // Close other accordions
            document.querySelectorAll('.accordion-content.open').forEach(item => {
                if (item !== content) {
                    item.classList.remove('open');
                    item.previousElementSibling.querySelector('.accordion-icon').classList.remove('open');
                }
            });

            // Toggle current
            content.classList.toggle('open');
            icon.classList.toggle('open');
        }

        // Search Functionality
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            let hasVisibleItems = false;

            faqItems.forEach(item => {
                const searchData = item.getAttribute('data-question');
                const isVisible = searchData.includes(searchTerm);

                item.style.display = isVisible ? 'block' : 'none';
                if (isVisible) {
                    hasVisibleItems = true;
                }
            });

            // Show/hide category sections based on visible items
            categorySections.forEach(section => {
                const visibleItems = Array.from(section.querySelectorAll('.faq-item')).filter(item => item.style.display !== 'none');
                section.style.display = visibleItems.length > 0 ? 'block' : 'none';
            });
        });

        // Category Filter
        function filterCategory(categoryIndex) {
            categoryButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('bg-slate-700/50', 'text-slate-300');
            });
            event.target.classList.add('active', 'bg-blue-600', 'text-white');
            event.target.classList.remove('bg-slate-700/50', 'text-slate-300');

            if (categoryIndex === 'all') {
                categorySections.forEach(section => section.style.display = 'block');
            } else {
                categorySections.forEach((section, index) => {
                    section.style.display = index == categoryIndex ? 'block' : 'none';
                });
            }

            // Reset accordion states
            document.querySelectorAll('.accordion-content.open').forEach(item => {
                item.classList.remove('open');
                item.previousElementSibling.querySelector('.accordion-icon').classList.remove('open');
            });

            searchInput.value = '';
        }
    </script>
</x-app-layout>
