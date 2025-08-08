<div>
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4 py-8">
            <!-- Hero Content -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">
                    Find Perfect <span class="text-yellow-400">Venues</span> & <span class="text-yellow-400">Services</span>
                </h1>
                <p class="text-xl text-blue-100 mb-8">Discover amazing event venues and services across Tanzania</p>
                
                <!-- Location Detection -->
                <div class="mb-6">
                    <button onclick="detectLocation()" class="bg-yellow-500 hover:bg-yellow-600 text-blue-900 font-semibold px-6 py-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Use My Location
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg p-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Search Input -->
                        <div class="flex-1">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="searchQuery"
                                placeholder="Search venues or services..."
                                class="w-full px-4 py-3 border-0 rounded-lg text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            >
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="md:w-48">
                            <select wire:model.live="selectedCategory" class="w-full px-4 py-3 border-0 rounded-lg text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="md:w-48">
                            <select wire:model.live="priceRange" class="w-full px-4 py-3 border-0 rounded-lg text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
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

    <!-- Navigation Tabs -->
    <div class="bg-white shadow-sm sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- View Toggle -->
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button 
                        wire:click="switchView('venues')"
                        class="px-6 py-2 rounded-md font-medium transition-all duration-200 {{ $viewType === 'venues' ? 'bg-blue-900 text-white shadow' : 'text-gray-600 hover:text-blue-900' }}"
                    >
                        <i class="fas fa-building mr-2"></i>
                        Venues
                    </button>
                    <button 
                        wire:click="switchView('services')"
                        class="px-6 py-2 rounded-md font-medium transition-all duration-200 {{ $viewType === 'services' ? 'bg-blue-900 text-white shadow' : 'text-gray-600 hover:text-blue-900' }}"
                    >
                        <i class="fas fa-concierge-bell mr-2"></i>
                        Services
                    </button>
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
                                <span class="bg-yellow-500 text-blue-900 px-3 py-1 rounded-full text-sm font-semibold shadow-sm">
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

                            @if($item->category)
                                <div class="flex items-center mb-3">
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ $item->category->name }}
                                    </span>
                                </div>
                            @endif

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
                            <a  href="{{ route('view.venue.details', $item->id ) }}"

                                class="w-full bg-blue-900 hover:bg-blue-800 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200"
                            >
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
                <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-3xl text-blue-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No {{ $viewType }} found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your search or filters to find what you're looking for.</p>
                <button 
                    wire:click="$set('searchQuery', '')"
                    onclick="@this.set('selectedCategory', ''); @this.set('priceRange', '');"
                    class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200"
                >
                    Clear All Filters
                </button>
            </div>
        @endif
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
