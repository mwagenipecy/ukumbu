<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home w-4 h-4 mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 w-4 h-4 mx-1"></i>
                        <a href="{{ route('services.management') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600">Services</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 w-4 h-4 mx-1"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500">{{ $service->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Service Header -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-start space-x-4">
                        <div class="h-20 w-20 flex-shrink-0">
                            @if($service->image_url)
                                <img src="{{ $service->image_url }}" 
                                     alt="{{ $service->name }}" 
                                     class="h-20 w-20 rounded-lg object-cover">
                            @else
                                <div class="h-20 w-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-concierge-bell text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $service->name }}</h1>
                            <div class="flex items-center mt-2 space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->getCategoryColor($service->category->name ?? 'Unknown') }}">
                                    {{ $service->category->name ?? 'Uncategorized' }}
                                </span>
                                @if($averageRating > 0)
                                    <div class="flex items-center">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-sm {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">{{ number_format($averageRating, 1) }} ({{ $totalReviews }} reviews)</span>
                                    </div>
                                @endif
                            </div>
                            @if($service->location_name)
                                <div class="flex items-center mt-2 text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>{{ $service->location_name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8 px-6">
                            <button wire:click="setActiveTab('overview')"
                                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Overview
                            </button>
                            <button wire:click="setActiveTab('provider')"
                                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'provider' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Provider
                            </button>
                            <button wire:click="setActiveTab('reviews')"
                                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'reviews' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Reviews ({{ $totalReviews }})
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Overview Tab -->
                        @if($activeTab === 'overview')
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-3">About This Service</h3>
                                    <p class="text-gray-700 leading-relaxed">{{ $service->description }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Service Details</h4>
                                        <dl class="space-y-2">
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600">Category:</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $service->category->name ?? 'Uncategorized' }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600">Pricing Model:</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $service->pricing_model)) }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600">Location:</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $service->location_name ?: 'Flexible' }}</dd>
                                            </div>
                                            @if($service->availability)
                                                <div class="flex justify-between">
                                                    <dt class="text-sm text-gray-600">Availability:</dt>
                                                    <dd class="text-sm font-medium text-gray-900">{{ $service->availability }}</dd>
                                                </div>
                                            @endif
                                        </dl>
                                    </div>

                                    @if($service->features)
                                        <div>
                                            <h4 class="text-md font-medium text-gray-900 mb-3">What's Included</h4>
                                            <ul class="space-y-1">
                                                @foreach(explode(',', $service->features) as $feature)
                                                    <li class="flex items-center text-sm text-gray-700">
                                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                                        {{ trim($feature) }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Provider Tab -->
                        @if($activeTab === 'provider')
                            <div class="max-w-2xl">
                                <div class="flex items-start space-x-4">
                                    <div class="h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-bold text-xl">
                                            {{ strtoupper(substr($service->user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-medium text-gray-900">{{ $service->user->name }}</h3>
                                        <p class="text-gray-600">{{ ucfirst($service->user->role) }} Provider</p>
                                        <div class="mt-4 space-y-2">
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium text-gray-500 w-20">Joined:</span>
                                                <span class="text-sm text-gray-900">{{ $service->user->created_at->format('M d, Y') }}</span>
                                            </div>
                                            @if($service->user->email_verified_at)
                                                <div class="flex items-center">
                                                    <span class="text-sm font-medium text-gray-500 w-20">Status:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Verified Provider
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-6">
                                            <a href="mailto:{{ $service->user->email }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-envelope mr-2"></i>
                                                Contact Provider
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Reviews Tab -->
                        @if($activeTab === 'reviews')
                            <div class="space-y-6">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-medium text-gray-900">Customer Reviews</h3>
                                    @auth
                                        <button wire:click="openReviewModal"
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-star mr-2"></i>
                                            Write Review
                                        </button>
                                    @endauth
                                </div>

                                @if($reviews->count() > 0)
                                    <div class="space-y-6">
                                        @foreach($reviews as $review)
                                            <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                                <div class="flex items-start space-x-4">
                                                    <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                        <span class="text-gray-600 font-medium text-sm">
                                                            {{ strtoupper(substr($review->user->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between">
                                                            <h4 class="font-medium text-gray-900">{{ $review->user->name }}</h4>
                                                            <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                        <div class="flex items-center mt-1 mb-2">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                            @endfor
                                                            <span class="ml-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
                                                        </div>
                                                        @if($review->comment)
                                                            <p class="text-gray-700">{{ $review->comment }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        {{ $reviews->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <i class="fas fa-star text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Reviews Yet</h3>
                                        <p class="text-gray-500">Be the first to review this service!</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <!-- Booking Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border-2 border-gray-200">
                        <div class="text-center mb-6">
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $this->formatPrice($service->pricing, $service->pricing_model) }}
                            </div>
                        </div>

                        @auth
                            <button wire:click="openBookingModal"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Book This Service
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Login to Book
                            </a>
                        @endauth

                        <div class="mt-4 space-y-3 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                Secure booking process
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-blue-500 mr-2"></i>
                                Instant confirmation
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-purple-500 mr-2"></i>
                                24/7 customer support
                            </div>
                        </div>
                    </div>

                    <!-- Related Services -->
                    @if($relatedServices->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Related Services</h3>
                            <div class="space-y-4">
                                @foreach($relatedServices as $relatedService)
                                    <div class="flex items-center space-x-3">
                                        <div class="h-12 w-12 flex-shrink-0">
                                            @if($relatedService->image_url)
                                                <img src="{{ $relatedService->image_url }}" 
                                                     alt="{{ $relatedService->name }}" 
                                                     class="h-12 w-12 rounded-lg object-cover">
                                            @else
                                                <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-concierge-bell text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <a href="{{ route('view.service.details', $relatedService->id) }}" 
                                               class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                                {{ $relatedService->name }}
                                            </a>
                                            <div class="text-xs text-gray-500">
                                                TSh {{ number_format($relatedService->pricing) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    @if($showBookingModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeBookingModal">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Book {{ $service->name }}</h3>
                    <form wire:submit.prevent="submitBooking">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Event Date *</label>
                                <input type="date" wire:model="eventDate" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('eventDate') border-red-300 @enderror"
                                       required>
                                @error('eventDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Event Time *</label>
                                <input type="time" wire:model="eventTime" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('eventTime') border-red-300 @enderror"
                                       required>
                                @error('eventTime') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                                <input type="number" wire:model="quantity" min="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('quantity') border-red-300 @enderror"
                                       required>
                                @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Guest Count</label>
                                <input type="number" wire:model="guestCount" min="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Estimated number of guests">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Special Requests</label>
                                <textarea wire:model="specialRequests" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Any special requirements or requests..."></textarea>
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="text-sm text-gray-600">Total Estimated Cost:</div>
                                <div class="text-lg font-bold text-gray-900">
                                    TSh {{ number_format($service->pricing * $quantity) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeBookingModal"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Submit Booking Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Review Modal -->
    @if($showReviewModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeReviewModal">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Write a Review</h3>
                    <form wire:submit.prevent="submitReview">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating *</label>
                                <div class="flex space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" wire:click="$set('rating', {{ $i }})"
                                                class="text-2xl {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endfor
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                                <textarea wire:model="reviewComment" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Share your experience with this service..."></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeReviewModal"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if (session()->has('booking_success'))
        <div class="fixed top-4 right-4 z-50 bg-blue-100 border border-blue-400 text-blue-700 px-6 py-4 rounded-lg shadow-lg" role="alert">
            <span class="block sm:inline">{{ session('booking_success') }}</span>
        </div>
    @endif
</div>

