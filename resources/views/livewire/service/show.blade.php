<div>
<div>
    @section('title', 'Service Details - ' . $service->name)

  
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4">
                <div class="h-16 w-16 flex-shrink-0">
                    @if($service->hasImages())
                        <img src="{{ $service->getFirstImage() }}" 
                             alt="{{ $service->name }}" 
                             class="h-16 w-16 rounded-lg object-cover">
                    @else
                        <div class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $service->name }}</h1>
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->getStatusColor($service->status) }}">
                            <div class="w-2 h-2 bg-current rounded-full mr-2 opacity-75"></div>
                            {{ ucfirst($service->status) }}
                        </span>
                        @if($service->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $service->category->name }}
                            </span>
                        @endif
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ $service->location_name }}
                        </span>
                    </div>
                    <p class="mt-2 text-gray-600">{{ $service->description }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Service
                </a>
                
                <!-- Status Actions -->
                @foreach($this->getStatusActions($service->status) as $status => $label)
                    <button wire:click="openStatusModal('{{ $status }}')"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium 
                                   {{ $status === 'active' ? 'bg-green-600 hover:bg-green-700 text-white' : 
                                      ($status === 'rejected' ? 'bg-red-600 hover:bg-red-700 text-white' : 
                                       'bg-yellow-600 hover:bg-yellow-700 text-white') }}">
                        <i class="fas fa-{{ $status === 'active' ? 'check' : ($status === 'rejected' ? 'times' : 'pause') }} mr-2"></i>
                        {{ $label }}
                    </button>
                @endforeach

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                        <button wire:click="contactProvider" 
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-envelope mr-2"></i>Contact Provider
                        </button>
                        <button wire:click="deleteService" 
                                wire:confirm="Are you sure you want to delete this service? This action cannot be undone."
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                            <i class="fas fa-trash mr-2"></i>Delete Service
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $analyticsData['total_bookings'] }}</p>
                        @php
                            $growth = $this->getGrowthPercentage($analyticsData['this_month_bookings'], $analyticsData['last_month_bookings']);
                        @endphp
                        @if($growth != 0)
                            <p class="text-xs {{ $growth > 0 ? 'text-green-600' : 'text-red-600' }}">
                                <i class="fas fa-arrow-{{ $growth > 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ abs($growth) }}% from last month
                            </p>
                        @endif
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
                        <p class="text-2xl font-bold text-gray-900">TSh {{ number_format($analyticsData['total_revenue']) }}</p>
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
                        <div class="flex items-baseline">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($analyticsData['average_rating'], 1) }}</p>
                            <p class="text-sm text-gray-500 ml-1">/5.0</p>
                        </div>
                        <div class="flex items-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-xs {{ $i <= $analyticsData['average_rating'] ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-comments text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Reviews</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $analyticsData['total_reviews'] }}</p>
                    </div>
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
                        Provider Info
                    </button>
                    <button wire:click="setActiveTab('gallery')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'gallery' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Gallery
                    </button>
                    <button wire:click="setActiveTab('reviews')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'reviews' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Reviews ({{ $analyticsData['total_reviews'] }})
                    </button>
                    <button wire:click="setActiveTab('bookings')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'bookings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Bookings ({{ $analyticsData['total_bookings'] }})
                    </button>
                    <button wire:click="setActiveTab('analytics')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'analytics' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Analytics
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Overview Tab -->
                @if($activeTab === 'overview')
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Service Details -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Service Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pricing</dt>
                                    <dd class="text-sm text-gray-900">
                                        TSh {{ number_format($service->price) }}
                                        <span class="text-gray-500">
                                            ({{ $service->pricing_description }})
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="text-sm text-gray-900">{{ $service->category?->name ?? 'Uncategorized' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="text-sm text-gray-900">{{ $service->location_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Coordinates</dt>
                                    <dd class="text-sm text-gray-900">{{ $service->latitude }}, {{ $service->longitude }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="text-sm text-gray-900">{{ $service->created_at->format('M d, Y \a\t H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $service->updated_at->format('M d, Y \a\t H:i') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Quick Stats -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-600">This Month's Bookings</span>
                                    <span class="text-lg font-bold text-blue-600">{{ $analyticsData['this_month_bookings'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-600">Last Month's Bookings</span>
                                    <span class="text-lg font-bold text-gray-600">{{ $analyticsData['last_month_bookings'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-600">Average per Booking</span>
                                    <span class="text-lg font-bold text-green-600">
                                        TSh {{ $analyticsData['total_bookings'] > 0 ? number_format($analyticsData['total_revenue'] / $analyticsData['total_bookings']) : '0' }}
                                    </span>
                                </div>
                            </div>
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
                                <p class="text-gray-600">{{ $service->user->email }}</p>
                                <div class="mt-4 space-y-2">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-500 w-20">Role:</span>
                                        <span class="text-sm text-gray-900">{{ ucfirst($service->user->role) }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-500 w-20">Joined:</span>
                                        <span class="text-sm text-gray-900">{{ $service->user->created_at->format('M d, Y') }}</span>
                                    </div>
                                    @if($service->user->email_verified_at)
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-500 w-20">Status:</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Email Verified
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

                <!-- Gallery Tab -->
                @if($activeTab === 'gallery')
                    @if($service->hasImages())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($service->gallery as $image)
                                <div class="aspect-square">
                                    <img src="{{ $image }}" 
                                         alt="Service gallery image" 
                                         class="w-full h-full object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                         onclick="openImageModal('{{ $image }}')">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-images text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Images Available</h3>
                            <p class="text-gray-500">This service doesn't have any gallery images yet.</p>
                        </div>
                    @endif
                @endif

                <!-- Reviews Tab -->
                @if($activeTab === 'reviews')
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
                            <p class="text-gray-500">This service hasn't received any reviews yet.</p>
                        </div>
                    @endif
                @endif

                <!-- Bookings Tab -->
                @if($activeTab === 'bookings')
                    @if($bookings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bookings as $bookingItem)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                                        <span class="text-gray-600 font-medium text-sm">
                                                            {{ strtoupper(substr($bookingItem->booking->user->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $bookingItem->booking->user->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $bookingItem->booking->user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $bookingItem->booking->event_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $bookingItem->quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                TSh {{ number_format($bookingItem->price) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                       {{ $bookingItem->booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                          ($bookingItem->booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                           'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($bookingItem->booking->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $bookingItem->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $bookings->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Bookings Yet</h3>
                            <p class="text-gray-500">This service hasn't been booked yet.</p>
                        </div>
                    @endif
                @endif

                <!-- Analytics Tab -->
                @if($activeTab === 'analytics')
                    <div class="space-y-6">
                        <!-- Revenue Chart -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Trend (Last 6 Months)</h3>
                            <div class="h-64 flex items-end justify-between space-x-2">
                                @foreach($revenueData as $data)
                                    @php
                                        $maxRevenue = collect($revenueData)->max('revenue');
                                        $height = $maxRevenue > 0 ? ($data['revenue'] / $maxRevenue) * 100 : 0;
                                    @endphp
                                    <div class="flex flex-col items-center flex-1">
                                        <div class="w-full bg-blue-500 rounded-t hover:bg-blue-600 transition-colors relative group"
                                             style="height: {{ $height }}%">
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                                TSh {{ number_format($data['revenue']) }}
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-600 mt-2 text-center">{{ $data['month'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Additional Analytics -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Performance Metrics -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Metrics</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Conversion Rate</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $analyticsData['total_reviews'] > 0 ? number_format(($analyticsData['total_bookings'] / $analyticsData['total_reviews']) * 100, 1) : 0 }}%
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Repeat Customer Rate</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $analyticsData['total_bookings'] > 0 ? number_format((collect($bookings->items())->groupBy('booking.user_id')->count() / $analyticsData['total_bookings']) * 100, 1) : 0 }}%
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Average Booking Value</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            TSh {{ $analyticsData['total_bookings'] > 0 ? number_format($analyticsData['total_revenue'] / $analyticsData['total_bookings']) : '0' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Rating Distribution -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Rating Distribution</h3>
                                @php
                                    $ratingCounts = [];
                                    for($i = 5; $i >= 1; $i--) {
                                        $ratingCounts[$i] = $service->reviews()->where('rating', $i)->count();
                                    }
                                    $maxCount = max(array_values($ratingCounts)) ?: 1;
                                @endphp
                                <div class="space-y-2">
                                    @foreach($ratingCounts as $rating => $count)
                                        <div class="flex items-center">
                                            <span class="text-sm w-6">{{ $rating }}</span>
                                            <i class="fas fa-star text-yellow-400 text-sm mx-2"></i>
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div class="bg-yellow-400 h-2 rounded-full" 
                                                     style="width: {{ ($count / $maxCount) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 ml-2 w-8">{{ $count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    @if($showStatusModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeStatusModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Change Status to {{ ucfirst($newStatus) }}
                    </h3>
                    <form wire:submit.prevent="changeStatus">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for status change *
                            </label>
                            <textarea wire:model="statusChangeReason" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('statusChangeReason') border-red-300 @enderror"
                                      placeholder="Please provide a reason for this status change..."
                                      required></textarea>
                            @error('statusChangeReason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" 
                                    wire:click="closeStatusModal"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="max-w-4xl max-h-full p-4">
            <img id="modalImage" src="" alt="Gallery image" class="max-w-full max-h-full object-contain rounded-lg">
        </div>
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
    </div>

    @push('scripts')
    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
    @endpush
</div>



</div>
