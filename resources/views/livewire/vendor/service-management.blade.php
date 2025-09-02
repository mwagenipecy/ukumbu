<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Services</h1>
                <p class="text-gray-600 mt-1">Manage your event services and offerings</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.services.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Service
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-concierge-bell text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Services</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Active</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <i class="fas fa-pause-circle text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Suspended</h3>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['suspended'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Rejected</h3>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search services, descriptions, locations..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            
            <!-- Category Filter -->
            <div>
                <select wire:model.live="categoryFilter" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div>
                <select wire:model.live="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending Approval</option>
                    <option value="suspended">Suspended</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            
            <!-- Pricing Model Filter -->
            <div>
                <select wire:model.live="pricingModelFilter" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Pricing Models</option>
                    <option value="flat">Flat Rate</option>
                    <option value="hourly">Per Hour</option>
                    <option value="per_guest">Per Guest</option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-700">Show:</span>
                <select wire:model.live="perPage" class="px-3 py-1 border border-gray-300 rounded-md">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-700">per page</span>
            </div>
            
            <button 
                wire:click="clearFilters"
                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
            >
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($services->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button wire:click="sortBy('name')" class="flex items-center hover:text-gray-700">
                                    Service Details
                                    @if($sortField === 'name')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button wire:click="sortBy('pricing')" class="flex items-center hover:text-gray-700">
                                    Pricing
                                    @if($sortField === 'pricing')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button wire:click="sortBy('status')" class="flex items-center hover:text-gray-700">
                                    Status
                                    @if($sortField === 'status')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button wire:click="sortBy('created_at')" class="flex items-center hover:text-gray-700">
                                    Created
                                    @if($sortField === 'created_at')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($services as $service)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            @if($service->image_url)
                                                <img class="h-12 w-12 rounded-lg object-cover" src="{{ $service->image_url }}" alt="">
                                            @else
                                                <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-concierge-bell text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($service->description, 50) }}</div>
                                            @if($service->location_name)
                                                <div class="text-xs text-gray-400 flex items-center mt-1">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $service->location_name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getCategoryColor($service->category->name ?? 'Unknown') }}">
                                        {{ $service->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $this->formatPrice($service->pricing, $service->pricing_model) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="{{ $this->getStatusIcon($service->status) }} mr-2"></i>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($service->status) }}">
                                            {{ ucfirst($service->status) }}
                                        </span>
                                    </div>
                                    @if($service->status === 'rejected' && $service->rejection_reason)
                                        <div class="text-xs text-red-600 mt-1">{{ $service->rejection_reason }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $service->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('view.service.details', $service->id) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <button wire:click="editService({{ $service->id }})"
                                                class="text-green-600 hover:text-green-900 transition-colors"
                                                title="Edit Service">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button wire:click="duplicateService({{ $service->id }})"
                                                class="text-purple-600 hover:text-purple-900 transition-colors"
                                                title="Duplicate Service">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        
                                        <button wire:click="deleteService({{ $service->id }})"
                                                onclick="return confirm('Are you sure you want to delete this service?')"
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Delete Service">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $services->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-concierge-bell text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No services found</h3>
                <p class="text-gray-500 mb-6">
                    @if($search || $categoryFilter || $statusFilter || $pricingModelFilter)
                        No services match your current filters. Try adjusting your search criteria.
                    @else
                        You haven't created any services yet. Start by adding your first service.
                    @endif
                </p>
                <a href="{{ route('admin.services.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Create Your First Service
                </a>
            </div>
        @endif
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
</div>

