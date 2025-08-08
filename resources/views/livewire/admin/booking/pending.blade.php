<div>

<div>
    @section('title', 'Pending Bookings - Confirmation Required')

    <div class="space-y-6">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pending Confirmation</h1>
                <p class="mt-1 text-sm text-gray-500">Bookings waiting for admin confirmation</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                @if(count($selectedBookings) > 0)
                    <div class="flex space-x-2">
                        <button wire:click="bulkConfirm" 
                                wire:confirm="Are you sure you want to confirm {{ count($selectedBookings) }} selected bookings?"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-check mr-2"></i>
                            Confirm ({{ count($selectedBookings) }})
                        </button>
                        <button wire:click="bulkCancel"
                                wire:confirm="Are you sure you want to cancel {{ count($selectedBookings) }} selected bookings?"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel ({{ count($selectedBookings) }})
                        </button>
                    </div>
                @endif
                <a href="{{ route('booking.management') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    All Bookings
                </a>
            </div>
        </div>

        <!-- Urgency Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-clock text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total Pending</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['total_pending'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Today's Events</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['today_events'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-calendar-day text-orange-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Tomorrow</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['tomorrow_events'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-calendar-week text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">This Week</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['this_week'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-hourglass-half text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Overdue</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['overdue'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-gray-500">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 rounded-lg">
                        <i class="fas fa-chart-line text-gray-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Avg Wait Time</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['avg_wait_time'] }}h</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search pending bookings..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Urgency Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urgency Level</label>
                    <select wire:model.live="urgencyFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Urgency Levels</option>
                        <option value="today">Events Today</option>
                        <option value="tomorrow">Events Tomorrow</option>
                        <option value="this_week">Events This Week</option>
                        <option value="overdue">Overdue (>2 days)</option>
                    </select>
                </div>

                <!-- Sort Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select wire:model.live="sortField" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="created_at">Date Requested</option>
                        <option value="event_date">Event Date</option>
                        <option value="total_amount">Amount</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Show</label>
                    <select wire:model.live="perPage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="20">20 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <button wire:click="clearFilters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </button>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">
                        Showing {{ $bookings->firstItem() ?? 0 }}-{{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} pending bookings
                    </span>
                </div>
            </div>
        </div>

        <!-- Pending Bookings List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" 
                                       wire:model.live="selectAll"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Urgency & Booking
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount & Services
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Waiting Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quick Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($bookings as $booking)
                            @php 
                                $urgency = $this->getUrgencyLevel($booking);
                                $urgencyColor = $this->getUrgencyColor($urgency);
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $urgency === 'critical' ? 'bg-red-50' : ($urgency === 'urgent' ? 'bg-orange-50' : '') }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" 
                                           wire:model.live="selectedBookings" 
                                           value="{{ $booking->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $urgencyColor }}">
                                            <i class="fas fa-{{ $urgency === 'critical' ? 'exclamation-triangle' : ($urgency === 'urgent' ? 'clock' : ($urgency === 'moderate' ? 'hourglass-half' : 'check-circle')) }} mr-1"></i>
                                            {{ ucfirst($urgency) }}
                                        </span>
                                        <div class="text-sm font-medium text-gray-900">#{{ $booking->id }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 {{ $this->getUserAvatarColor($booking->user->id) }} rounded-full flex items-center justify-center mr-3">
                                            <span class="font-medium text-sm">{{ $this->getUserInitials($booking->user->name) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 mb-1">
                                            {{ $booking->event_date->format('M d, Y') }}
                                            @if($booking->event_date->isToday())
                                                <span class="text-red-600 font-bold">(Today!)</span>
                                            @elseif($booking->event_date->isTomorrow())
                                                <span class="text-orange-600 font-bold">(Tomorrow)</span>
                                            @endif
                                        </div>
                                        <div class="text-gray-500">
                                            {{ $booking->getDaysUntilEvent() >= 0 ? 
                                               'In ' . $booking->getDaysUntilEvent() . ' days' : 
                                               abs($booking->getDaysUntilEvent()) . ' days ago' }}
                                        </div>
                                        @if($booking->venue)
                                            <div class="text-gray-500 mt-1">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $booking->venue->name }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">TSh {{ number_format($booking->total_amount) }}</div>
                                        <div class="text-gray-500 mt-1">
                                            {{ $booking->bookingItems->count() }} service(s)
                                        </div>
                                        @if($booking->bookingItems->isNotEmpty())
                                            <div class="text-gray-400 text-xs mt-1">
                                                {{ Str::limit($booking->bookingItems->pluck('service.name')->implode(', '), 40) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm">
                                        <div class="font-medium {{ $urgency === 'critical' ? 'text-red-600' : ($urgency === 'urgent' ? 'text-orange-600' : 'text-gray-900') }}">
                                            {{ $this->getWaitingTime($booking) }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Since {{ $booking->created_at->format('M d, H:i') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="openConfirmModal({{ $booking->id }})" 
                                                class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-check mr-1"></i>
                                            Confirm
                                        </button>
                                        <button wire:click="quickCancel({{ $booking->id }})" 
                                                wire:confirm="Are you sure you want to cancel this booking?"
                                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                            <i class="fas fa-times mr-1"></i>
                                            Cancel
                                        </button>
                                        <a href="{{ route('admin.bookings.show', $booking) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-check-circle text-4xl text-green-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No pending bookings!</h3>
                                        <p class="text-gray-500 mb-4">All bookings have been processed.</p>
                                        @if($search || $urgencyFilter)
                                            <button wire:click="clearFilters" class="text-blue-600 hover:text-blue-800">
                                                Clear filters to see all pending bookings
                                            </button>
                                        @else
                                            <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:text-blue-800">
                                                View all bookings
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($bookings->hasPages())
                <div class="bg-white px-6 py-3 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmModal && $confirmingBooking)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeConfirmModal">
            <div class="relative top-20 mx-auto p-5 border max-w-2xl shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Confirm Booking #{{ $confirmingBooking->id }}
                    </h3>
                    
                    <!-- Booking Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Info -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-user mr-2 text-blue-500"></i>
                                    Customer Details
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 {{ $this->getUserAvatarColor($confirmingBooking->user->id) }} rounded-full flex items-center justify-center mr-3">
                                            <span class="font-medium text-xs">{{ $this->getUserInitials($confirmingBooking->user->name) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $confirmingBooking->user->name }}</div>
                                            <div class="text-gray-600">{{ $confirmingBooking->user->email }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Info -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-calendar mr-2 text-green-500"></i>
                                    Event Details
                                </h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div><strong>Date:</strong> {{ $confirmingBooking->event_date->format('M d, Y') }}</div>
                                    <div><strong>Amount:</strong> TSh {{ number_format($confirmingBooking->total_amount) }}</div>
                                    @if($confirmingBooking->venue)
                                        <div><strong>Venue:</strong> {{ $confirmingBooking->venue->name }}</div>
                                    @endif
                                    <div><strong>Status:</strong> 
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending Confirmation
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Services List -->
                        @if($confirmingBooking->bookingItems->count() > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-list mr-2 text-purple-500"></i>
                                    Services Requested
                                </h4>
                                <div class="bg-white rounded-lg p-4 border">
                                    <div class="space-y-3">
                                        @foreach($confirmingBooking->bookingItems as $item)
                                            <div class="flex justify-between items-center">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">{{ $item->service->name }}</div>
                                                    <div class="text-sm text-gray-600">
                                                        Quantity: {{ $item->quantity }}
                                                        @if($item->service->pricing_model !== 'flat')
                                                            • {{ ucfirst(str_replace('_', ' ', $item->service->pricing_model)) }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-medium text-gray-900">TSh {{ number_format($item->price) }}</div>
                                                    @if($item->quantity > 1)
                                                        <div class="text-sm text-gray-500">TSh {{ number_format($item->price / $item->quantity) }} each</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="border-t pt-3 mt-3">
                                            <div class="flex justify-between items-center font-medium text-lg">
                                                <span>Total Amount</span>
                                                <span class="text-green-600">TSh {{ number_format($confirmingBooking->total_amount) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Confirmation Form -->
                    <form wire:submit.prevent="confirmBooking">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmation Note (Optional)
                            </label>
                            <textarea wire:model="confirmationNote" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Add a note that will be sent to the customer (e.g., special instructions, contact information, etc.)"></textarea>
                            <p class="mt-1 text-xs text-gray-500">This note will be included in the confirmation email to the customer.</p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" 
                                    wire:click="closeConfirmModal"
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

   
</div>

</div>
                                    
