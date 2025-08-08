<div>

<div>
    @section('title', 'Bookings Management')

    <div class="space-y-6">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bookings Management</h1>
                <p class="mt-1 text-sm text-gray-500">Monitor and manage all event bookings</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                @if(count($selectedBookings) > 0)
                    <button wire:click="toggleBulkModal" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-tasks mr-2"></i>
                        Bulk Actions ({{ count($selectedBookings) }})
                    </button>
                @endif
                <a href="" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Export Data
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-8 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-calendar text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Confirmed</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['confirmed'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-times text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Cancelled</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['cancelled'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-credit-card text-orange-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Payment Pending</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['payment_pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Payment Issues</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['payment_issues'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i class="fas fa-money-bill-wave text-emerald-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Revenue</p>
                        <p class="text-lg font-semibold text-gray-900">TSh {{ number_format($stats['total_revenue']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-calendar-plus text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">This Month</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['this_month'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search bookings..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Payment Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment</label>
                    <select wire:model.live="paymentStatusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Payments</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                        <option value="disputed">Disputed</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Event Date</label>
                    <select wire:model.live="dateRangeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" 
                           wire:model.live="startDate" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" 
                           wire:model.live="endDate" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                        Showing {{ $bookings->firstItem() ?? 0 }}-{{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} bookings
                    </span>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-700">Show:</label>
                        <select wire:model.live="perPage" class="px-3 py-1 border border-gray-300 rounded text-sm">
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
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
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('id')">
                                    <span>Booking ID</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('user_id')">
                                    <span>Customer</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('total_amount')">
                                    <span>Amount</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('status')">
                                    <span>Status</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('created_at')">
                                    <span>Booked</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50 {{ $booking->urgency_level === 'today' ? 'bg-red-50' : ($booking->urgency_level === 'urgent' ? 'bg-orange-50' : '') }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" 
                                           wire:model.live="selectedBookings" 
                                           value="{{ $booking->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">#{{ $booking->id }}</div>
                                        @if($booking->urgency_level === 'today')
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Today
                                            </span>
                                        @elseif($booking->urgency_level === 'urgent')
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                Tomorrow
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                    <div class="text-sm text-gray-900">
                                        <div class="font-medium">{{ $booking->event_date->format('M d, Y') }}</div>
                                        <div class="text-gray-500">{{ $booking->event_status }}</div>
                                        @if($booking->venue)
                                            <div class="text-gray-500 mt-1">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $booking->venue->name }}
                                            </div>
                                        @endif
                                        <div class="text-gray-500 mt-1">
                                            <i class="fas fa-list mr-1"></i>
                                            {{ $booking->total_services }} service(s)
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">TSh {{ number_format($booking->total_amount) }}</div>
                                    @if($booking->bookingItems->isNotEmpty())
                                        <div class="text-sm text-gray-500">
                                            {{ Str::limit($booking->services_list, 30) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($booking->status) }}">
                                        <div class="w-1.5 h-1.5 bg-current rounded-full mr-1"></div>
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getPaymentStatusColor($booking->payment_status) }}">
                                        <i class="{{ $booking->payment_status_icon }} mr-1"></i>
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                    @if($booking->hasPaymentIssues())
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Attention Required
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $booking->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $booking->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if($booking->isPending())
                                            <button wire:click="quickConfirm({{ $booking->id }})" 
                                                    class="text-green-600 hover:text-green-900" title="Quick Confirm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button wire:click="quickCancel({{ $booking->id }})" 
                                                    class="text-red-600 hover:text-red-900" title="Quick Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('admin.bookings.show', $booking) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" 
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                                
                                                @if($booking->isPending())
                                                    <button wire:click="openBookingModal({{ $booking->id }}, 'confirm')" 
                                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        <i class="fas fa-check mr-2 text-green-500"></i>Confirm Booking
                                                    </button>
                                                    <button wire:click="openBookingModal({{ $booking->id }}, 'cancel')" 
                                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        <i class="fas fa-times mr-2 text-red-500"></i>Cancel Booking
                                                    </button>
                                                @endif

                                                @if($booking->canBeCancelled())
                                                    <button wire:click="openBookingModal({{ $booking->id }}, 'refund')" 
                                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        <i class="fas fa-undo mr-2 text-blue-500"></i>Cancel & Refund
                                                    </button>
                                                @endif

                                                @if($booking->hasPaymentIssues())
                                                    <button wire:click="openBookingModal({{ $booking->id }}, 'payment_resolved')" 
                                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        <i class="fas fa-credit-card mr-2 text-green-500"></i>Mark Payment Resolved
                                                    </button>
                                                @endif

                                                <hr class="my-1">
                                                <a href="mailto:{{ $booking->user->email }}" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-envelope mr-2"></i>Contact Customer
                                                </a>
                                                <a href="{{ route('admin.bookings.invoice', $booking) }}" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-file-invoice mr-2"></i>View Invoice
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                                        <p class="text-gray-500 mb-4">No bookings match your current filters.</p>
                                        @if($search || $statusFilter || $paymentStatusFilter || $dateRangeFilter)
                                            <button wire:click="clearFilters" class="text-blue-600 hover:text-blue-800">
                                                Clear all filters
                                            </button>
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

    <!-- Booking Management Modal -->
    @if($showBookingModal && $managingBooking)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeBookingModal">
            <div class="relative top-20 mx-auto p-5 border max-w-2xl shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ ucfirst(str_replace('_', ' ', $managementAction)) }} Booking #{{ $managingBooking->id }}
                    </h3>
                    
                    <!-- Booking Preview -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Customer Details</h4>
                                <p class="text-sm text-gray-600">{{ $managingBooking->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $managingBooking->user->email }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Event Details</h4>
                                <p class="text-sm text-gray-600">Date: {{ $managingBooking->event_date->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-600">Amount: TSh {{ number_format($managingBooking->total_amount) }}</p>
                                @if($managingBooking->venue)
                                    <p class="text-sm text-gray-600">Venue: {{ $managingBooking->venue->name }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @if($managingBooking->bookingItems->count() > 0)
                            <div class="mt-4">
                                <h4 class="font-medium text-gray-900 mb-2">Services</h4>
                                <div class="space-y-1">
                                    @foreach($managingBooking->bookingItems as $item)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">{{ $item->service->name }} (x{{ $item->quantity }})</span>
                                            <span class="text-gray-900">TSh {{ number_format($item->price) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <form wire:submit.prevent="manageBooking">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Reason for {{ $managementAction === 'payment_resolved' ? 'resolving payment' : $managementAction }} *
                                </label>
                                <textarea wire:model="managementReason" 
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('managementReason') border-red-300 @enderror"
                                          placeholder="Please provide a detailed reason for this action..."
                                          required></textarea>
                                @error('managementReason')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($managementAction === 'refund')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Refund Amount (TSh) *
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                               wire:model="refundAmount" 
                                               step="0.01"
                                               min="0"
                                               max="{{ $managingBooking->total_amount }}"
                                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('refundAmount') border-red-300 @enderror"
                                               placeholder="0.00">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                            <span class="text-gray-500">TSh</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Maximum refund: TSh {{ number_format($managingBooking->total_amount) }}
                                    </p>
                                    @error('refundAmount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" 
                                    wire:click="closeBookingModal"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 rounded-lg text-white font-medium
                                           {{ $managementAction === 'confirm' ? 'bg-green-600 hover:bg-green-700' : 
                                              ($managementAction === 'cancel' || $managementAction === 'refund' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700') }}">
                                <i class="fas fa-{{ $managementAction === 'confirm' ? 'check' : ($managementAction === 'cancel' || $managementAction === 'refund' ? 'times' : 'credit-card') }} mr-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $managementAction)) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Actions Modal -->
    @if($showBulkModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="toggleBulkModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions</h3>
                    <p class="text-sm text-gray-500 mb-6">{{ count($selectedBookings) }} booking(s) selected</p>
                    <div class="space-y-3">
                        <button wire:click="bulkConfirm" 
                                wire:confirm="Are you sure you want to confirm {{ count($selectedBookings) }} selected bookings?"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Confirm Selected
                        </button>
                        <button wire:click="bulkCancel"
                                wire:confirm="Are you sure you want to cancel {{ count($selectedBookings) }} selected bookings?"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel Selected
                        </button>
                        
                        <!-- Payment Status Updates -->
                        <div class="border-t pt-3 mt-3">
                            <p class="text-sm text-gray-600 mb-2">Update Payment Status:</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button wire:click="bulkUpdatePayment('paid')"
                                        wire:confirm="Mark {{ count($selectedBookings) }} selected bookings as paid?"
                                        class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm transition-colors">
                                    <i class="fas fa-check mr-1"></i>Paid
                                </button>
                                <button wire:click="bulkUpdatePayment('failed')"
                                        wire:confirm="Mark {{ count($selectedBookings) }} selected bookings as failed?"
                                        class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm transition-colors">
                                    <i class="fas fa-times mr-1"></i>Failed
                                </button>
                            </div>
                        </div>
                        
                        <button wire:click="toggleBulkModal" 
                                class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

   
</div>

</div>
