<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Frequently Asked Questions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Bar -->
            <div class="mb-8">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search FAQs..."
                        class="w-full px-4 py-3 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                    <svg class="absolute right-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="space-y-8">
                
                <!-- GENERAL QUESTIONS -->
                <section class="faq-category">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-200">General Questions</h2>
                    <div class="space-y-3">
                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="what is serbizyu">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">What is Serbizyu?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Serbizyu is a local services marketplace connecting customers with service providers in the Philippines. Find services you need or offer your skills to earn.
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="who can use">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">Who can use Serbizyu?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Anyone can use Serbizyu. You can be a customer looking for services, a provider offering your skills, or both at the same time.
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="free cost">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">Is it free to use?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Yes, signing up and browsing is completely free. We only charge a small platform fee on completed transactions.
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="areas locations coverage">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">What areas do you cover?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Currently focused on the Philippines, with plans to expand to more locations.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ACCOUNT & PROFILE -->
                <section class="faq-category">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-200">Account & Profile</h2>
                    <div class="space-y-3">
                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="create account">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">How do I create an account?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Click "Join" in the top menu, enter your name, email, and password. Verify your email and you're ready to go.
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="buyer seller both">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">Can I be both a buyer and seller?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Absolutely! One account lets you book services as a customer and offer services as a provider.
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="reset password">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">How do I reset my password?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Click "Forgot Password" on the login page. Check your email for a reset link.
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="update profile">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">How do I update my profile?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Go to Settings → Profile. You can update your name, contact info, addresses, and notification preferences.
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="multiple addresses">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">Can I have multiple addresses?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Yes. Add addresses in Settings → Addresses. Set one as your primary address for faster bookings.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- PAYMENTS -->
                <section class="faq-category">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-200">Payments</h2>
                    <div class="space-y-3">
                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="payment methods gcash">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">How can customers pay?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Two payment methods: Online (GCash, Credit Card, Bank Transfer via Xendit) and Cash (in-person payment with seller confirmation).
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="online payment safe secure">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">Is online payment safe?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Yes. Payments are processed by Xendit, a licensed payment gateway. Money is held in escrow until work is completed.
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="escrow protection">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">What is escrow?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Money is held securely by the platform. It's only released to the seller after work is completed and the customer is satisfied (usually 3 days).
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="platform fee">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">What is the platform fee?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    A small percentage (3-5%) is deducted from each completed order. Example: ₱1,000 order - ₱50 fee (5%) = ₱950 to seller.
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="refund request">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">Can I request a refund?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Yes, within 7 days of payment if service isn't delivered, work quality doesn't match, or seller is unresponsive. Requests are reviewed case-by-case.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SUPPORT -->
                <section class="faq-category">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 pb-3 border-b border-gray-200">Support & Contact</h2>
                    <div class="space-y-3">
                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="contact support">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">How do I contact support?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Email: support@serbizyu.com | Live chat: Click the icon in bottom right corner | Help Center: Click "Help" in the footer
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="support hours">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">What are your support hours?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Monday to Friday: 8:00 AM - 6:00 PM Philippine Time. Weekend: Limited support (emergency only)
                                </div>
                            </div>
                        </div>

                        <div class="faq-item bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300" data-search="report bug">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 text-left">Found a bug?</span>
                                <svg class="accordion-icon w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300">
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-gray-700 leading-relaxed">
                                    Please report it! Email us with: what you were trying to do, what happened instead, screenshots if possible, and your browser/device type.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <style>
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
        const categorySections = document.querySelectorAll('.faq-category');

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
                const searchData = item.getAttribute('data-search');
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
    </script>
</x-app-layout>
