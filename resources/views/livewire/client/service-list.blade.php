<div>
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Find Professional <span class="text-yellow-400">Services</span>
                </h1>
                <p class="text-xl text-blue-100 mb-6">Get expert assistance for your special events</p>
                
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
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search services..."
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

                <!-- Pricing Model Filter -->
                <div>
                    <select wire:model.live="pricingModelFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">All Pricing</option>
                        <option value="flat">Fixed Price</option>
                        <option value="hourly">Hourly Rate</option>
                        <option value="per_guest">Per Guest</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div>
                    <select wire:model.live="priceRange" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Any Price</option>
                        <option value="under_50k">Under TSh 50K</option>
                        <option value="50k_200k">TSh 50K - 200K</option>
                        <option value="over_200k">Over TSh 200K</option>
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
                <span class="text-gray-600">{{ $services->total() }} services found</span>
                
                <button 
                    wire:click="$set('search', ''); $set('categoryFilter', ''); $set('priceRange', ''); $set('pricingModelFilter', '')"
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
                <span class="text-gray-700">Loading services...</span>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="container mx-auto px-4 py-8">
        @if($services->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($services as $service)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                        <!-- Image Container -->
                        <div class="relative aspect-[4/3] overflow-hidden">
                            @php
                                $gallery = is_string($service->gallery) ? json_decode($service->gallery, true) : $service->gallery;
                                $firstImage = is_array($gallery) && count($gallery) > 0 ? $gallery[0] : null;
                            @endphp
                            
                            @if($firstImage)
                                <img 
                                    src="{{ asset('storage/' . $firstImage) }}" 
                                    alt="{{ $service->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                    <i class="fas fa-concierge-bell text-4xl text-blue-400"></i>
                                </div>
                            @endif

                            <!-- Overlay Actions -->
                            <div class="absolute top-3 right-3 flex gap-2">
                                @auth
                                    <livewire:client.like-button 
                                        :model="$service" 
                                        :key="'service-like-' . $service->id" 
                                    />
                                    
                                    <livewire:client.bookmark-button 
                                        :model="$service" 
                                        :key="'service-bookmark-' . $service->id" 
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
                                    TSh {{ number_format($service->price ?? $service->price_max ?? 0) }}
                                    @if($service->pricing_model === 'hourly')
                                        /hr
                                    @elseif($service->pricing_model === 'per_guest')
                                        /guest
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-1">{{ $service->name }}</h3>
                            
                            <!-- Provider Info -->
                            @if($service->user)
                                <div class="flex items-center text-gray-600 text-sm mb-2">
                                    <i class="fas fa-user mr-1 text-blue-500"></i>
                                    <span class="font-medium">{{ $service->user->name }}</span>
                                </div>
                            @endif
                            
                            <!-- Location -->
                            <div class="flex items-center text-gray-600 text-sm mb-3">
                                <i class="fas fa-map-marker-alt mr-1 text-yellow-500"></i>
                                <span class="line-clamp-1">{{ $service->location_name }}</span>
                            </div>

                            <!-- Badges -->
                            <div class="flex items-center space-x-2 mb-3 flex-wrap gap-1">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                    {{ $service->category->name ?? 'Uncategorized' }}
                                </span>
                                
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                    {{ ucfirst(str_replace('_', ' ', $service->pricing_model)) }}
                                </span>
                                
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </div>

                            <!-- Description -->
                            @if($service->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $service->description }}</p>
                            @endif

                            <!-- Pricing Details -->
                            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Base Price:</span>
                                    <span class="font-semibold text-blue-600">
                                        TSh {{ number_format($service->price ?? $service->price_max ?? 0) }}
                                    </span>
                                </div>
                                @if($service->price_min && $service->price_max && $service->price_min != $service->price_max)
                                    <div class="flex justify-between items-center text-sm mt-1">
                                        <span class="text-gray-600">Price Range:</span>
                                        <span class="font-semibold text-blue-600">
                                            TSh {{ number_format($service->price_min) }} - {{ number_format($service->price_max) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Stats -->
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                <div class="flex items-center gap-3">
                                    <span><i class="fas fa-heart mr-1 text-red-400"></i>{{ $service->likes()->count() }}</span>
                                    <span><i class="fas fa-star mr-1 text-yellow-400"></i>{{ number_format($this->getServiceRating($service), 1) }}</span>
                                    <span><i class="fas fa-eye mr-1 text-blue-400"></i>{{ $service->views ?? 0 }}</span>
                                </div>
                                @if(isset($service->distance))
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>{{ number_format($service->distance, 1) }}km</span>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('view.service.details', $service->id) }}"
                               class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 text-center">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $services->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-3xl text-blue-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No services found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your search or filters to find what you're looking for.</p>
                <button 
                    wire:click="$set('search', ''); $set('categoryFilter', ''); $set('priceRange', ''); $set('pricingModelFilter', '');"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200"
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
    if (confirm('Please login to like and bookmark services. Would you like to login now?')) {
        window.location.href = '/login';
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 max-w-sm rounded-lg shadow-lg p-4 transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-blue-500 text-white' : 
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
