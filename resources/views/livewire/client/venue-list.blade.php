<div>
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Discover Amazing <span class="text-yellow-400">Venues</span>
                </h1>
                <p class="text-xl text-blue-100 mb-6">Find the perfect location for your special events</p>
                
                <!-- Location Detection -->
                <div class="mb-6">
                    <button onclick="detectLocation()" class="bg-yellow-500 hover:bg-yellow-600 text-blue-900 font-semibold px-6 py-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Use My Location
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white shadow-sm sticky top-0 z-40">
        <div class="container mx-auto px-4 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search venues..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                </div>
                
                <!-- Category Filter -->
                <div>
                    <select wire:model.live="categoryFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range -->
                <div>
                    <select wire:model.live="priceRange" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Any Price</option>
                        <option value="under_100k">Under TSh 100K</option>
                        <option value="100k_500k">TSh 100K - 500K</option>
                        <option value="over_500k">Over TSh 500K</option>
                    </select>
                </div>

                <!-- Sort Options -->
                <div>
                    <select wire:model.live="sortBy" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="latest">Latest Added</option>
                        <option value="proximity">Nearest First</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="popular">Most Popular</option>
                    </select>
                </div>
            </div>
            
            <!-- Results Count -->
            <div class="mt-4 flex justify-between items-center">
                <span class="text-gray-600">{{ $venues->total() }} venues found</span>
                
                <button 
                    wire:click="$set('search', ''); $set('categoryFilter', ''); $set('priceRange', '')"
                    class="text-blue-600 hover:text-blue-800 text-sm"
                >
                    Clear all filters
                </button>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-900 mr-3"></div>
                <span class="text-gray-700">Loading venues...</span>
            </div>
        </div>
    </div>

    <!-- Venues Grid -->
    <div class="container mx-auto px-4 py-8">
        @if($venues->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($venues as $venue)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                        <!-- Image Container -->
                        <div class="relative aspect-[4/3] overflow-hidden">
                            @php
                                $gallery = is_string($venue->gallery) ? json_decode($venue->gallery, true) : $venue->gallery;
                                $firstImage = is_array($gallery) && count($gallery) > 0 ? $gallery[0] : null;
                            @endphp
                            
                            @if($firstImage)
                                <img 
                                    src="{{ asset('storage/' . $firstImage) }}" 
                                    alt="{{ $venue->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                    <i class="fas fa-building text-4xl text-blue-400"></i>
                                </div>
                            @endif

                            <!-- Overlay Actions -->
                            <div class="absolute top-3 right-3 flex gap-2">
                                @auth
                                    <livewire:client.like-button 
                                        :model="$venue" 
                                        :key="'venue-like-' . $venue->id" 
                                    />
                                    
                                    <livewire:client.bookmark-button 
                                        :model="$venue" 
                                        :key="'venue-bookmark-' . $venue->id" 
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
                                <span class="bg-yellow-500 text-blue-900 px-3 py-1 rounded-full text-sm font-semibold shadow-sm">
                                    TSh {{ number_format($venue->base_price) }}
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-1">{{ $venue->name }}</h3>
                            
                            <div class="flex items-center text-gray-600 text-sm mb-3">
                                <i class="fas fa-map-marker-alt mr-1 text-yellow-500"></i>
                                <span class="line-clamp-1">{{ $venue->location_name }}</span>
                            </div>

                            <!-- Category Badge -->
                            <div class="flex items-center space-x-2 mb-3">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                    {{ $venue->category->name ?? 'Uncategorized' }}
                                </span>
                                
                                @if($venue->user)
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                                        {{ $venue->user->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- Description -->
                            @if($venue->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $venue->description }}</p>
                            @endif

                            <!-- Stats -->
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                <div class="flex items-center gap-3">
                                    <span><i class="fas fa-heart mr-1 text-red-400"></i>{{ $venue->likes_count ?? 0 }}</span>
                                    <span><i class="fas fa-star mr-1 text-yellow-400"></i>{{ $venue->reviews_avg_rating ?? 0 }}</span>
                                </div>
                                @if(isset($venue->distance))
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>{{ number_format($venue->distance, 1) }}km</span>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('view.venue.details', $venue->id) }}"
                               class="block w-full bg-blue-900 hover:bg-blue-800 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 text-center">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $venues->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-3xl text-blue-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No venues found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your search or filters to find what you're looking for.</p>
                <button 
                    wire:click="$set('search', ''); $set('categoryFilter', ''); $set('priceRange', '');"
                    class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200"
                >
                    Clear All Filters
                </button>
            </div>
        @endif
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

function showLoginPrompt() {
    if (confirm('Please login to like and bookmark venues. Would you like to login now?')) {
        window.location.href = '/login';
    }
}

function showToast(message, type = 'info') {
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

.aspect-[4/3] {
    aspect-ratio: 4 / 3;
}
</style>
</div>
</div>