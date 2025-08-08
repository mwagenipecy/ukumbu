<div>
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section with Images -->
    <div class="relative">
        @php
            $gallery = is_array($venue->gallery) ? $venue->gallery : [];
        @endphp
        
        @if(count($gallery) > 0)
            <!-- Main Image -->
            <div class="relative h-96 md:h-[500px] overflow-hidden">
                <img 
                    src="{{ asset('storage/' . $gallery[$selectedImageIndex]) }}" 
                    alt="{{ $venue->name }}"
                    class="w-full h-full object-cover cursor-pointer"
                    wire:click="openImageModal({{ $selectedImageIndex }})"
                >
                
                <!-- Navigation Arrows -->
                @if(count($gallery) > 1)
                    <button 
                        wire:click="previousImage"
                        class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white p-3 rounded-full transition-all duration-200"
                    >
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button 
                        wire:click="nextImage"
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white p-3 rounded-full transition-all duration-200"
                    >
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif

                <!-- Image Counter -->
                @if(count($gallery) > 1)
                    <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                        {{ $selectedImageIndex + 1 }} / {{ count($gallery) }}
                    </div>
                @endif
            </div>

            <!-- Thumbnail Gallery -->
            @if(count($gallery) > 1)
                <div class="bg-white p-4 border-b">
                    <div class="container mx-auto">
                        <div class="flex gap-2 overflow-x-auto pb-2">
                            @foreach($gallery as $index => $image)
                                <button 
                                    wire:click="selectImage({{ $index }})"
                                    class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 {{ $index === $selectedImageIndex ? 'border-blue-500' : 'border-gray-200' }} transition-all duration-200"
                                >
                                    <img src="{{ asset('storage/' . $image) }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Default Image -->
            <div class="h-96 md:h-[500px] bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-building text-6xl text-blue-400 mb-4"></i>
                    <p class="text-blue-600">No images available</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Details -->
            <div class="lg:col-span-2">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $venue->name }}</h1>
                            <div class="flex items-center text-gray-600 mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-yellow-500"></i>
                                <span>{{ $venue->location_name }}</span>
                            </div>
                            
                            @if($venue->category)
                                <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                    {{ $venue->category->name }}
                                </span>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            @auth
                                <livewire:client.like-button :model="$venue" key="venue-detail-like-{{ $venue->id }}" />
                                <livewire:client.bookmark-button :model="$venue" key="venue-detail-bookmark-{{ $venue->id }}" />
                            @else
                                <button onclick="showLoginPrompt()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 p-3 rounded-full transition-all duration-200">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <button onclick="showLoginPrompt()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 p-3 rounded-full transition-all duration-200">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                            @endauth
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center gap-6 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-heart mr-1 text-red-400"></i>
                            <span>{{ $venue->likes()->count() }} likes</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-star mr-1 text-yellow-400"></i>
                            <span>{{ number_format($averageRating, 1) }} ({{ $totalReviews }} reviews)</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user mr-1 text-blue-400"></i>
                            <span>{{ $venue->user->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($venue->description)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">About This Venue</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $venue->description }}</p>
                    </div>
                @endif

                <!-- Available Services -->
                @if($venue->services->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Available Add-on Services</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($venue->services as $service)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors duration-200">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-medium text-gray-900">{{ $service->name }}</h3>
                                        <span class="text-blue-600 font-semibold">{{ $service->formatted_price }}</span>
                                    </div>
                                    @if($service->description)
                                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit($service->description, 100) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                            {{ $service->category->name ?? 'Service' }}
                                        </span>
                                        <button 
                                            wire:click="$dispatch('serviceToggled', { serviceId: {{ $service->id }} })"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200"
                                        >
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Location Map -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Location</h2>
                    <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center mb-4">
                        <div id="venue-map" class="w-full h-full rounded-lg">
                            <div class="flex items-center justify-center h-full text-gray-500">
                                <div class="text-center">
                                    <i class="fas fa-map-marker-alt text-3xl mb-2"></i>
                                    <p>Interactive map will load here</p>
                                    <p class="text-sm">{{ $venue->location_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p><strong>Address:</strong> {{ $venue->location_name }}</p>
                        <p><strong>Coordinates:</strong> {{ $venue->latitude }}, {{ $venue->longitude }}</p>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Reviews & Ratings</h2>
                        @auth
                            <button 
                                wire:click="toggleReviewForm"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                            >
                                {{ $userReview ? 'Edit Review' : 'Write Review' }}
                            </button>
                        @else
                            <button 
                                onclick="showLoginPrompt()"
                                class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg font-medium"
                            >
                                Login to Review
                            </button>
                        @endauth
                    </div>

                    <!-- Review Form -->
                    @if($showReviewForm)
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="font-medium text-gray-900 mb-4">{{ $userReview ? 'Update Your Review' : 'Write a Review' }}</h3>
                            <form wire:submit.prevent="submitReview">
                                <!-- Star Rating -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                    <div class="flex gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button 
                                                type="button"
                                                wire:click="$set('reviewRating', {{ $i }})"
                                                class="text-2xl {{ $i <= $reviewRating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors duration-200"
                                            >
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endfor
                                    </div>
                                </div>

                                <!-- Comment -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Comment (Optional)</label>
                                    <textarea 
                                        wire:model="reviewComment"
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Share your experience with this venue..."
                                    ></textarea>
                                </div>

                                <!-- Buttons -->
                                <div class="flex gap-3">
                                    <button 
                                        type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                                        wire:loading.attr="disabled"
                                    >
                                        <span wire:loading.remove>{{ $userReview ? 'Update Review' : 'Submit Review' }}</span>
                                        <span wire:loading>Submitting...</span>
                                    </button>
                                    <button 
                                        type="button"
                                        wire:click="$set('showReviewForm', false)"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Rating Summary -->
                    @if($totalReviews > 0)
                        <div class="flex items-center gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ number_format($averageRating, 1) }}</div>
                                <div class="flex justify-center mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-sm {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <div class="text-sm text-gray-600">{{ $totalReviews }} reviews</div>
                            </div>
                            
                            <!-- Rating Breakdown -->
                            <div class="flex-1">
                                @for($rating = 5; $rating >= 1; $rating--)
                                    @php
                                        $count = $venue->reviews()->where('rating', $rating)->count();
                                        $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="w-3">{{ $rating }}</span>
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="w-8 text-gray-600">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endif

                    <!-- Individual Reviews -->
                    @if($venue->reviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($venue->reviews->take(5) as $review)
                                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-medium">{{ substr($review->user->name, 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <h4 class="font-medium text-gray-900">{{ $review->user->name }}</h4>
                                                <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-sm text-gray-600">{{ $review->rating }}/5</span>
                                            </div>
                                            @if($review->comment)
                                                <p class="text-gray-700">{{ $review->comment }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($venue->reviews->count() > 5)
                            <div class="text-center mt-4">
                                <button class="text-blue-600 hover:text-blue-800 font-medium">
                                    View All {{ $totalReviews }} Reviews
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-comment-alt text-3xl mb-2"></i>
                            <p>No reviews yet. Be the first to review this venue!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Booking -->
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <!-- Booking Card -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <div class="text-center mb-6">
                            <div class="text-3xl font-bold text-blue-900 mb-1">
                                {{ $venue->formatted_price }}
                            </div>
                            <div class="text-sm text-gray-600">Base venue price</div>
                        </div>

                        <button 
                            wire:click="openBookingModal"
                            class="w-full bg-blue-900 hover:bg-blue-800 text-white py-3 px-4 rounded-lg font-semibold text-lg transition-colors duration-200 mb-4"
                        >
                            Book This Venue
                        </button>

                        <div class="text-center text-sm text-gray-600 mb-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            You won't be charged yet
                        </div>

                        <!-- Quick Info -->
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Response time:</span>
                                <span class="font-medium">Within 24 hours</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Cancellation:</span>
                                <span class="font-medium">Free cancellation</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Card -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Contact Venue Owner</h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium">{{ substr($venue->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $venue->user->name }}</div>
                                    <div class="text-sm text-gray-600">Venue Owner</div>
                                </div>
                            </div>
                            <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-envelope mr-2"></i>
                                Send Message
                            </button>
                            <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-phone mr-2"></i>
                                Call Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    @if($showBookingModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-md w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Book {{ $venue->name }}</h2>
                        <button wire:click="closeBookingModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="submitBooking">
                        <!-- Date Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Date</label>
                            <input 
                                type="date" 
                                wire:model="selectedDate"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                            @error('selectedDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Services Selection -->
                        @if($venue->services->count() > 0)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Add-on Services (Optional)</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($venue->services as $service)
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                value="{{ $service->id }}"
                                                wire:model="selectedServices"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            >
                                            <div class="ml-3 flex-1">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $service->name }}</div>
                                                        @if($service->description)
                                                            <div class="text-sm text-gray-600">{{ Str::limit($service->description, 50) }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="text-blue-600 font-semibold ml-2">{{ $service->formatted_price }}</div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Price Summary -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="font-medium text-gray-900 mb-3">Booking Summary</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Venue rental</span>
                                    <span>{{ $venue->formatted_price }}</span>
                                </div>
                                @if(!empty($selectedServices))
                                    @foreach($venue->services->whereIn('id', $selectedServices) as $service)
                                        <div class="flex justify-between">
                                            <span>{{ $service->name }}</span>
                                            <span>{{ $service->formatted_price }}</span>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="border-t border-gray-200 pt-2 flex justify-between font-semibold">
                                    <span>Total</span>
                                    <span>TSh {{ number_format($venue->base_price + $venue->services->whereIn('id', $selectedServices)->sum('price')) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="w-full bg-blue-900 hover:bg-blue-800 text-white py-3 px-4 rounded-lg font-semibold transition-colors duration-200"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Submit Booking Request</span>
                            <span wire:loading>Processing...</span>
                        </button>

                        <p class="text-xs text-gray-600 mt-3 text-center">
                            By submitting, you agree to our terms and conditions. You will receive a confirmation email with payment instructions.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Image Modal -->
    @if($showImageModal && count($gallery) > 0)
        <div class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50" wire:click="closeImageModal">
            <div class="relative max-w-4xl max-h-screen p-4" wire:click.stop>
                <img 
                    src="{{ asset('storage/' . $gallery[$selectedImageIndex]) }}" 
                    alt="{{ $venue->name }}"
                    class="max-w-full max-h-full object-contain rounded-lg"
                >
                
                <!-- Close Button -->
                <button 
                    wire:click="closeImageModal"
                    class="absolute top-4 right-4 bg-black bg-opacity-50 hover:bg-opacity-70 text-white p-2 rounded-full"
                >
                    <i class="fas fa-times"></i>
                </button>

                <!-- Navigation -->
                @if(count($gallery) > 1)
                    <button 
                        wire:click="previousImage"
                        class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white p-3 rounded-full"
                    >
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button 
                        wire:click="nextImage"
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white p-3 rounded-full"
                    >
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <!-- Image Counter -->
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full">
                        {{ $selectedImageIndex + 1 }} / {{ count($gallery) }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
function showLoginPrompt() {
    if (confirm('Please login to access this feature. Would you like to login now?')) {
        window.location.href = '/login';
    }
}

// Flash message handling
@if(session('booking_success'))
    showToast('{{ session('booking_success') }}', 'success');
@endif

@if(session('review_success'))
    showToast('{{ session('review_success') }}', 'success');
@endif

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


</div>
