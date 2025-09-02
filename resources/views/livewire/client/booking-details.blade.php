<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <div class="flex items-center mb-2">
                        <a href="{{ route('user.dashboard') }}" class="text-blue-100 hover:text-white mr-4">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <h1 class="text-2xl sm:text-3xl font-bold">Booking Details</h1>
                    </div>
                    <p class="text-blue-100 text-sm sm:text-base">Booking #{{ $booking->id }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $this->getBookingStatusColor($booking->status) }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $this->getPaymentStatusColor($booking->payment_status) }}">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Flash Messages -->
        @if(session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Overview -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Booking Overview</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($booking->venue)
                                    <i class="fas fa-building text-blue-600 text-2xl"></i>
                                @else
                                    <i class="fas fa-concierge-bell text-blue-600 text-2xl"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                    @if($booking->venue)
                                        {{ $booking->venue->name }}
                                    @elseif($booking->bookingItems->count() > 0)
                                        {{ $booking->bookingItems->first()->service->name ?? 'Service Booking' }}
                                    @else
                                        Booking #{{ $booking->id }}
                                    @endif
                                </h3>
                                
                                @if($booking->venue && $booking->venue->description)
                                    <p class="text-gray-600 mb-4">{{ $booking->venue->description }}</p>
                                @elseif($booking->bookingItems->count() > 0 && $booking->bookingItems->first()->service->description)
                                    <p class="text-gray-600 mb-4">{{ $booking->bookingItems->first()->service->description }}</p>
                                @endif

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Event Date</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($booking->event_date)->format('F j, Y') }}
                                            @if($booking->event_time)
                                                <br><span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($booking->event_time)->format('g:i A') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Booking Date</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $booking->created_at->format('F j, Y') }}
                                            <br><span class="text-sm text-gray-600">{{ $booking->created_at->format('g:i A') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                @if(($booking->venue && $booking->venue->location_name) || ($booking->bookingItems->count() > 0 && $booking->bookingItems->first()->service->location_name))
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Location Information</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-yellow-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-2">Event Location</h3>
                                    @if($booking->venue && $booking->venue->location_name)
                                        <p class="text-gray-600 mb-2">{{ $booking->venue->location_name }}</p>
                                        @if($booking->venue->latitude && $booking->venue->longitude)
                                            <div class="bg-gray-100 rounded-lg h-48 mb-4">
                                                <iframe 
                                                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q={{ $booking->venue->latitude }},{{ $booking->venue->longitude }}&zoom=15&maptype=roadmap"
                                                    width="100%" 
                                                    height="100%" 
                                                    style="border:0;" 
                                                    allowfullscreen="" 
                                                    loading="lazy" 
                                                    referrerpolicy="no-referrer-when-downgrade"
                                                    class="rounded-lg"
                                                ></iframe>
                                            </div>
                                            <a 
                                                href="https://www.google.com/maps?q={{ $booking->venue->latitude }},{{ $booking->venue->longitude }}" 
                                                target="_blank" 
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium"
                                            >
                                                <i class="fas fa-external-link-alt mr-2"></i>
                                                Open in Google Maps
                                            </a>
                                        @endif
                                    @elseif($booking->bookingItems->count() > 0 && $booking->bookingItems->first()->service->location_name)
                                        <p class="text-gray-600 mb-2">{{ $booking->bookingItems->first()->service->location_name }}</p>
                                        @if($booking->bookingItems->first()->service->latitude && $booking->bookingItems->first()->service->longitude)
                                            <div class="bg-gray-100 rounded-lg h-48 mb-4">
                                                <iframe 
                                                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q={{ $booking->bookingItems->first()->service->latitude }},{{ $booking->bookingItems->first()->service->longitude }}&zoom=15&maptype=roadmap"
                                                    width="100%" 
                                                    height="100%" 
                                                    style="border:0;" 
                                                    allowfullscreen="" 
                                                    loading="lazy" 
                                                    referrerpolicy="no-referrer-when-downgrade"
                                                    class="rounded-lg"
                                                ></iframe>
                                            </div>
                                            <a 
                                                href="https://www.google.com/maps?q={{ $booking->bookingItems->first()->service->latitude }},{{ $booking->bookingItems->first()->service->longitude }}" 
                                                target="_blank" 
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium"
                                            >
                                                <i class="fas fa-external-link-alt mr-2"></i>
                                                Open in Google Maps
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Services (if any) -->
                @if($booking->bookingItems->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Included Services</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($booking->bookingItems as $item)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-concierge-bell text-purple-600"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $item->service->name ?? 'Service' }}</h4>
                                                @if($item->service && $item->service->description)
                                                    <p class="text-sm text-gray-600">{{ Str::limit($item->service->description, 100) }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500">Quantity: {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-semibold text-gray-900">TSh {{ number_format($item->price) }}</p>
                                            <p class="text-sm text-gray-600">per item</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Cancellation Information -->
                @if($booking->status === 'cancelled' && $booking->cancellation_reason)
                    <div class="bg-white rounded-lg shadow-sm border border-red-200">
                        <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                            <h2 class="text-lg font-semibold text-red-900">Cancellation Details</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-red-900 mb-2">Booking Cancelled</h3>
                                    <p class="text-red-700 mb-2">{{ $booking->cancellation_reason }}</p>
                                    @if($booking->cancelled_at)
                                        <p class="text-sm text-red-600">
                                            Cancelled on {{ $booking->cancelled_at->format('F j, Y \a\t g:i A') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pricing Summary -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Pricing Summary</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if($booking->venue)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Venue Rental</span>
                                    <span class="font-semibold">TSh {{ number_format($booking->venue->price_max ?? $booking->venue->base_price ?? 0) }}</span>
                                </div>
                            @endif
                            
                            @if($booking->bookingItems->count() > 0)
                                @foreach($booking->bookingItems as $item)
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">{{ $item->service->name ?? 'Service' }}</span>
                                        <span class="font-semibold">TSh {{ number_format($item->price * $item->quantity) }}</span>
                                    </div>
                                @endforeach
                            @endif
                            
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                                    <span class="text-xl font-bold text-blue-600">TSh {{ number_format($booking->total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Contact Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Provider</p>
                                <p class="font-semibold text-gray-900">
                                    @if($booking->venue)
                                        {{ $booking->venue->user->name }}
                                    @elseif($booking->bookingItems->count() > 0)
                                        {{ $booking->bookingItems->first()->service->user->name ?? 'Service Provider' }}
                                    @else
                                        {{ $booking->user->name }}
                                    @endif
                                </p>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Message
                                </button>
                                <button class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                                    <i class="fas fa-phone mr-2"></i>
                                    Call
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if($this->canCancel())
                                <button 
                                    wire:click="openCancelModal"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200"
                                >
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Cancel Booking
                                </button>
                            @endif
                            
                            <a 
                                href="{{ route('user.dashboard') }}" 
                                class="block w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 text-center"
                            >
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Modal -->
    @if($showCancelModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-red-900">Cancel Booking</h3>
                        <button wire:click="closeCancelModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Warning -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-red-900 mb-1">Important Notice</h4>
                                <p class="text-sm text-red-700">
                                    Cancelling this booking is a serious action. Please provide a detailed reason for cancellation. 
                                    This action cannot be undone and may affect your booking history.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form -->
                    <form wire:submit.prevent="cancelBooking">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for Cancellation <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                wire:model="cancellationReason"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="Please provide a detailed reason for cancelling this booking..."
                                required
                            ></textarea>
                            @error('cancellationReason') 
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <button 
                                type="button"
                                wire:click="closeCancelModal" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200"
                            >
                                Keep Booking
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>Cancel Booking</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// Auto-hide flash messages
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.querySelectorAll('[class*="bg-green-100"], [class*="bg-red-100"]');
    flashMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.transition = 'opacity 0.5s';
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 500);
        }, 5000);
    });
});
</script>

