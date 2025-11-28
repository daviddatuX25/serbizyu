<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About Serbizyu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-12">

            <!-- Our Story -->
            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Story</h2>
                    <div class="text-gray-700 space-y-4 leading-relaxed">
                        <p>Serbizyu was born from a simple observation: finding reliable local services in the Philippines is harder than it should be. Whether you need someone to fix your laptop, tutor your child, or clean your home, the process often involves endless phone calls, uncertain pricing, and questions about trustworthiness.</p>
                        <p>We built Serbizyu to change that.</p>
                        <p>Our platform connects customers with skilled service providers in a transparent, secure, and efficient way. With features like structured workflows, escrow payments, and real-time updates, we're making local services work the way they should—simple, safe, and satisfying for everyone.</p>
                    </div>
                </div>
            </section>

            <!-- Our Mission -->
            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Mission</h2>
                    <div class="text-gray-700 space-y-4 leading-relaxed">
                        <p class="text-lg font-semibold text-indigo-600">To empower local service providers and make quality services accessible to everyone.</p>
                        <p>We believe talented people are everywhere, but opportunities aren't. Serbizyu creates a level playing field where anyone with skills can build a business, and anyone who needs help can find it easily.</p>
                    </div>
                </div>
            </section>

            <!-- What Makes Us Different -->
            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">What Makes Us Different</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">🎯 Workflow System</h3>
                            <p class="text-gray-700">Unlike other platforms, we don't just connect people—we guide them through the entire work process. Structured workflows ensure clear expectations, progress tracking, and quality results.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">🔒 Secure Payments</h3>
                            <p class="text-gray-700">Money is held safely in escrow until work is completed. Both buyers and sellers are protected, reducing risk and building trust.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">⚡ Real-Time Everything</h3>
                            <p class="text-gray-700">See updates the moment they happen. New messages, order changes, workflow progress—all instant, no refresh needed.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">📱 Mobile-First Design</h3>
                            <p class="text-gray-700">Built for the way Filipinos actually use the internet—on their phones. Fast, responsive, and data-efficient.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">💬 Built-In Communication</h3>
                            <p class="text-gray-700">No need for separate chat apps. Everything you need to discuss is right there in the order page.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">⭐ Transparent Reviews</h3>
                            <p class="text-gray-700">Honest feedback from real users helps everyone make better decisions. Reviews are permanent and publicly visible.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Our Values -->
            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Values</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Trust</h3>
                            <p class="text-gray-700">We're building a community, not just a marketplace. Every feature is designed to create trust between strangers.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Empowerment</h3>
                            <p class="text-gray-700">Whether you're earning or hiring, Serbizyu gives you the tools to succeed. No gatekeepers, no barriers.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Quality</h3>
                            <p class="text-gray-700">Good enough isn't good enough. We push for excellence in every service, every interaction, every line of code.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Transparency</h3>
                            <p class="text-gray-700">No hidden fees. No surprise charges. No unclear terms. What you see is what you get.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 md:col-span-2">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Local First</h3>
                            <p class="text-gray-700">We understand Filipino culture, payment methods, and needs because we're built by Filipinos, for Filipinos.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Current Features -->
            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Current Features</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">User Accounts</p>
                                <p class="text-sm text-gray-700">Register as buyer, seller, or both</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Service Listings</p>
                                <p class="text-sm text-gray-700">Create and browse service offerings</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Open Offers</p>
                                <p class="text-sm text-gray-700">Post what you need, get bids from providers</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Workflow System</p>
                                <p class="text-sm text-gray-700">Step-by-step work execution with tracking</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Activity Threads</p>
                                <p class="text-sm text-gray-700">Post updates, photos, and progress reports</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Smart Prompts</p>
                                <p class="text-sm text-gray-700">Ask and answer questions during work</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Secure Payments</p>
                                <p class="text-sm text-gray-700">Online (Xendit) and cash options</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Escrow Protection</p>
                                <p class="text-sm text-gray-700">Money held until work is complete</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Real-Time Messaging</p>
                                <p class="text-sm text-gray-700">Instant communication between users</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Review System</p>
                                <p class="text-sm text-gray-700">Rate services and users</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="font-semibold text-gray-900">Mobile Responsive</p>
                                <p class="text-sm text-gray-700">Works perfectly on any device</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Coming Soon -->
            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Coming Soon</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                            <span class="text-2xl">🚀</span>
                            <div>
                                <p class="font-semibold text-gray-900">Quick Deals</p>
                                <p class="text-sm text-gray-700">QR codes for instant service booking</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                            <span class="text-2xl">🚀</span>
                            <div>
                                <p class="font-semibold text-gray-900">Enhanced Reporting</p>
                                <p class="text-sm text-gray-700">Better tools to flag inappropriate content</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                            <span class="text-2xl">🚀</span>
                            <div>
                                <p class="font-semibold text-gray-900">Video Support</p>
                                <p class="text-sm text-gray-700">Upload video evidence and consultations</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                            <span class="text-2xl">🚀</span>
                            <div>
                                <p class="font-semibold text-gray-900">Advanced Search</p>
                                <p class="text-sm text-gray-700">More filters and smarter results</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                            <span class="text-2xl">🚀</span>
                            <div>
                                <p class="font-semibold text-gray-900">Analytics Dashboard</p>
                                <p class="text-sm text-gray-700">Track your earnings and performance</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                            <span class="text-2xl">🚀</span>
                            <div>
                                <p class="font-semibold text-gray-900">Mobile App</p>
                                <p class="text-sm text-gray-700">Native iOS and Android applications</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Technology -->
            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">The Technology</h2>
                    <div class="text-gray-700 space-y-4 leading-relaxed mb-6">
                        <p>Serbizyu is built with modern, reliable technology:</p>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <p class="font-semibold text-gray-900">Backend: Laravel 11 (PHP framework)</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <p class="font-semibold text-gray-900">Frontend: Alpine.js + Tailwind CSS</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <p class="font-semibold text-gray-900">Database: MySQL</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <p class="font-semibold text-gray-900">Real-Time: Soketi (WebSocket server)</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <p class="font-semibold text-gray-900">Payments: Xendit integration</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <p class="font-semibold text-gray-900">Hosting: Hostinger (production-ready)</p>
                        </div>
                    </div>
                    <div class="text-gray-700 space-y-4 leading-relaxed mt-6">
                        <p>We chose these technologies for speed, security, and scalability. As we grow, the platform grows with us.</p>
                    </div>
                </div>
            </section>

            <!-- The Team -->
            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">The Team</h2>
                    <p class="text-gray-700 mb-6">Serbizyu was created by a passionate team of developers and designers:</p>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">David Datu Sarmiento</h3>
                            <p class="text-indigo-600 font-semibold mb-2">Lead Developer & Architect</p>
                            <p class="text-gray-700 text-sm">Designed the system architecture and backend infrastructure. Passionate about clean code and scalable solutions.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Joross Manzano</h3>
                            <p class="text-indigo-600 font-semibold mb-2">Full-Stack Developer</p>
                            <p class="text-gray-700 text-sm">Built core features including the workflow system and real-time communications. Loves solving complex problems.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Charlene Hipolito</h3>
                            <p class="text-indigo-600 font-semibold mb-2">Frontend Developer & UI/UX Designer</p>
                            <p class="text-gray-700 text-sm">Crafted the user interface and experience. Obsessed with making things beautiful and intuitive.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Rayver Leal</h3>
                            <p class="text-indigo-600 font-semibold mb-2">Backend Developer</p>
                            <p class="text-gray-700 text-sm">Implemented payment systems and security features. Ensures every transaction is safe and reliable.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="bg-indigo-50 border border-indigo-200 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to Get Started?</h2>
                    <p class="text-gray-700 mb-6 max-w-2xl mx-auto">Join thousands of service providers and customers on Serbizyu today</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('auth.signin') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200">
                            Get Started
                        </a>
                        <a href="{{ route('browse') }}" class="px-6 py-3 bg-white hover:bg-gray-50 text-indigo-600 font-semibold rounded-lg border border-indigo-200 transition-all duration-200">
                            Browse Services
                        </a>
                    </div>
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
