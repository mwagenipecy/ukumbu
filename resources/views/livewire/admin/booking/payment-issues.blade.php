<div>
<div>
    @section('title', 'Payment Issues - Resolution Required')

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
                <h1 class="text-2xl font-bold text-gray-900">Payment Issues</h1>
                <p class="mt-1 text-sm text-gray-500">Resolve failed payments, disputes, and unpaid bookings</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                @if(count($selectedBookings) > 0)
                    <div class="flex space-x-2">
                        <button wire:click="bulkMarkPaid" 
                                wire:confirm="Are you sure you want to mark {{ count($selectedBookings) }} selected payments as completed?"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-check mr-2"></i>
                            Mark Paid ({{ count($selectedBookings) }})
                        </button>
                        <button wire:click="bulkRetryPayment"
                                wire:confirm="Are you sure you want to retry payment for {{ count($selectedBookings) }} selected bookings?"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-redo mr-2"></i>
                            Retry ({{ count($selectedBookings) }})
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

        <!-- Issue Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total Issues</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['total_issues'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-600">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Failed</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['failed_payments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-gavel text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Disputed</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['disputed_payments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Unpaid</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['unpaid_bookings'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-fire text-orange-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Urgent</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['urgent_issues'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Pending Amount</p>
                        <p class="text-lg font-semibold text-gray-900">TSh {{ number_format($stats['total_pending_amount']) }}</p>
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
                               placeholder="Search payment issues..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Issue Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issue Type</label>
                    <select wire:model.live="issueTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Issues</option>
                        <option value="failed">Failed Payments</option>
                        <option value="disputed">Disputed Payments</option>
                        <option value="unpaid">Unpaid Bookings</option>
                    </select>
                </div>

                <!-- Sort Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select wire:model.live="sortField" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="updated_at">Last Updated</option>
                        <option value="created_at">Date Created</option>
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
                        Showing {{ $bookings->firstItem() ?? 0 }}-{{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} payment issues
                    </span>
                </div>
            </div>
        </div>

        <!-- Payment Issues List -->
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
                                Issue Type & Booking
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event & Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Issue Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($bookings as $booking)
                            @php 
                                $severity = $this->getIssueSeverity($booking);
                                $issueColor = $this->getIssueTypeColor($booking->payment_status);
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $severity === 'critical' ? 'bg-red-50' : ($severity === 'urgent' ? 'bg-orange-50' : '') }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" 
                                           wire:model.live="selectedBookings" 
                                           value="{{ $booking->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $issueColor }}">
                                            <i class="{{ $this->getIssueTypeIcon($booking->payment_status) }} mr-1"></i>
                                            {{ ucfirst($booking->payment_status) }}
                                        </span>
                                        <div class="text-sm font-medium text-gray-900">#{{ $booking->id }}</div>
                                        @if($severity === 'critical')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Critical
                                            </span>
                                        @elseif($severity === 'urgent')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-clock mr-1"></i>Urgent
                                            </span>
                                        @endif
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
                                        <div class="text-lg font-bold text-green-600">TSh {{ number_format($booking->total_amount) }}</div>
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
                                        <div class="font-medium text-gray-900 mb-1">
                                            {{ $this->getIssueDescription($booking->payment_status) }}
                                        </div>
                                        <div class="text-gray-500">
                                            Last updated: {{ $booking->updated_at->diffForHumans() }}
                                        </div>
                                        @if($booking->payments->count() > 0)
                                            <div class="text-gray-500 mt-1">
                                                {{ $booking->payments->count() }} payment attempt(s)
                                            </div>
                                        @endif
                                        @if($booking->payment_status === 'failed')
                                            <div class="text-red-500 text-xs mt-1">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Payment processing failed
                                            </div>
                                        @elseif($booking->payment_status === 'disputed')
                                            <div class="text-purple-500 text-xs mt-1">
                                                <i class="fas fa-gavel mr-1"></i>
                                                Customer dispute filed
                                            </div>
                                        @elseif($booking->payment_status === 'unpaid')
                                            <div class="text-yellow-600 text-xs mt-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                Payment not completed
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm">
                                        @if($booking->getDaysUntilEvent() >= 0)
                                            <div class="font-medium {{ $severity === 'critical' ? 'text-red-600' : ($severity === 'urgent' ? 'text-orange-600' : 'text-gray-900') }}">
                                                {{ $booking->getDaysUntilEvent() === 0 ? 'Today' : ($booking->getDaysUntilEvent() === 1 ? 'Tomorrow' : 'In ' . $booking->getDaysUntilEvent() . ' days') }}
                                            </div>
                                        @else
                                            <div class="font-medium text-gray-500">
                                                {{ abs($booking->getDaysUntilEvent()) }} days ago
                                            </div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $booking->event_date->format('M d, Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <button wire:click="quickMarkPaid({{ $booking->id }})" 
                                                    class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check mr-1"></i>
                                                Mark Paid
                                            </button>
                                            @if($booking->payment_status !== 'disputed')
                                                <button wire:click="quickRetryPayment({{ $booking->id }})" 
                                                        class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                                                    <i class="fas fa-redo mr-1"></i>
                                                    Retry
                                                </button>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            @if($booking->payment_status === 'failed')
                                                <button wire:click="openResolutionModal({{ $booking->id }}, 'refund')" 
                                                        class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition-colors">
                                                    <i class="fas fa-undo mr-1"></i>
                                                    Refund
                                                </button>
                                            @elseif($booking->payment_status === 'disputed')
                                                <button wire:click="openResolutionModal({{ $booking->id }}, 'dispute_resolved')" 
                                                        class="inline-flex items-center px-3 py-1 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition-colors">
                                                    <i class="fas fa-gavel mr-1"></i>
                                                    Resolve
                                                </button>
                                            @endif
                                            
                                            <a href="{{ route('admin.bookings.show', $booking) }}" 
                                               class="inline-flex items-center px-3 py-1 border border-gray-300 text-gray-700 text-xs rounded-lg hover:bg-gray-50 transition-colors">
                                                <i class="fas fa-eye mr-1"></i>
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-check-circle text-4xl text-green-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No payment issues!</h3>
                                        <p class="text-gray-500 mb-4">All payments are processed successfully.</p>
                                        @if($search || $issueTypeFilter)
                                            <button wire:click="clearFilters" class="text-blue-600 hover:text-blue-800">
                                                Clear filters to see all payment issues
                                            </button>
                                        @else
                                            <a href="{{ route('booking.management') }}" class="text-blue-600 hover:text-blue-800">
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

    <!-- Payment Resolution Modal -->
    @if($showResolutionModal && $resolvingBooking)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeResolutionModal">
            <div class="relative top-20 mx-auto p-5 border max-w-2xl shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ ucfirst(str_replace('_', ' ', $resolutionAction)) }} - Booking #{{ $resolvingBooking->id }}
                    </h3>
                    
                    <!-- Payment Issue Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Issue Details -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                                    Payment Issue
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <strong>Issue Type:</strong> 
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $this->getIssueTypeColor($resolvingBooking->payment_status) }}">
                                            {{ ucfirst($resolvingBooking->payment_status) }}
                                        </span>
                                    </div>
                                    <div><strong>Amount:</strong> TSh {{ number_format($resolvingBooking->total_amount) }}</div>
                                    <div><strong>Customer:</strong> {{ $resolvingBooking->user->name }}</div>
                                    <div><strong>Event Date:</strong> {{ $resolvingBooking->event_date->format('M d, Y') }}</div>
                                </div>
                            </div>

                            <!-- Payment History -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-history mr-2 text-blue-500"></i>
                                    Payment History
                                </h4>
                                @if($resolvingBooking->payments->count() > 0)
                                    <div class="space-y-2 text-sm max-h-24 overflow-y-auto">
                                        @foreach($resolvingBooking->payments->take(3) as $payment)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">{{ $payment->created_at->format('M d, H:i') }}</span>
                                                <span class="font-medium {{ $payment->status === 'completed' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No payment attempts recorded</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Resolution Form -->
                    <form wire:submit.prevent="resolvePaymentIssue">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Resolution Notes *
                                </label>
                                <textarea wire:model="resolutionNote" 
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('resolutionNote') border-red-300 @enderror"
                                          placeholder="Describe the resolution action and any relevant details..."
                                          required></textarea>
                                @error('resolutionNote')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($resolutionAction === 'refund')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Refund Amount (TSh) *
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                               wire:model="refundAmount" 
                                               step="0.01"
                                               min="0"
                                               max="{{ $resolvingBooking->total_amount }}"
                                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('refundAmount') border-red-300 @enderror"
                                               placeholder="0.00">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                            <span class="text-gray-500">TSh</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Maximum refund: TSh {{ number_format($resolvingBooking->total_amount) }}
                                    </p>
                                    @error('refundAmount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" 
                                    wire:click="closeResolutionModal"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 rounded-lg text-white font-medium
                                           {{ $resolutionAction === 'mark_paid' ? 'bg-green-600 hover:bg-green-700' : 
                                              ($resolutionAction === 'refund' ? 'bg-red-600 hover:bg-red-700' : 
                                               ($resolutionAction === 'dispute_resolved' ? 'bg-purple-600 hover:bg-purple-700' : 'bg-blue-600 hover:bg-blue-700')) }}">
                                <i class="fas fa-{{ $resolutionAction === 'mark_paid' ? 'check' : ($resolutionAction === 'refund' ? 'undo' : ($resolutionAction === 'dispute_resolved' ? 'gavel' : 'redo')) }} mr-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $resolutionAction)) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

  
</div>


</div>
