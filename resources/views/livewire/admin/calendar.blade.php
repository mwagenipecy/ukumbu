<div class="p-6 bg-white rounded-lg shadow-lg">
    <!-- Calendar Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <h2 class="text-2xl font-bold text-gray-900">Admin Calendar</h2>
            <div class="flex space-x-2">
                <button wire:click="changeView('month')" 
                        class="px-3 py-1 text-sm rounded {{ $view === 'month' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Month
                </button>
                <button wire:click="changeView('week')" 
                        class="px-3 py-1 text-sm rounded {{ $view === 'week' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Week
                </button>
                <button wire:click="changeView('day')" 
                        class="px-3 py-1 text-sm rounded {{ $view === 'day' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Day
                </button>
            </div>
        </div>
        
        <div class="flex items-center space-x-4">
            <!-- Venue Filter -->
            <select wire:model="selectedVenue" wire:change="loadBookings" class="px-3 py-2 border border-gray-300 rounded-md">
                <option value="">All Venues</option>
                @foreach($venues as $venue)
                    <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                @endforeach
            </select>
            
            <!-- Navigation -->
            <div class="flex items-center space-x-2">
                <button wire:click="previousPeriod" class="p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <span class="text-lg font-semibold text-gray-900">
                    @if($view === 'month')
                        {{ Carbon\Carbon::parse($selectedDate)->format('F Y') }}
                    @elseif($view === 'week')
                        {{ Carbon\Carbon::parse($selectedDate)->startOfWeek()->format('M j') }} - 
                        {{ Carbon\Carbon::parse($selectedDate)->endOfWeek()->format('M j, Y') }}
                    @else
                        {{ Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                    @endif
                </span>
                
                <button wire:click="nextPeriod" class="p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            
            <button wire:click="$set('selectedDate', '{{ now()->format('Y-m-d') }}')" 
                    class="px-4 py-2 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600">
                Today
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <!-- Calendar Views -->
    @if($view === 'month')
        <!-- Month View -->
        <div class="grid grid-cols-7 gap-1">
            <!-- Day Headers -->
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div class="p-2 text-center text-sm font-semibold text-gray-600 bg-gray-50">
                    {{ $day }}
                </div>
            @endforeach
            
            <!-- Calendar Days -->
            @foreach($monthDays as $day)
                <div class="min-h-[120px] p-2 border border-gray-200 {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50' }} {{ $day['isToday'] ? 'ring-2 ring-blue-500' : '' }}">
                    <div class="text-right">
                        <span class="text-sm {{ $day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400' }} {{ $day['isToday'] ? 'font-bold' : '' }}">
                            {{ $day['day'] }}
                        </span>
                    </div>
                    
                    <!-- Bookings for this day -->
                    <div class="mt-1 space-y-1">
                        @foreach($day['bookings'] as $booking)
                            <div class="text-xs p-1 rounded cursor-pointer {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}"
                                 wire:click="showBooking({{ $booking->id }})">
                                <div class="font-semibold">{{ $booking->venue->name }}</div>
                                <div>{{ $booking->user->name }}</div>
                                <div class="text-xs">{{ $booking->status }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        
    @elseif($view === 'week')
        <!-- Week View -->
        <div class="grid grid-cols-7 gap-1">
            <!-- Day Headers -->
            @foreach($weekDays as $day)
                <div class="p-2 text-center text-sm font-semibold text-gray-600 bg-gray-50">
                    <div>{{ $day['day'] }}</div>
                    <div class="text-xs text-gray-500">{{ $day['fullDate'] }}</div>
                </div>
            @endforeach
            
            <!-- Week Days -->
            @foreach($weekDays as $day)
                <div class="min-h-[200px] p-2 border border-gray-200 bg-white {{ $day['isToday'] ? 'ring-2 ring-blue-500' : '' }}">
                    <!-- Bookings for this day -->
                    <div class="space-y-1">
                        @foreach($day['bookings'] as $booking)
                            <div class="text-xs p-2 rounded cursor-pointer {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}"
                                 wire:click="showBooking({{ $booking->id }})">
                                <div class="font-semibold">{{ $booking->venue->name }}</div>
                                <div>{{ $booking->user->name }}</div>
                                <div class="text-xs">{{ $booking->status }}</div>
                                <div class="text-xs text-gray-600">{{ $booking->total_amount }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        
    @else
        <!-- Day View -->
        <div class="space-y-4">
            <div class="text-center">
                <h3 class="text-xl font-semibold text-gray-900">
                    {{ Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}
                </h3>
            </div>
            
            <!-- Bookings for this day -->
            <div class="space-y-3">
                @forelse($bookings as $booking)
                    <div class="p-4 border border-gray-200 rounded-lg {{ $booking->status === 'confirmed' ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $booking->venue->name }}</h4>
                                <p class="text-sm text-gray-600">Booked by: {{ $booking->user->name }}</p>
                                <p class="text-sm text-gray-600">Amount: {{ $booking->total_amount }}</p>
                                <p class="text-sm text-gray-600">Status: {{ ucfirst($booking->status) }}</p>
                            </div>
                            <div class="flex space-x-2">
                                @if($booking->status === 'pending')
                                    <button wire:click="approveBooking({{ $booking->id }})" 
                                            class="px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                                        Approve
                                    </button>
                                    <button wire:click="rejectBooking({{ $booking->id }})" 
                                            class="px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                        Reject
                                    </button>
                                @endif
                                <button wire:click="showBooking({{ $booking->id }})" 
                                        class="px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        No bookings for this date.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Booking Details Modal -->
    @if($showBookingModal && $selectedBooking)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Details</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <span class="font-semibold">Venue:</span>
                            <span>{{ $selectedBooking->venue->name }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Client:</span>
                            <span>{{ $selectedBooking->user->name }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Email:</span>
                            <span>{{ $selectedBooking->user->email }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Event Date:</span>
                            <span>{{ Carbon\Carbon::parse($selectedBooking->event_date)->format('F j, Y') }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Amount:</span>
                            <span>{{ $selectedBooking->total_amount }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Status:</span>
                            <span class="px-2 py-1 text-xs rounded {{ $selectedBooking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($selectedBooking->status) }}
                            </span>
                        </div>
                        <div>
                            <span class="font-semibold">Payment Status:</span>
                            <span class="px-2 py-1 text-xs rounded {{ $selectedBooking->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($selectedBooking->payment_status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        @if($selectedBooking->status === 'pending')
                            <button wire:click="approveBooking({{ $selectedBooking->id }})" 
                                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                Approve
                            </button>
                            <button wire:click="rejectBooking({{ $selectedBooking->id }})" 
                                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                Reject
                            </button>
                        @endif
                        <button wire:click="closeBookingModal" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

