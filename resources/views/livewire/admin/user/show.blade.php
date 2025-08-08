<div>
<div>
    @section('title', 'User Details - ' . $user->name)

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-6">
                <div class="h-24 w-24 {{ $this->getUserAvatarColor($user->id) }} rounded-full flex items-center justify-center">
                    <span class="font-bold text-3xl">{{ $this->getUserInitials($user->name) }}</span>
                </div>
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        @if($user->id === auth()->id())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-user mr-1"></i>
                                You
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                    <div class="flex items-center mt-3 space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->getRoleColor($user->role) }}">
                            <i class="{{ $this->getRoleIcon($user->role) }} mr-2"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Email Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Email Unverified
                            </span>
                        @endif
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        <p>Joined {{ $user->created_at->format('M d, Y') }} • Last active {{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <button wire:click="toggleEmailVerification" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-{{ $user->email_verified_at ? 'times' : 'check' }}-circle mr-2"></i>
                    {{ $user->email_verified_at ? 'Remove Verification' : 'Verify Email' }}
                </button>

                <button wire:click="openPasswordModal" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-key mr-2"></i>
                    Reset Password
                </button>

                @if($user->id !== auth()->id())
                    <button wire:click="impersonateUser" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-user-secret mr-2"></i>
                        Impersonate
                    </button>
                @endif

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                        <button wire:click="changeRole('client')" 
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user mr-2"></i>Change to Client
                        </button>
                        <button wire:click="changeRole('vendor')" 
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-store mr-2"></i>Change to Vendor
                        </button>
                        <button wire:click="changeRole('admin')" 
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user-shield mr-2"></i>Change to Admin
                        </button>
                        <hr class="my-1">
                        <a href="mailto:{{ $user->email }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-envelope mr-2"></i>Send Email
                        </a>
                        @if($user->id !== auth()->id())
                            <button wire:click="deleteUser" 
                                    wire:confirm="Are you sure you want to delete this user? This action cannot be undone."
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                <i class="fas fa-trash mr-2"></i>Delete User
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

            @if($user->role === 'vendor')
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-concierge-bell text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Services</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $analyticsData['total_services'] ?? 0 }}</p>
                            <p class="text-xs text-green-600">{{ $analyticsData['active_services'] ?? 0 }} active</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">TSh {{ number_format($analyticsData['total_revenue'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <i class="fas fa-star text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Average Rating</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($analyticsData['average_rating'] ?? 0, 1) }}</p>
                            <p class="text-xs text-gray-500">/5.0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $analyticsData['total_bookings'] ?? 0 }}</p>
                            <p class="text-xs text-blue-600">{{ $analyticsData['this_month_bookings'] ?? 0 }} this month</p>
                        </div>
                    </div>
                </div>
            @elseif($user->role === 'client')
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-calendar text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $analyticsData['total_bookings'] ?? 0 }}</p>
                            <p class="text-xs text-green-600">{{ $analyticsData['confirmed_bookings'] ?? 0 }} confirmed</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-money-bill text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Spent</p>
                            <p class="text-2xl font-bold text-gray-900">TSh {{ number_format($analyticsData['total_spent'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Upcoming Events</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $analyticsData['upcoming_bookings'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-star text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Reviews Given</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $analyticsData['reviews_given'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Avg: {{ number_format($analyticsData['average_rating_given'] ?? 0, 1) }}/5</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-2 lg:col-span-4">
                    <div class="flex items-center justify-center">
                        <div class="p-3 bg-red-100 rounded-lg mr-4">
                            <i class="fas fa-user-shield text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Administrator Access</p>
                            <p class="text-sm text-gray-500">Full system access and management privileges</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6">
                    <button wire:click="setActiveTab('overview')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Overview
                    </button>
                    @if($user->role === 'vendor')
                        <button wire:click="setActiveTab('services')"
                                class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'services' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Services ({{ $analyticsData['total_services'] ?? 0 }})
                        </button>
                    @endif
                    @if($user->role !== 'admin')
                        <button wire:click="setActiveTab('bookings')"
                                class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'bookings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Bookings
                        </button>
                        <button wire:click="setActiveTab('reviews')"
                                class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'reviews' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Reviews
                        </button>
                    @endif
                    <button wire:click="setActiveTab('activity')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'activity' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Activity Log
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Overview Tab -->
                @if($activeTab === 'overview')
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- User Details -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($user->role) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email Verification</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($user->email_verified_at)
                                            <span class="text-green-600">Verified on {{ $user->email_verified_at->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-orange-600">Not verified</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Account Created</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y \a\t H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->updated_at->format('M d, Y \a\t H:i') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Quick Actions -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button wire:click="toggleEmailVerification" 
                                        class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium text-gray-900">
                                        {{ $user->email_verified_at ? 'Remove Email Verification' : 'Verify Email Address' }}
                                    </span>
                                    <i class="fas fa-{{ $user->email_verified_at ? 'times' : 'check' }}-circle text-gray-400"></i>
                                </button>

                                <button wire:click="openPasswordModal" 
                                        class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium text-gray-900">Reset Password</span>
                                    <i class="fas fa-key text-gray-400"></i>
                                </button>

                                @if($user->id !== auth()->id())
                                    <button wire:click="impersonateUser" 
                                            class="w-full flex items-center justify-between p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                        <span class="font-medium text-purple-900">Impersonate User</span>
                                        <i class="fas fa-user-secret text-purple-400"></i>
                                    </button>
                                @endif

                                <a href="mailto:{{ $user->email }}" 
                                   class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium text-gray-900">Send Email</span>
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Services Tab -->
                @if($activeTab === 'services' && $user->role === 'vendor')
                    @if($services->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($services as $service)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($service->description, 50) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $service->category?->name ?? 'Uncategorized' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                TSh {{ number_format($service->price) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                       {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                          ($service->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($service->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $service->bookingItems->count() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.services.show', $service) }}" 
                                                   class="text-blue-600 hover:text-blue-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($services->hasPages())
                            <div class="mt-4">{{ $services->links() }}</div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-concierge-bell text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Services</h3>
                            <p class="text-gray-500">This vendor hasn't created any services yet.</p>
                        </div>
                    @endif
                @endif

                <!-- Bookings Tab -->
                @if($activeTab === 'bookings' && $user->role !== 'admin')
                    @if($bookings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @if($user->role === 'client')
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                                        @else
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                        @endif
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bookings as $booking)
                                        <tr class="hover:bg-gray-50">
                                            @if($user->role === 'client')
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">#{{ $booking->id }}</div>
                                                    <div class="text-sm text-gray-500">{{ $booking->bookingItems->count() }} items</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $booking->venue?->name ?? 'No venue' }}</div>
                                                </td>
                                            @else
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        {{ $booking->bookingItems->first()?->service?->name }}
                                                    </div>
                                                </td>
                                            @endif
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $booking->event_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                TSh {{ number_format($booking->total_amount) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                       {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                          ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $booking->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($bookings->hasPages())
                            <div class="mt-4">{{ $bookings->links() }}</div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Bookings</h3>
                            <p class="text-gray-500">
                                @if($user->role === 'client')
                                    This user hasn't made any bookings yet.
                                @else
                                    No bookings found for this vendor's services.
                                @endif
                            </p>
                        </div>
                    @endif
                @endif

                <!-- Reviews Tab -->
                @if($activeTab === 'reviews' && $user->role !== 'admin')
                    @if($reviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                                <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                    <div class="flex items-start space-x-4">
                                        <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            <span class="text-gray-600 font-medium text-sm">
                                                @if($user->role === 'client')
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                @else
                                                    {{ strtoupper(substr($review->user->name, 0, 2)) }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-medium text-gray-900">
                                                    @if($user->role === 'client')
                                                        Review for {{ $review->reviewable?->name }}
                                                    @else
                                                        Review by {{ $review->user->name }}
                                                    @endif
                                                </h4>
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
                        </div>
                        @if($reviews->hasPages())
                            <div class="mt-6">{{ $reviews->links() }}</div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-star text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Reviews</h3>
                            <p class="text-gray-500">
                                @if($user->role === 'client')
                                    This user hasn't written any reviews yet.
                                @else
                                    No reviews found for this vendor's services.
                                @endif
                            </p>
                        </div>
                    @endif
                @endif

                <!-- Activity Log Tab -->
                @if($activeTab === 'activity')
                    <div class="text-center py-12">
                        <i class="fas fa-list-alt text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Activity Log</h3>
                        <p class="text-gray-500">Activity logging feature coming soon.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Password Reset Modal -->
    @if($showPasswordModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closePasswordModal">
            <div class="relative top-20 mx-auto p-5 border max-w-md shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Reset Password for {{ $user->name }}</h3>
                    <form wire:submit.prevent="resetPassword">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                                <input type="password" 
                                       wire:model="newPassword" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('newPassword') border-red-300 @enderror"
                                       placeholder="Enter new password">
                                @error('newPassword')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                                <input type="password" 
                                       wire:model="confirmPassword" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('confirmPassword') border-red-300 @enderror"
                                       placeholder="Confirm new password">
                                @error('confirmPassword')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" 
                                    wire:click="closePasswordModal"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>

</div>
