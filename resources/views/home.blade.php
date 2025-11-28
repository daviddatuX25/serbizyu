<x-app-layout header="Welcome to Serbizyu" :jsFiles="['app.js', 'home.js']">
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
                <form method="GET" action="{{ route('browse') }}" class="flex justify-center mb-6">
                <input type="text" name="search" placeholder="Search for any service..."
                    class="hero-search" value="{{ request('search', '') }}">
                <button type="submit" class="rounded-l-none rounded-r-lg">
                    <x-icons.search />
                </button>
                </form>

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
                <form method="GET" action="{{ route('browse') }}" class="flex justify-center mb-6">
                <input type="text" name="search" placeholder="Search open offers..."
                    class="hero-search" value="{{ request('search', '') }}">
                <button type="submit" class="rounded-l-none rounded-r-lg">
                    <x-icons.search />
                </button>
                </form>

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
        </div>

        <!-- Card Grid -->
        <div class="browse-grid">
        @forelse($browseItems as $item)
            @if($item instanceof App\Domains\Listings\Models\Service)
                <!-- Service Card -->
                <article class="listing-card">
                    <div class="card-top">
                    <span class="badge-service">Service</span>
                    <span class="rating">★★★★★</span>
                    </div>
                    <h3 class="card-title">{{ $item->title }}</h3>
                    <p class="card-desc">{{ Str::limit($item->description, 60) }}</p>
                    <p class="card-meta">Rate: ₱{{ $item->price }}/hr</p>
                    @if($item->address)
                        <p class="card-meta">Location: {{ $item->address->city ?? 'N/A' }}</p>
                    @endif
                    <div class="card-footer">
                    <span class="text-xs text-text-secondary">{{ $item->creator->name ?? 'Servicer' }}</span>
                    <div class="card-avatar">
                        @if($item->creator->media->first())
                            <img src="{{ asset('storage/' . $item->creator->media->first()->disk_path) }}" alt="Avatar" class="w-8 h-8 rounded-full" />
                        @else
                            👤
                        @endif
                    </div>
                    </div>
                </article>
            @else
                <!-- Open Offer Card -->
                <article class="listing-card">
                    <div class="card-top">
                    <span class="badge-offer">Open Offer</span>
                    <span class="text-sm text-text-secondary">Budget: ₱{{ $item->budget ?? 'TBD' }}</span>
                    </div>
                    <h3 class="card-title">{{ $item->title }}</h3>
                    <p class="card-desc">{{ Str::limit($item->description, 60) }}</p>
                    @if($item->address)
                        <p class="card-meta">{{ $item->address->city ?? 'N/A' }}</p>
                    @endif
                    <div class="card-footer">
                    <span class="text-xs text-text-secondary">{{ $item->created_at->diffForHumans() }}</span>
                    <div class="card-avatar">📝</div>
                    </div>
                </article>
            @endif
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No services or offers available yet. Check back soon!</p>
            </div>
        @endforelse
        </div>
    </div>

    <div class="w-full text-center mt-6">
        <a href="{{ route('browse') }}" class="browse-viewmore">View More</a>
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
            <textarea id="serviceDesc" placeholder="Describe the service you want to offer..." rows="4"></textarea>
            <button onclick="handleCreateService()">Continue</button>
        </div>

        <!-- Create Open Offer -->
        <div class="create-card">
            <h3>Create an Open Offer</h3>
            <textarea id="offerDesc" placeholder="Describe what you are looking for..." rows="4"></textarea>
            <button onclick="handleCreateOffer()">Continue</button>
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
        @forelse($workflows as $workflow)
            <!-- Workflow Card -->
            <div class="workflow-card">
                <h3>{{ $workflow->name }}</h3>
                <p>
                    <strong>Steps:</strong> 
                    @if($workflow->workTemplates->count() > 0)
                        {{ $workflow->workTemplates->pluck('name')->join(' → ') }}
                    @else
                        No steps defined
                    @endif
                </p>
                <div class="workflow-actions">
                <a href="{{ route('workflows.create-service', $workflow->id) }}" class="btn-service">Create Service</a>
                <a href="{{ route('workflows.create-offer', $workflow->id) }}" class="btn-offer">Create Offer</a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">No workflow templates available.</p>
            </div>
        @endforelse
        </div>
    </div>
    </section>

    <script>
        function handleCreateService() {
            const description = document.getElementById('serviceDesc').value;
            if (description.trim()) {
                const url = new URL('{{ route("creator.services.create") }}', window.location.origin);
                url.searchParams.append('description', description);
                window.location.href = url.toString();
            } else {
                alert('Please enter a service description');
            }
        }

        function handleCreateOffer() {
            const description = document.getElementById('offerDesc').value;
            if (description.trim()) {
                const url = new URL('{{ route("creator.openoffers.create") }}', window.location.origin);
                url.searchParams.append('description', description);
                window.location.href = url.toString();
            } else {
                alert('Please enter an offer description');
            }
        }
    </script>

</x-app-layout>
