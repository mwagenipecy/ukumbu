<div>
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section with Video -->
    <div class="relative h-[70vh] overflow-hidden">
        <!-- Video Background -->
        <div class="absolute inset-0 z-0">
            <video 
                class="w-full h-full object-cover" 
                autoplay 
                muted 
                loop 
                playsinline
            >
                <source src="{{ asset('video/invideo-ai-1080 Unlock the Perfect Venue for Your Next E 2025-09-01 (1) (1) (1).mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40 z-10"></div>
        
        <!-- Blue Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#1F398A] via-blue-800/50 to-transparent z-15"></div>
        
        <!-- Content -->
        <div class="relative z-20 h-full flex flex-col justify-center">
            <!-- Navigation -->
            <div class="absolute top-0 left-0 right-0 z-30">
                <div class="container mx-auto px-4 py-4">
                    <div class="flex justify-between items-center">
                        <!-- <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-yellow-400 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-[#1F398A] text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">VenueLink</h2>
                                <p class="text-blue-200 text-xs">Event Solutions Platform</p>
                            </div>
                        </div> -->
                        
                       
                    </div>
                </div>
            </div>
            
            <!-- Hero Content -->
            <div class="container mx-auto px-4 text-center">
                <div class="inline-block bg-yellow-400 text-[#1F398A] px-3 py-1 rounded-full text-xs font-semibold mb-4">
                    <i class="fas fa-star mr-1"></i>
                    Tanzania's Premier Event Platform
                </div>
                
                <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight text-white">
                    Your Perfect Event
                    <span class="text-yellow-400 block">Starts Here</span>
                </h1>
                
                <p class="text-lg md:text-xl text-blue-100 mb-6 max-w-2xl mx-auto leading-relaxed">
                    Discover, book, and manage exceptional venues and services across Tanzania
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center items-center mb-8">
                    <button onclick="scrollToSearch()" class="bg-yellow-400 hover:bg-yellow-500 text-[#1F398A] font-bold px-6 py-3 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-search mr-2"></i>
                        Find Your Perfect Venue
                    </button>
                    <button onclick="scrollToServices()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 backdrop-blur-sm border border-white border-opacity-30">
                        <i class="fas fa-concierge-bell mr-2"></i>
                        Explore Services
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="max-w-4xl mx-auto" id="search-section">
                    <div class="bg-white rounded-xl shadow-2xl p-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Search Input -->
                            <div class="flex-1">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="searchQuery"
                                    placeholder="Search venues or services..."
                                    class="w-full px-6 py-4 border-0 rounded-lg text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none text-lg"
                                >
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="md:w-48">
                                <select wire:model.live="selectedCategory" class="w-full px-4 py-4 border-0 rounded-lg text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="md:w-48">
                                <select wire:model.live="priceRange" class="w-full px-4 py-4 border-0 rounded-lg text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="">Any Price</option>
                                    @if($viewType === 'venues')
                                        <option value="under_100k">Under TSh 100K</option>
                                        <option value="100k_500k">TSh 100K - 500K</option>
                                        <option value="over_500k">Over TSh 500K</option>
                                    @else
                                        <option value="under_50k">Under TSh 50K</option>
                                        <option value="50k_200k">TSh 50K - 200K</option>
                                        <option value="over_200k">Over TSh 200K</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Navigation Tabs -->
    <div class="bg-white shadow-sm sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- View Toggle -->
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button 
                        wire:click="switchView('venues')"
                        class="px-6 py-2 rounded-md font-medium transition-all duration-200 {{ $viewType === 'venues' ? 'bg-[#1F398A] text-white shadow' : 'text-gray-600 hover:text-[#1F398A]' }}"
                    >
                        <i class="fas fa-building mr-2"></i>
                        Venues
                    </button>
                    <button 
                        wire:click="switchView('services')"
                        class="px-6 py-2 rounded-md font-medium transition-all duration-200 {{ $viewType === 'services' ? 'bg-[#1F398A] text-white shadow' : 'text-gray-600 hover:text-[#1F398A]' }}"
                    >
                        <i class="fas fa-concierge-bell mr-2"></i>
                        Services
                    </button>
                </div>
                
                <!-- Type Explanation -->
                <div class="hidden md:block text-sm text-gray-600">
                    @if($viewType === 'venues')
                        <span class="flex items-center">
                            <i class="fas fa-building mr-1 text-[#1F398A]"></i>
                            <strong>Venues:</strong> Physical locations for your events
                        </span>
                    @else
                        <span class="flex items-center">
                            <i class="fas fa-concierge-bell mr-1 text-green-600"></i>
                            <strong>Services:</strong> Professional assistance for your events
                        </span>
                    @endif
                </div>

                <!-- Sort Options -->
                <div class="flex items-center gap-4">
                    <select wire:model.live="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="proximity">Nearest First</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="popular">Most Popular</option>
                        <option value="latest">Latest Added</option>
                    </select>
                    
                    <span class="text-sm text-gray-500">
                        {{ $items->total() }} {{ $viewType }} found
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-900 mr-3"></div>
                <span class="text-gray-700">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        @if($items->count() > 0)
            <!-- Instagram-style Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($items as $item)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                        <!-- Image Container -->
                        <div class="relative aspect-square overflow-hidden">
                            @php
                                $gallery = is_string($item->gallery) ? json_decode($item->gallery, true) : $item->gallery;
                                $firstImage = is_array($gallery) && count($gallery) > 0 ? $gallery[0] : null;
                            @endphp
                            
                            @if($firstImage)
                                <img 
                                    src="{{ asset('storage/' . $firstImage) }}" 
                                    alt="{{ $item->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                    <i class="fas fa-{{ $viewType === 'venues' ? 'building' : 'concierge-bell' }} text-4xl text-blue-400"></i>
                                </div>
                            @endif

                            <!-- Overlay Actions -->
                            <div class="absolute top-3 right-3 flex gap-2">
                                <!-- Like Button -->
                                @auth
                                    <livewire:client.like-button 
                                        :model="$item" 
                                        :key="$viewType . '-like-' . $item->id" 
                                    />
                                    
                                    <!-- Bookmark Button -->
                                    <livewire:client.bookmark-button 
                                        :model="$item" 
                                        :key="$viewType . '-bookmark-' . $item->id" 
                                    />
                                @else
                                    <button onclick="showLoginPrompt()" class="bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-600 p-2 rounded-full shadow-sm transition-all duration-200">
                                        <i class="fas fa-heart text-sm"></i>
                                    </button>
                                    <button onclick="showLoginPrompt()" class="bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-600 p-2 rounded-full shadow-sm transition-all duration-200">
                                        <i class="fas fa-bookmark text-sm"></i>
                                    </button>
                                @endauth
                            </div>

                            <!-- Price Badge -->
                            <div class="absolute bottom-3 left-3">
                                <span class="bg-yellow-500 text-[#1F398A] px-3 py-1 rounded-full text-sm font-semibold shadow-sm">
                                    TSh {{ number_format($viewType === 'venues' ? $item->base_price : $item->price) }}
                                    @if($viewType === 'services' && $item->pricing_model !== 'flat')
                                        <span class="text-xs">/ {{ $item->pricing_model === 'hourly' ? 'hr' : 'guest' }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-1">{{ $item->name }}</h3>
                            
                            <div class="flex items-center text-gray-600 text-sm mb-3">
                                <i class="fas fa-map-marker-alt mr-1 text-yellow-500"></i>
                                <span class="line-clamp-1">{{ $item->location_name }}</span>
                            </div>

                            <!-- Type and Category Badges -->
                            <div class="flex items-center space-x-2 mb-3">
                                <!-- Type Badge -->
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">
                                    <i class="fas fa-{{ $viewType === 'venues' ? 'building' : 'concierge-bell' }} mr-1"></i>
                                    {{ ucfirst($viewType === 'venues' ? 'Venue' : 'Service') }}
                                </span>
                                
                                <!-- Category Badge -->
                                @if($item->category)
                                    <span class="bg-[#1F398A]/10 text-[#1F398A] text-xs px-2 py-1 rounded-full">
                                        {{ $item->category->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- Description -->
                            @if($item->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $item->description }}</p>
                            @endif

                            <!-- Stats -->
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                <div class="flex items-center gap-3">
                                    <span><i class="fas fa-heart mr-1 text-red-400"></i>{{ $item->likes_count ?? 0 }}</span>
                                    <span><i class="fas fa-star mr-1 text-yellow-400"></i>{{ $item->reviews_avg_rating ?? 0 }}</span>
                                </div>
                                @if(isset($item->distance))
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>{{ number_format($item->distance, 1) }}km</span>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <a href="{{ $viewType === 'venues' ? route('view.venue.details', $item->id) : route('view.service.details', $item->id) }}"
                               class="block w-full text-center bg-[#1F398A] hover:bg-[#1F398A]/80 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $items->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-[#1F398A]/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-3xl text-blue-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No {{ $viewType }} found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your search or filters to find what you're looking for.</p>
                <button 
                    wire:click="$set('searchQuery', '')"
                    onclick="@this.set('selectedCategory', ''); @this.set('priceRange', '');"
                    class="bg-[#1F398A] hover:bg-[#1F398A]/80 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200"
                >
                    Clear All Filters
                </button>
            </div>
        @endif
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Why Choose <span class="text-[#1F398A]">VenueLink</span>?
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    We provide comprehensive event solutions with cutting-edge technology and exceptional service
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center p-8 rounded-xl hover:shadow-lg transition-all duration-300 group">
                    <div class="w-20 h-20 bg-[#1F398A]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#1F398A]/20 transition-colors duration-300">
                        <i class="fas fa-search text-3xl text-[#1F398A]"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Smart Discovery</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Advanced search algorithms help you find the perfect venue and services based on your specific needs, location, and budget.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="text-center p-8 rounded-xl hover:shadow-lg transition-all duration-300 group">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-green-200 transition-colors duration-300">
                        <i class="fas fa-shield-alt text-3xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Secure Booking</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Safe and secure payment processing with comprehensive booking management and real-time confirmations.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="text-center p-8 rounded-xl hover:shadow-lg transition-all duration-300 group">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-purple-200 transition-colors duration-300">
                        <i class="fas fa-headset text-3xl text-purple-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">24/7 Support</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Round-the-clock customer support to ensure your event planning experience is smooth and stress-free.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="text-center p-8 rounded-xl hover:shadow-lg transition-all duration-300 group">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-yellow-200 transition-colors duration-300">
                        <i class="fas fa-map-marker-alt text-3xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Location Intelligence</h3>
                    <p class="text-gray-600 leading-relaxed">
                        GPS-powered location services help you find venues and services near you with accurate distance calculations.
                    </p>
                </div>
                
                <!-- Feature 5 -->
                <div class="text-center p-8 rounded-xl hover:shadow-lg transition-all duration-300 group">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-red-200 transition-colors duration-300">
                        <i class="fas fa-star text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Quality Assured</h3>
                    <p class="text-gray-600 leading-relaxed">
                        All venues and services are verified and rated by real customers to ensure the highest quality standards.
                    </p>
                </div>
                
                <!-- Feature 6 -->
                <div class="text-center p-8 rounded-xl hover:shadow-lg transition-all duration-300 group">
                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-indigo-200 transition-colors duration-300">
                        <i class="fas fa-mobile-alt text-3xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Mobile First</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Fully responsive design optimized for all devices, ensuring seamless experience on mobile, tablet, and desktop.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        About <span class="text-[#1F398A]">VenueLink</span>
                    </h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        Founded with a vision to revolutionize event planning in Tanzania, VenueLink connects event organizers with the finest venues and professional services across the country.
                    </p>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Our platform leverages cutting-edge technology to provide seamless booking experiences, comprehensive event management tools, and exceptional customer support. We're committed to making every event memorable and stress-free.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                            <div class="text-2xl font-bold text-[#1F398A] mb-2">5+</div>
                            <div class="text-gray-600">Years Experience</div>
                        </div>
                        <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                            <div class="text-2xl font-bold text-[#1F398A] mb-2">50+</div>
                            <div class="text-gray-600">Cities Covered</div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="/register" class="bg-[#1F398A] hover:bg-[#1F398A]/90 text-white px-8 py-4 rounded-lg font-semibold transition-colors duration-200 text-center">
                            Join Our Community
                        </a>
                        <a href="#contact" class="bg-white hover:bg-gray-50 text-[#1F398A] px-8 py-4 rounded-lg font-semibold transition-colors duration-200 text-center border border-[#1F398A]">
                            Learn More
                        </a>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="bg-gradient-to-br from-[#1F398A] to-[#1F398A]/80 rounded-2xl p-8 text-white">
                        <h3 class="text-2xl font-bold mb-6">Our Mission</h3>
                        <p class="text-blue-100 mb-6 leading-relaxed">
                            To democratize access to premium event venues and services, making exceptional events accessible to everyone across Tanzania.
                        </p>
                        <h3 class="text-2xl font-bold mb-6">Our Vision</h3>
                        <p class="text-blue-100 leading-relaxed">
                            To become Tanzania's leading event solutions platform, setting the standard for innovation, quality, and customer satisfaction in the events industry.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    What Our <span class="text-[#1F398A]">Customers</span> Say
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Don't just take our word for it. Here's what our satisfied customers have to say about their experience with VenueLink.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-[#1F398A]/10 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-[#1F398A]"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Sarah Mwalimu</h4>
                            <p class="text-gray-600 text-sm">Wedding Planner</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        "VenueLink made finding the perfect wedding venue so easy! The platform is intuitive and the customer support is exceptional. Highly recommended!"
                    </p>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">John Kimaro</h4>
                            <p class="text-gray-600 text-sm">Corporate Events Manager</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        "The booking process is seamless and the venue quality is outstanding. VenueLink has become our go-to platform for all corporate events."
                    </p>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Amina Hassan</h4>
                            <p class="text-gray-600 text-sm">Birthday Party Organizer</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        "I found the perfect venue for my daughter's birthday party within minutes. The location-based search feature is amazing!"
                    </p>
                </div>
            </div>
        </div>
    </div>

 

   
</div>

<script>
function detectLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                @this.call('setUserLocation', position.coords.latitude, position.coords.longitude);
                showToast('Location detected successfully!', 'success');
            },
            function(error) {
                console.error('Geolocation error:', error);
                showToast('Unable to detect location. Please enable location services.', 'error');
            }
        );
    } else {
        showToast('Geolocation is not supported by this browser.', 'error');
    }
}

function scrollToSearch() {
    document.getElementById('search-section').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}

function scrollToServices() {
    @this.call('switchView', 'services');
    setTimeout(() => {
        document.getElementById('search-section').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }, 100);
}

function viewDetails(type, id) {
    // Redirect to details page
    window.location.href = `/${type}/${id}`;
}

function showLoginPrompt() {
    if (confirm('Please login to like and bookmark items. Would you like to login now?')) {
        window.location.href = '/login';
    }
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 max-w-sm rounded-lg shadow-lg p-4 transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.aspect-square {
    aspect-ratio: 1 / 1;
}
</style>


</div>
