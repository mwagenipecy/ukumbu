<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Booking Management</h1>
            <p class="mt-2 text-gray-600">Manage bookings for your venues</p>
        </div>

        <!-- Flash Messages -->
        @if(session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by customer, venue, or booking ID..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Filter</label>
                    <select wire:model.live="dateFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="upcoming">Upcoming</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button 
                        wire:click="$set('search', ''); $set('statusFilter', ''); $set('dateFilter', '')"
                        class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($bookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Booking Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    Booking #{{ $booking->id }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $booking->venue->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $booking->event_date->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $booking->event_date->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            TSh {{ number_format($booking->total_amount) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $booking->payment_status }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $booking->status_color }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button 
                                                wire:click="viewBooking({{ $booking->id }})"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                View
                                            </button>
                                            
                                            @if($booking->status === 'pending')
                                                <button 
                                                    wire:click="confirmBooking({{ $booking->id }})"
                                                    wire:confirm="Are you sure you want to confirm this booking?"
                                                    class="text-green-600 hover:text-green-900"
                                                >
                                                    Confirm
                                                </button>
                                                <button 
                                                    wire:click="cancelBooking({{ $booking->id }})"
                                                    wire:confirm="Are you sure you want to cancel this booking?"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Cancel
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                    <p class="text-gray-600">No bookings match your current filters.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Booking Details Modal -->
    @if($showBookingModal && $selectedBooking)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">
                            Booking #{{ $selectedBooking->id }} Details
                        </h3>
                        <button wire:click="closeBookingModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="py-4 space-y-4">
                        <!-- Customer Info -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Customer Information</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p><strong>Name:</strong> {{ $selectedBooking->user->name }}</p>
                                <p><strong>Email:</strong> {{ $selectedBooking->user->email }}</p>
                            </div>
                        </div>

                        <!-- Venue Info -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Venue Information</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p><strong>Venue:</strong> {{ $selectedBooking->venue->name }}</p>
                                <p><strong>Location:</strong> {{ $selectedBooking->venue->location_name }}</p>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Event Details</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p><strong>Event Date:</strong> {{ $selectedBooking->event_date->format('F d, Y') }}</p>
                                <p><strong>Status:</strong> 
                                    <span class="px-2 py-1 text-xs rounded-full {{ $selectedBooking->status_color }}">
                                        {{ ucfirst($selectedBooking->status) }}
                                    </span>
                                </p>
                                <p><strong>Payment Status:</strong> {{ ucfirst($selectedBooking->payment_status) }}</p>
                            </div>
                        </div>

                        <!-- Services -->
                        @if($selectedBooking->bookingItems->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Additional Services</h4>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    @foreach($selectedBooking->bookingItems as $item)
                                        <div class="flex justify-between items-center py-1">
                                            <span>{{ $item->service->name }}</span>
                                            <span>TSh {{ number_format($item->price) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Total Amount -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Payment Summary</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex justify-between items-center">
                                    <span>Venue Base Price:</span>
                                    <span>TSh {{ number_format($selectedBooking->venue->base_price) }}</span>
                                </div>
                                @foreach($selectedBooking->bookingItems as $item)
                                    <div class="flex justify-between items-center">
                                        <span>{{ $item->service->name }}:</span>
                                        <span>TSh {{ number_format($item->price) }}</span>
                                    </div>
                                @endforeach
                                <hr class="my-2">
                                <div class="flex justify-between items-center font-semibold">
                                    <span>Total Amount:</span>
                                    <span>TSh {{ number_format($selectedBooking->total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($selectedBooking->status === 'pending')
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <button 
                                wire:click="cancelBooking({{ $selectedBooking->id }})"
                                wire:confirm="Are you sure you want to cancel this booking?"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                            >
                                Cancel Booking
                            </button>
                            <button 
                                wire:click="confirmBooking({{ $selectedBooking->id }})"
                                wire:confirm="Are you sure you want to confirm this booking?"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                            >
                                Confirm Booking
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
