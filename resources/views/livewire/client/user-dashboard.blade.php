<div class="min-h-screen bg-gray-50">
    <!-- Responsive Header -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">My Dashboard</h1>
                    <p class="text-blue-100 text-sm sm:text-base">Welcome back, {{ auth()->user()->name }}! Here's what's happening with your bookings.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex items-center text-blue-100">
                        <i class="fas fa-user-circle text-2xl mr-2"></i>
                        <span class="text-sm sm:text-base">{{ auth()->user()->email }}</span>
                    </div>
                    <div class="flex items-center text-blue-100">
                        <i class="fas fa-calendar-alt text-lg mr-2"></i>
                        <span class="text-sm sm:text-base">{{ now()->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Flash Messages -->
        @if(session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Responsive Navigation Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex flex-col sm:flex-row">
                    <button 
                        wire:click="switchTab('overview')"
                        class="flex-1 py-3 sm:py-4 px-4 sm:px-6 border-b-2 sm:border-b-0 sm:border-r font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600 bg-blue-50 sm:bg-transparent' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 sm:hover:bg-transparent hover:border-gray-300' }}"
                    >
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        <span class="hidden sm:inline">Overview</span>
                        <span class="sm:hidden">Dashboard</span>
                    </button>
                    <button 
                        wire:click="switchTab('bookings')"
                        class="flex-1 py-3 sm:py-4 px-4 sm:px-6 border-b-2 sm:border-b-0 sm:border-r font-medium text-sm {{ $activeTab === 'bookings' ? 'border-blue-500 text-blue-600 bg-blue-50 sm:bg-transparent' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 sm:hover:bg-transparent hover:border-gray-300' }}"
                    >
                        <i class="fas fa-calendar-check mr-2"></i>
                        <span class="hidden sm:inline">My Bookings</span>
                        <span class="sm:hidden">Bookings</span>
                    </button>
                    <button 
                        wire:click="switchTab('payments')"
                        class="flex-1 py-3 sm:py-4 px-4 sm:px-6 font-medium text-sm {{ $activeTab === 'payments' ? 'border-blue-500 text-blue-600 bg-blue-50 sm:bg-transparent' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 sm:hover:bg-transparent hover:border-gray-300' }}"
                    >
                        <i class="fas fa-credit-card mr-2"></i>
                        <span class="hidden sm:inline">Payment History</span>
                        <span class="sm:hidden">Payments</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Overview Tab -->
        @if($activeTab === 'overview')
            <!-- Enhanced Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
                <!-- Total Bookings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-check text-blue-600 text-lg lg:text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-3 lg:ml-4 flex-1">
                            <p class="text-xs lg:text-sm font-medium text-gray-600">Total Bookings</p>
                            <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['total_bookings'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-chart-line mr-1"></i>
                                {{ $stats['completed_bookings'] }} completed
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Bookings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-green-600 text-lg lg:text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-3 lg:ml-4 flex-1">
                            <p class="text-xs lg:text-sm font-medium text-gray-600">Upcoming</p>
                            <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['upcoming_bookings'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-calendar-day mr-1"></i>
                                Next 30 days
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pending Payments -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-credit-card text-yellow-600 text-lg lg:text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-3 lg:ml-4 flex-1">
                            <p class="text-xs lg:text-sm font-medium text-gray-600">Pending Payments</p>
                            <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['pending_payments'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Requires attention
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Spent -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-purple-600 text-lg lg:text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-3 lg:ml-4 flex-1">
                            <p class="text-xs lg:text-sm font-medium text-gray-600">Total Spent</p>
                            <p class="text-lg lg:text-2xl font-semibold text-gray-900">TSh {{ number_format($stats['total_spent']) }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-wallet mr-1"></i>
                                All time
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
                <!-- Favorite Venues -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-heart text-red-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600">Favorite Venues</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $stats['favorite_venues'] }}</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                </div>

                <!-- Favorite Services -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bookmark text-indigo-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600">Favorite Services</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $stats['favorite_services'] }}</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                </div>

                <!-- Cancelled Bookings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-times-circle text-gray-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600">Cancelled</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $stats['cancelled_bookings'] }}</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Enhanced Upcoming Bookings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Upcoming Bookings</h3>
                        <span class="text-sm text-gray-500">{{ $upcomingBookings->count() }} upcoming</span>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    @if($upcomingBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingBookings as $booking)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-start sm:items-center space-x-3 sm:space-x-4 mb-3 sm:mb-0">
                                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                @if($booking->venue)
                                                    <i class="fas fa-building text-blue-600 text-sm sm:text-base"></i>
                                                @else
                                                    <i class="fas fa-concierge-bell text-blue-600 text-sm sm:text-base"></i>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 text-sm sm:text-base mb-1">
                                                    @if($booking->venue)
                                                        {{ $booking->venue->name }}
                                                    @elseif($booking->bookingItems->count() > 0)
                                                        {{ $booking->bookingItems->first()->service->name ?? 'Service Booking' }}
                                                    @else
                                                        Booking #{{ $booking->id }}
                                                    @endif
                                                </h4>
                                                <div class="space-y-1">
                                                    <p class="text-xs sm:text-sm text-gray-600">
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($booking->event_date)->format('M j, Y') }}
                                                        @if($booking->event_time)
                                                            at {{ \Carbon\Carbon::parse($booking->event_time)->format('H:i') }}
                                                        @endif
                                                    </p>
                                                    @if($booking->venue && $booking->venue->location_name)
                                                        <p class="text-xs sm:text-sm text-gray-500">
                                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                                            {{ $booking->venue->location_name }}
                                                        </p>
                                                    @elseif($booking->bookingItems->count() > 0 && $booking->bookingItems->first()->service->location_name)
                                                        <p class="text-xs sm:text-sm text-gray-500">
                                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                                            {{ $booking->bookingItems->first()->service->location_name }}
                                                        </p>
                                                    @endif
                                                    <p class="text-xs sm:text-sm text-gray-500">
                                                        <i class="fas fa-money-bill mr-1"></i>
                                                        TSh {{ number_format($booking->total_amount) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                            <div class="flex space-x-2">
                                                <a 
                                                    href="{{ route('user.booking.details', $booking->id) }}"
                                                    class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium"
                                                >
                                                    View Details
                                                </a>
                                                @if($booking->status === 'pending')
                                                    <button 
                                                        wire:click="cancelBooking({{ $booking->id }})"
                                                        class="text-red-600 hover:text-red-800 text-xs sm:text-sm font-medium"
                                                        onclick="return confirm('Are you sure you want to cancel this booking?')"
                                                    >
                                                        Cancel
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 text-center">
                            <a href="#" wire:click="switchTab('bookings')" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                View All Bookings
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-gray-400 text-xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No upcoming bookings</h4>
                            <p class="text-gray-600 mb-4 text-sm sm:text-base">Start exploring venues and services to make your first booking!</p>
                            <a href="/" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>
                                Browse Venues
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enhanced Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                        <span class="text-sm text-gray-500">{{ $recentActivity->count() }} activities</span>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    @if($recentActivity->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentActivity as $activity)
                                <div class="flex items-start space-x-3 sm:space-x-4 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="{{ $activity['icon'] }} {{ $activity['color'] }} text-sm sm:text-base"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 mb-1">{{ $activity['title'] }}</p>
                                                <p class="text-xs sm:text-sm text-gray-600 mb-1">{{ $activity['description'] }}</p>
                                                <p class="text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $activity['date']->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0 mt-2 sm:mt-0">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                    {{ $activity['status'] === 'completed' || $activity['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                       ($activity['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($activity['status']) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 text-center">
                            <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm">
                                View All Activity
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-history text-gray-400 text-xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No recent activity</h4>
                            <p class="text-gray-600 text-sm sm:text-base">Your activity will appear here as you make bookings and payments.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Enhanced Bookings Tab -->
        @if($activeTab === 'bookings')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">My Bookings</h3>
                        @if(isset($bookings) && $bookings->count() > 0)
                            <span class="text-sm text-gray-500">{{ $bookings->total() }} total bookings</span>
                        @endif
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    @if(isset($bookings) && $bookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($bookings as $booking)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                        <div class="flex items-start space-x-3 sm:space-x-4 mb-3 lg:mb-0">
                                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                @if($booking->venue)
                                                    <i class="fas fa-building text-blue-600 text-sm sm:text-base"></i>
                                                @else
                                                    <i class="fas fa-concierge-bell text-blue-600 text-sm sm:text-base"></i>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 text-sm sm:text-base mb-1">
                                                    @if($booking->venue)
                                                        {{ $booking->venue->name }}
                                                    @elseif($booking->bookingItems->count() > 0)
                                                        {{ $booking->bookingItems->first()->service->name ?? 'Service Booking' }}
                                                    @else
                                                        Booking #{{ $booking->id }}
                                                    @endif
                                                </h4>
                                                <div class="space-y-1">
                                                    <p class="text-xs sm:text-sm text-gray-600">
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($booking->event_date)->format('M j, Y') }}
                                                        @if($booking->event_time)
                                                            at {{ \Carbon\Carbon::parse($booking->event_time)->format('H:i') }}
                                                        @endif
                                                    </p>
                                                    @if($booking->venue && $booking->venue->location_name)
                                                        <p class="text-xs sm:text-sm text-gray-500">
                                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                                            {{ $booking->venue->location_name }}
                                                        </p>
                                                    @elseif($booking->bookingItems->count() > 0 && $booking->bookingItems->first()->service->location_name)
                                                        <p class="text-xs sm:text-sm text-gray-500">
                                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                                            {{ $booking->bookingItems->first()->service->location_name }}
                                                        </p>
                                                    @endif
                                                    <p class="text-xs sm:text-sm text-gray-500">
                                                        <i class="fas fa-money-bill mr-1"></i>
                                                        TSh {{ number_format($booking->total_amount) }}
                                                    </p>
                                                    @if($booking->bookingItems->count() > 0)
                                                        <p class="text-xs text-gray-400">
                                                            <i class="fas fa-list mr-1"></i>
                                                            {{ $booking->bookingItems->count() }} {{ $booking->bookingItems->count() === 1 ? 'service' : 'services' }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                   ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                            <div class="flex space-x-2">
                                                <a 
                                                    href="{{ route('user.booking.details', $booking->id) }}"
                                                    class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium"
                                                >
                                                    View Details
                                                </a>
                                                @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                                    <button 
                                                        wire:click="cancelBooking({{ $booking->id }})"
                                                        class="text-red-600 hover:text-red-800 text-xs sm:text-sm font-medium"
                                                        onclick="return confirm('Are you sure you want to cancel this booking?')"
                                                    >
                                                        Cancel
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Enhanced Pagination -->
                        <div class="mt-6">
                            {{ $bookings->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-gray-400 text-xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No bookings yet</h4>
                            <p class="text-gray-600 mb-4 text-sm sm:text-base">Start exploring venues and services to make your first booking!</p>
                            <a href="/" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>
                                Browse Venues
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Enhanced Payments Tab -->
        @if($activeTab === 'payments')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Payment History</h3>
                        @if(isset($payments) && $payments->count() > 0)
                            <span class="text-sm text-gray-500">{{ $payments->total() }} total payments</span>
                        @endif
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    @if(isset($payments) && $payments->count() > 0)
                        <div class="space-y-4">
                            @foreach($payments as $payment)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                        <div class="flex items-start space-x-3 sm:space-x-4 mb-3 lg:mb-0">
                                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-credit-card text-green-600 text-sm sm:text-base"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 text-sm sm:text-base mb-1">
                                                    @if($payment->booking->venue)
                                                        {{ $payment->booking->venue->name }}
                                                    @elseif($payment->booking->bookingItems->count() > 0)
                                                        {{ $payment->booking->bookingItems->first()->service->name ?? 'Service Payment' }}
                                                    @else
                                                        Payment #{{ $payment->id }}
                                                    @endif
                                                </h4>
                                                <div class="space-y-1">
                                                    <p class="text-xs sm:text-sm text-gray-600">
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($payment->booking->event_date)->format('M j, Y') }}
                                                    </p>
                                                    <p class="text-xs sm:text-sm text-gray-500">
                                                        <i class="fas fa-money-bill mr-1"></i>
                                                        TSh {{ number_format($payment->amount) }}
                                                    </p>
                                                    <p class="text-xs sm:text-sm text-gray-500">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $payment->created_at->format('M j, Y g:i A') }}
                                                    </p>
                                                    @if($payment->payment_method)
                                                        <p class="text-xs text-gray-400">
                                                            <i class="fas fa-credit-card mr-1"></i>
                                                            {{ ucfirst($payment->payment_method) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                            <a 
                                                href="{{ route('user.booking.details', $payment->booking_id) }}"
                                                class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium"
                                            >
                                                View Booking
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Enhanced Pagination -->
                        <div class="mt-6">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-credit-card text-gray-400 text-xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No payments yet</h4>
                            <p class="text-gray-600 mb-4 text-sm sm:text-base">Payments will appear here once you make bookings and complete transactions.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>


</div>

