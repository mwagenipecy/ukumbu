<div>
    @section('title', 'All Services')

    @section('content')
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
                <h1 class="text-2xl font-bold text-gray-900">All Services</h1>
                <p class="mt-1 text-sm text-gray-500">Manage all registered services on the platform</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                @if(count($selectedServices) > 0)
                    <button wire:click="toggleBulkModal" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-tasks mr-2"></i>
                        Bulk Actions ({{ count($selectedServices) }})
                    </button>
                @endif
                <a href="{{ route('admin.services.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Service
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total Services</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Active</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['active'] }}</p>
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
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-pause-circle text-orange-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Suspended</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['suspended'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Rejected</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['rejected'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Services</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search by name, location, provider..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select wire:model.live="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <!-- Pricing Model Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pricing Model</label>
                    <select wire:model.live="pricingModelFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Models</option>
                        <option value="flat">Flat Rate</option>
                        <option value="hourly">Per Hour</option>
                        <option value="per_guest">Per Guest</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <button wire:click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <button wire:click="clearFilters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </button>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">
                        Showing {{ $services->firstItem() ?? 0 }}-{{ $services->lastItem() ?? 0 }} of {{ $services->total() }} services
                    </span>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-700">Show:</label>
                        <select wire:model.live="perPage" class="px-3 py-1 border border-gray-300 rounded text-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Table -->
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
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('name')">
                                    <span>Service</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('user_id')">
                                    <span>Provider</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('price')">
                                    <span>Price</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Location
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1 cursor-pointer" wire:click="sortBy('status')">
                                    <span>Status</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($services as $service)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" 
                                           wire:model.live="selectedServices" 
                                           value="{{ $service->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 flex-shrink-0">
                                        @php
    $gallery = is_string($service->gallery) 
        ? json_decode($service->gallery, true) 
        : $service->gallery;
@endphp

@if($gallery && count($gallery) > 0)
    <img class="h-12 w-12 rounded-lg object-cover" 
         src="{{ $gallery[0] }}" 
         alt="Service">
@else
    <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
        <i class="fas fa-image text-gray-400"></i>
    </div>
@endif



                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($service->description, 30) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 {{ $this->getUserAvatarColor($service->user->id) }} rounded-full flex items-center justify-center mr-3">
                                            <span class="font-medium text-sm">{{ $this->getUserInitials($service->user->name) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $service->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $service->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getCategoryColor($service->category?->name ?? 'Other') }}">
                                        {{ $service->category?->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">TSh {{ number_format($service->price) }}</div>
                                    <div class="text-sm text-gray-500">
                                        @switch($service->pricing_model)
                                            @case('flat')
                                                Flat rate
                                                @break
                                            @case('hourly')
                                                Per hour
                                                @break
                                            @case('per_guest')
                                                Per guest
                                                @break
                                            @default
                                                {{ $service->pricing_model }}
                                        @endswitch
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $service->location_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($service->status) }}">
                                        <div class="w-1.5 h-1.5 bg-current rounded-full mr-1 opacity-75"></div>
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('view.service.details',$service->id) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('service.form.edit',$service->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="deleteService({{ $service->id }})" 
                                                wire:confirm="Are you sure you want to delete '{{ $service->name }}'? This action cannot be undone."
                                                class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" 
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                                @if($service->status === 'pending')
                                                    <button wire:click="approveService({{ $service->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        <i class="fas fa-check mr-2 text-green-500"></i>Reactivate Service
                                                    </button>
                                                @endif
                                                <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-chart-bar mr-2"></i>View Analytics
                                                </a>
                                                <a href="mailto:{{ $service->user->email }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-envelope mr-2"></i>Contact Provider
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No services found</h3>
                                        <p class="text-gray-500 mb-4">No services match your current filters.</p>
                                        @if($search || $categoryFilter || $statusFilter || $pricingModelFilter)
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
            @if($services->hasPages())
                <div class="bg-white px-6 py-3 border-t border-gray-200">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions Modal -->
    @if($showBulkModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="toggleBulkModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions</h3>
                    <p class="text-sm text-gray-500 mb-6">{{ count($selectedServices) }} service(s) selected</p>
                    <div class="space-y-3">
                        <button wire:click="bulkApprove" 
                                wire:confirm="Are you sure you want to approve {{ count($selectedServices) }} selected services?"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Approve Selected
                        </button>
                        <button wire:click="bulkReject"
                                wire:confirm="Are you sure you want to reject {{ count($selectedServices) }} selected services?"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>Reject Selected
                        </button>
                        <button wire:click="bulkSuspend"
                                wire:confirm="Are you sure you want to suspend {{ count($selectedServices) }} selected services?"
                                class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-pause mr-2"></i>Suspend Selected
                        </button>
                        <button wire:click="toggleBulkModal" 
                                class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
    @endpush
    @endsection
</div>