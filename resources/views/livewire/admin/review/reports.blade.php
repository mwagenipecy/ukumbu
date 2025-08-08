<div>

<div>
    @section('title', 'Reviews Analytics & Reports')

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reviews Analytics & Reports</h1>
                <p class="mt-1 text-sm text-gray-500">Comprehensive insights and analytics on user reviews</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <button wire:click="exportReport('csv')" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Export CSV
                </button>
                <button wire:click="exportReport('pdf')" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Date Range Selector -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select wire:model.live="dateRange" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 3 months</option>
                        <option value="365">Last year</option>
                        <option value="custom">Custom range</option>
                    </select>
                </div>
                
                @if($dateRange === 'custom')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" 
                               wire:model.live="customStartDate" 
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" 
                               wire:model.live="customEndDate" 
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                @endif
                
                <div class="text-sm text-gray-600">
                    <span class="font-medium">Period:</span> {{ $customStartDate }} to {{ $customEndDate }}
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6">
                    <button wire:click="setActiveTab('overview')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-chart-bar mr-2"></i>Overview
                    </button>
                    <button wire:click="setActiveTab('ratings')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'ratings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-star mr-2"></i>Rating Analysis
                    </button>
                    <button wire:click="setActiveTab('services')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'services' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-concierge-bell mr-2"></i>Service Performance
                    </button>
                    <button wire:click="setActiveTab('users')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'users' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-users mr-2"></i>User Insights
                    </button>
                    <button wire:click="setActiveTab('trends')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'trends' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-chart-line mr-2"></i>Trends
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Overview Tab -->
                @if($activeTab === 'overview' && isset($reportData['overview']))
                    @php $data = $reportData['overview']; @endphp
                    
                    <!-- Key Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                            <div class="flex items-center">
                                <i class="fas fa-comments text-2xl opacity-80"></i>
                                <div class="ml-4">
                                    <p class="text-blue-100">Total Reviews</p>
                                    <p class="text-3xl font-bold">{{ number_format($data['total_reviews']) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                            <div class="flex items-center">
                                <i class="fas fa-star text-2xl opacity-80"></i>
                                <div class="ml-4">
                                    <p class="text-green-100">Average Rating</p>
                                    <p class="text-3xl font-bold">{{ number_format($data['average_rating'], 1) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-2xl opacity-80"></i>
                                <div class="ml-4">
                                    <p class="text-purple-100">Approval Rate</p>
                                    <p class="text-3xl font-bold">{{ number_format($data['approval_rate'], 1) }}%</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white">
                            <div class="flex items-center">
                                <i class="fas fa-flag text-2xl opacity-80"></i>
                                <div class="ml-4">
                                    <p class="text-orange-100">Flagged Reviews</p>
                                    <p class="text-3xl font-bold">{{ $data['flagged_reviews'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Top Rated Services -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                                Top Rated Services
                            </h3>
                            <div class="space-y-3">
                                @forelse($data['top_rated_services'] as $service)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $service['name'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $service['reviews'] }} reviews</p>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="flex text-yellow-400 mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $service['rating'] ? '' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="font-bold text-gray-900">{{ $service['rating'] }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No services found</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Top Rated Venues -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-building text-blue-500 mr-2"></i>
                                Top Rated Venues
                            </h3>
                            <div class="space-y-3">
                                @forelse($data['top_rated_venues'] as $venue)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $venue['name'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $venue['reviews'] }} reviews</p>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="flex text-yellow-400 mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $venue['rating'] ? '' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="font-bold text-gray-900">{{ $venue['rating'] }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No venues found</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Recent Reviews -->
                    <div class="bg-white border rounded-lg p-6 mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-clock text-gray-500 mr-2"></i>
                            Recent Reviews
                        </h3>
                        <div class="space-y-4">
                            @forelse($data['recent_reviews'] as $review)
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">
                                            {{ strtoupper(substr($review->user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="font-medium text-gray-900">{{ $review->user->name }}</span>
                                            <div class="flex text-yellow-400">
                                                @for($i = 1; $i <= $review->rating; $i++)
                                                    <i class="fas fa-star text-xs"></i>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 mb-1">{{ $review->reviewable?->name }}</p>
                                        @if($review->comment)
                                            <p class="text-sm text-gray-600">"{{ Str::limit($review->comment, 100) }}"</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No recent reviews</p>
                            @endforelse
                        </div>
                    </div>
                @endif

                <!-- Rating Analysis Tab -->
                @if($activeTab === 'ratings' && isset($reportData['ratings']))
                    @php $data = $reportData['ratings']; @endphp
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Rating Distribution -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rating Distribution</h3>
                            <div class="space-y-3">
                                @php $totalRatings = array_sum($data['distribution']); @endphp
                                @foreach($data['distribution'] as $rating => $count)
                                    @php $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0; @endphp
                                    <div class="flex items-center">
                                        <div class="flex items-center w-16">
                                            <span class="text-sm font-medium w-2">{{ $rating }}</span>
                                            <i class="fas fa-star text-yellow-400 text-sm ml-1"></i>
                                        </div>
                                        <div class="flex-1 mx-4">
                                            <div class="w-full bg-gray-200 rounded-full h-4">
                                                <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-4 rounded-full transition-all duration-300" 
                                                     style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-600 w-20 text-right">
                                            <span class="font-medium">{{ $count }}</span>
                                            <span class="text-gray-400 ml-1">({{ number_format($percentage, 1) }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Rating Comparison -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Period Comparison</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                    <div>
                                        <p class="text-sm text-blue-600 font-medium">Current Period</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $data['comparison']['previous'] }}</p>
                                    </div>
                                    <i class="fas fa-history text-gray-400 text-2xl"></i>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 {{ $data['comparison']['change_percentage'] >= 0 ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium {{ $data['comparison']['change_percentage'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            Change
                                        </p>
                                        <p class="text-2xl font-bold {{ $data['comparison']['change_percentage'] >= 0 ? 'text-green-900' : 'text-red-900' }}">
                                            {{ $data['comparison']['change_percentage'] >= 0 ? '+' : '' }}{{ $data['comparison']['change_percentage'] }}%
                                        </p>
                                    </div>
                                    <i class="fas fa-{{ $data['comparison']['change_percentage'] >= 0 ? 'arrow-up text-green-400' : 'arrow-down text-red-400' }} text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ratings by Category -->
                    @if(isset($data['by_category']) && count($data['by_category']) > 0)
                        <div class="bg-white border rounded-lg p-6 mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ratings by Category</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Rating</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Reviews</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating Visualization</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($data['by_category'] as $category)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $category->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <span class="text-lg font-bold text-gray-900 mr-2">{{ number_format($category->avg_rating, 2) }}</span>
                                                        <div class="flex text-yellow-400">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star text-sm {{ $i <= $category->avg_rating ? '' : 'text-gray-300' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $category->review_count }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ ($category->avg_rating / 5) * 100 }}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Service Performance Tab -->
                @if($activeTab === 'services' && isset($reportData['services']))
                    @php $data = $reportData['services']; @endphp
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Most Reviewed Services -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-fire text-orange-500 mr-2"></i>
                                Most Reviewed
                            </h3>
                            <div class="space-y-3">
                                @forelse($data['most_reviewed'] as $service)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900 text-sm">{{ Str::limit($service['name'], 25) }}</p>
                                            <div class="flex items-center mt-1">
                                                <div class="flex text-yellow-400 mr-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star text-xs {{ $i <= $service['avg_rating'] ? '' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-600">{{ number_format($service['avg_rating'], 1) }}</span>
                                            </div>
                                        </div>
                                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2 py-1 rounded-full">
                                            {{ $service['reviews'] }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4 text-sm">No data available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Highest Rated Services -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-star text-yellow-500 mr-2"></i>
                                Highest Rated
                            </h3>
                            <div class="space-y-3">
                                @forelse($data['highest_rated'] as $service)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900 text-sm">{{ Str::limit($service['name'], 25) }}</p>
                                            <p class="text-xs text-gray-600">{{ $service['reviews'] }} reviews</p>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="flex text-yellow-400 mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-xs {{ $i <= $service['rating'] ? '' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="font-bold text-gray-900 text-sm">{{ $service['rating'] }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4 text-sm">No data available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Needs Attention -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                Needs Attention
                            </h3>
                            <div class="space-y-3">
                                @forelse($data['lowest_rated'] as $service)
                                    <div class="flex items-center justify-between p-3 {{ $service['needs_attention'] ? 'bg-red-50 border border-red-200' : 'bg-gray-50' }} rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium {{ $service['needs_attention'] ? 'text-red-900' : 'text-gray-900' }} text-sm">
                                                {{ Str::limit($service['name'], 25) }}
                                            </p>
                                            <p class="text-xs {{ $service['needs_attention'] ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ $service['reviews'] }} reviews
                                            </p>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="flex text-yellow-400 mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-xs {{ $i <= $service['rating'] ? '' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="font-bold {{ $service['needs_attention'] ? 'text-red-900' : 'text-gray-900' }} text-sm">
                                                {{ $service['rating'] }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4 text-sm">All services performing well!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Reviews by Provider -->
                    @if(isset($data['by_provider']) && count($data['by_provider']) > 0)
                        <div class="bg-white border rounded-lg p-6 mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Reviews by Service Provider</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Rating</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Reviews</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($data['by_provider'] as $provider)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $provider->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $provider->email }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <span class="text-sm font-bold text-gray-900 mr-2">{{ number_format($provider->avg_rating, 2) }}</span>
                                                        <div class="flex text-yellow-400">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star text-xs {{ $i <= $provider->avg_rating ? '' : 'text-gray-300' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $provider->review_count }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($provider->avg_rating >= 4.5)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Excellent
                                                        </span>
                                                    @elseif($provider->avg_rating >= 4.0)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            Good
                                                        </span>
                                                    @elseif($provider->avg_rating >= 3.0)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Average
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Needs Improvement
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- User Insights Tab -->
                @if($activeTab === 'users' && isset($reportData['users']))
                    @php $data = $reportData['users']; @endphp
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Most Active Reviewers -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-trophy text-gold-500 mr-2"></i>
                                Most Active Reviewers
                            </h3>
                            <div class="space-y-3">
                                @forelse($data['most_active_reviewers'] as $reviewer)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-blue-600 font-medium text-xs">
                                                    {{ strtoupper(substr($reviewer['name'], 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">{{ $reviewer['name'] }}</p>
                                                <p class="text-xs text-gray-600">Avg rating given: {{ $reviewer['avg_rating_given'] }}</p>
                                            </div>
                                        </div>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">
                                            {{ $reviewer['reviews'] }} reviews
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No active reviewers found</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Reviewer Satisfaction -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-smile text-green-500 mr-2"></i>
                                Customer Satisfaction
                            </h3>
                            @php $satisfaction = $data['reviewer_satisfaction']; @endphp
                            <div class="space-y-4">
                                <div class="text-center mb-6">
                                    <div class="text-4xl font-bold text-green-600 mb-2">{{ $satisfaction['satisfaction_rate'] }}%</div>
                                    <p class="text-gray-600">Overall Satisfaction Rate</p>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">😊 Satisfied (4-5 stars)</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                @php 
                                                    $total = $satisfaction['satisfied'] + $satisfaction['neutral'] + $satisfaction['dissatisfied'];
                                                    $satisfiedPerc = $total > 0 ? ($satisfaction['satisfied'] / $total) * 100 : 0;
                                                @endphp
                                                <div class="bg-green-400 h-2 rounded-full" style="width: {{ $satisfiedPerc }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium">{{ $satisfaction['satisfied'] }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">😐 Neutral (3 stars)</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                @php $neutralPerc = $total > 0 ? ($satisfaction['neutral'] / $total) * 100 : 0; @endphp
                                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $neutralPerc }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium">{{ $satisfaction['neutral'] }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">😞 Dissatisfied (1-2 stars)</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                @php $dissatisfiedPerc = $total > 0 ? ($satisfaction['dissatisfied'] / $total) * 100 : 0; @endphp
                                                <div class="bg-red-400 h-2 rounded-full" style="width: {{ $dissatisfiedPerc }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium">{{ $satisfaction['dissatisfied'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- New Reviewers -->
                    @if(isset($data['new_reviewers']) && count($data['new_reviewers']) > 0)
                        <div class="bg-white border rounded-lg p-6 mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                                New Reviewers This Period
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($data['new_reviewers'] as $reviewer)
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-green-600 font-medium text-sm">
                                                {{ strtoupper(substr($reviewer->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $reviewer->name }}</p>
                                            <p class="text-xs text-gray-600">{{ $reviewer->reviews_count }} reviews</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Trends Tab -->
                @if($activeTab === 'trends' && isset($reportData['trends']))
                    @php $data = $reportData['trends']; @endphp
                    
                    <!-- Growth Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white border rounded-lg p-6 text-center">
                            <i class="fas fa-chart-line text-3xl text-blue-500 mb-3"></i>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($data['growth_metrics']['avg_per_day'], 1) }}</p>
                            <p class="text-sm text-gray-600">Avg Reviews/Day</p>
                        </div>
                        
                        <div class="bg-white border rounded-lg p-6 text-center">
                            <i class="fas fa-trending-up text-3xl text-green-500 mb-3"></i>
                            <p class="text-lg font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $data['growth_metrics']['growth_trend'])) }}</p>
                            <p class="text-sm text-gray-600">Growth Trend</p>
                        </div>
                        
                        <div class="bg-white border rounded-lg p-6 text-center">
                            <i class="fas fa-calendar-day text-3xl text-purple-500 mb-3"></i>
                            <p class="text-lg font-bold text-gray-900">
                                @if($data['growth_metrics']['peak_day'])
                                    {{ now()->parse($data['growth_metrics']['peak_day']->date)->format('M d') }}
                                @else
                                    N/A
                                @endif
                            </p>
                            <p class="text-sm text-gray-600">Peak Review Day</p>
                        </div>
                        
                        <div class="bg-white border rounded-lg p-6 text-center">
                            <i class="fas fa-percentage text-3xl text-orange-500 mb-3"></i>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $data['monthly_comparison']['change_percentage'] >= 0 ? '+' : '' }}{{ $data['monthly_comparison']['change_percentage'] }}%
                            </p>
                            <p class="text-sm text-gray-600">Monthly Growth</p>
                        </div>
                    </div>

                    <!-- Seasonal Patterns -->
                    @if(isset($data['seasonal_patterns']) && count($data['seasonal_patterns']) > 0)
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                                Monthly Review Patterns (This Year)
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($data['seasonal_patterns'] as $month)
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <p class="font-medium text-gray-900">{{ $month['month'] }}</p>
                                        <p class="text-2xl font-bold text-blue-600">{{ $month['reviews'] }}</p>
                                        <p class="text-sm text-gray-600">{{ $month['avg_rating'] }} avg</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

   
</div>

</div>
