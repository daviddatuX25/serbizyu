<x-app-layout header="Welcome to Serbizyu" :cssFiles='["home.css", "browse.css"]' jsFile="home.js">
    <!-- Single Hero Section Container with Swiper -->
    <section class="hero-section relative">
    <!-- Swiper Container -->
    <div class="swiper heroSwiper h-[80vh]">
        <div class="swiper-wrapper">
        
        <!-- Slide 1: Services Hero -->
        <div class="swiper-slide">
            <div class="relative h-[80vh] bg-cover bg-center flex items-center justify-center"
            style="background-image: url('https://images.unsplash.com/photo-1614289941451-60f7ce249549?auto=format&fit=crop&w=1350&q=80');">
            <div class="absolute inset-0 bg-black bg-opacity-60"></div>
            <div class="relative z-10 text-center max-w-2xl px-4 text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-5">
                Kami na ang bahala sa inyong mga pangangailangan.
                </h1>
                
                <!-- Search Bar -->
                <div class="flex justify-center mb-6">
                <input type="text" placeholder="Search for any service..."
                    class="hero-search">
                <x-primary-button class="rounded-l-none rounded-r-lg">
                    <x-icons.search />
                </x-primary-button>
                </div>

                <!-- Categories -->
                <div class="flex flex-wrap justify-center gap-3  text-sm ">
                <x-outline-button>Event Decorating Service</x-outline-button>
                <x-outline-button>Catering Service</x-outline-button>
                <x-outline-button>Home Repair & Maintenance</x-outline-button>
                <x-outline-button>Construction Service</x-outline-button>
                </div>
            </div>
            </div>
        </div>
        
        <!-- Slide 2: Open Offers Hero -->
        <div class="swiper-slide">
            <div class="relative h-[80vh] bg-cover bg-center flex items-center justify-center"
            style="background-image: url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1350&q=80');">
            <div class="absolute inset-0 bg-black bg-opacity-60"></div>
            <div class="relative z-10 text-center max-w-2xl px-4 text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-5">
                Tuklasin ang mga bukas na alok at simulan nang kumita.
                </h1>

                <!-- Search Bar -->
                <div class="flex justify-center mb-6">
                <input type="text" placeholder="Search open offers..."
                    class="hero-search">
                <x-primary-button class="rounded-l-none rounded-r-lg">
                    <x-icons.search />
                </x-primary-button>
                </div>

                <!-- Categories -->
                <div class="flex flex-wrap justify-center gap-3">
                <x-outline-button>Looking for Catering</x-outline-button>
                <x-outline-button>Need Home Repair</x-outline-button>
                <x-outline-button>Event Decoration Needed</x-outline-button>
                <x-outline-button>Small Construction Project</x-outline-button>
                </div>
            </div>
            </div>
        </div>
        </div>
        
        <!-- Swiper Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        
        <!-- Swiper Pagination -->
        <div class="swiper-pagination"></div>
    </div>
    </section>


    <!-- Explore Categories -->
    <section class="section-categories">
    <h2 class="section-categories-title">Explore categories</h2>

    <!-- Horizontal scroll -->
    <div class="categories-scroll">
        
        <div class="category-card">
        <p class="category-icon">💻</p>
        <p class="category-label">Programming & Tech</p>
        </div>

        <div class="category-card">
        <p class="category-icon">🎨</p>
        <p class="category-label">Graphics & Design</p>
        </div>

        <div class="category-card">
        <p class="category-icon">📈</p>
        <p class="category-label">Digital Marketing</p>
        </div>

        <div class="category-card">
        <p class="category-icon">📝</p>
        <p class="category-label">Writing & Translation</p>
        </div>

        <div class="category-card">
        <p class="category-icon">🎬</p>
        <p class="category-label">Video & Animation</p>
        </div>

        <div class="category-card">
        <p class="category-icon">🤖</p>
        <p class="category-label">AI Services</p>
        </div>

        <div class="category-card">
        <p class="category-icon">🎵</p>
        <p class="category-label">Music & Audio</p>
        </div>

        <div class="category-card">
        <p class="category-icon">💼</p>
        <p class="category-label">Business</p>
        </div>

        <div class="category-card">
        <p class="category-icon">📊</p>
        <p class="category-label">Consulting</p>
        </div>
        
    </div>
    </section>

    <!-- Browse Section -->
    <section class="py-12 px-6 bg-gray-50">
    <div class="browse-inner">
        <div class="browse-header">
        <h2 class="text-3xl font-bold">Browse Offers & Services</h2>

        <!-- Filters -->
        <div class="browse-filters">
            <button class="filter-btn filter-btn-primary">All</button>
            <button class="filter-btn filter-btn-outline">Services</button>
            <button class="filter-btn filter-btn-outline">Open Offers</button>

            <select class="filter-select">
            <option>All Categories</option>
            <option>Catering</option>
            <option>Construction</option>
            <option>Repair</option>
            </select>

            <select class="filter-select">
            <option>All Locations</option>
            <option>Tagudin</option>
            <option>Sta. Cruz</option>
            <option>Candon</option>
            </select>
        </div>
        </div>

        <!-- Card Grid -->
        <div class="browse-grid">

        <!-- Service Card -->
        <article class="browse-card">
            <div class="card-top">
            <span class="badge-service">Service</span>
            <span class="rating">★★★★★</span>
            </div>
            <h3 class="card-title">Arnel’s Plumbing</h3>
            <p class="card-desc">Diagnose &gt; Buy materials &gt; On field &gt; Finish</p>
            <p class="card-meta">Rate: ₱200/hr</p>
            <p class="card-meta">Location: Tagudin, Ilocos Sur</p>
            <div class="card-footer">
            <span class="text-xs text-text-secondary">Verified Servicer</span>
            <div class="card-avatar">👤</div>
            </div>
        </article>

        <!-- Open Offer Card -->
        <article class="browse-card">
            <div class="card-top">
            <span class="badge-offer">Open Offer</span>
            <span class="text-sm text-text-secondary">Budget: ₱5,000</span>
            </div>
            <h3 class="card-title">Looking for Catering Service</h3>
            <p class="card-desc">Event for 50 guests</p>
            <p class="card-meta">Sta. Cruz, Ilocos Sur</p>
            <div class="card-footer">
            <span class="text-xs text-text-secondary">Posted 2h ago</span>
            <div class="card-avatar">📝</div>
            </div>
        </article>

        <!-- Service Card -->
        <article class="browse-card">
            <div class="card-top">
            <span class="badge-service">Service</span>
            <span class="rating">★★★★☆</span>
            </div>
            <h3 class="card-title">General Home Repairs</h3>
            <p class="card-desc">Diagnose &gt; Buy materials &gt; On field &gt; Finish</p>
            <p class="card-meta">Rate: ₱150/hr</p>
            <p class="card-meta">Candon, Ilocos Sur</p>
            <div class="card-footer">
            <span class="text-xs text-text-secondary">Trusted Local</span>
            <div class="card-avatar">👤</div>
            </div>
        </article>

        <!-- Open Offer Card -->
        <article class="browse-card">
            <div class="card-top">
            <span class="badge-offer">Open Offer</span>
            <span class="text-sm text-text-secondary">Budget: ₱12,000</span>
            </div>
            <h3 class="card-title">Need House Painting</h3>
            <p class="card-desc">2-story house, labor only</p>
            <p class="card-meta">Luna, La Union</p>
            <div class="card-footer">
            <span class="text-xs text-text-secondary">Posted 1d ago</span>
            <div class="card-avatar">📝</div>
            </div>
        </article>

        </div>
    </div>

    <div class="w-full text-center mt-6">
        <a href="/browse" class="browse-viewmore">View More</a>
    </div>
    </section>


    <!-- Create Section -->
    <section class="create-section">
    <div class="create-container">
        <h2>Start Creating</h2>
        <p>Quickly draft an open offer or service idea and continue to the full creator page.</p>

        <div class="create-grid">

        <!-- Create Service -->
        <div class="create-card">
            <h3>Create a Service</h3>
            <textarea placeholder="Describe the service you want to offer..." rows="4"></textarea>
            <button>Continue</button>
        </div>

        <!-- Create Open Offer -->
        <div class="create-card">
            <h3>Create an Open Offer</h3>
            <textarea placeholder="Describe what you are looking for..." rows="4"></textarea>
            <button>Continue</button>
        </div>

        </div>
    </div>
    </section>

    <!-- Featured Workflows -->
    <section class="workflows-section">
    <div class="workflows-container">
        <h2>Featured Workflows</h2>
        <p>Explore workflow templates that can guide you in creating a service or responding to an open offer.</p>

        <!-- Workflow Grid -->
        <div class="workflows-grid">

        <!-- Workflow Card -->
        <div class="workflow-card">
            <h3>Wedding Video Production</h3>
            <p><strong>Steps:</strong> Import Footage → Edit Clips → Color Grade → Export</p>
            <div class="workflow-actions">
            <button class="btn-service">Create Service</button>
            <button class="btn-offer">Create Offer</button>
            </div>
        </div>

        <!-- Workflow Card -->
        <div class="workflow-card">
            <h3>House Painting</h3>
            <p><strong>Steps:</strong> Inspect → Prepare Surface → Apply Paint → Finish</p>
            <div class="workflow-actions">
            <button class="btn-service">Create Service</button>
            <button class="btn-offer">Create Offer</button>
            </div>
        </div>

        <!-- Workflow Card -->
        <div class="workflow-card">
            <h3>Birthday Catering</h3>
            <p><strong>Steps:</strong> Menu Planning → Cook → Serve → Clean Up</p>
            <div class="workflow-actions">
            <button class="btn-service">Create Service</button>
            <button class="btn-offer">Create Offer</button>
            </div>
        </div>

        </div>
    </div>
    </section>

</x-app-layout>