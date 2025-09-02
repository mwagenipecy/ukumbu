
<div>
@section('title', 'Venues Management')

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Venues Management</h2>
            <p class="mt-1 text-sm text-gray-600">Manage all venues on the platform</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button 
                wire:click="openCreateModal"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
            >
                <i class="fas fa-plus mr-2"></i>
                Add Venue
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-building text-blue-600"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Total Venues</div>
                    <div class="text-xl font-semibold text-gray-900">{{ $venues->total() }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Active</div>
                    <div class="text-xl font-semibold text-gray-900">
                        {{ \App\Models\Venue::where('status', 'active')->count() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Pending</div>
                    <div class="text-xl font-semibold text-gray-900">
                        {{ \App\Models\Venue::where('status', 'pending')->count() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-pause-circle text-red-600"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Inactive</div>
                    <div class="text-xl font-semibold text-gray-900">
                        {{ \App\Models\Venue::where('status', 'inactive')->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search venues..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                    wire:model.live="statusFilter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select 
                    wire:model.live="categoryFilter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Owner</label>
                <select 
                    wire:model.live="ownerFilter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">All Owners</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Venues Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Venues List</h3>
        </div>

        @if($venues->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Venue
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Owner
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stats
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($venues as $venue)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden mr-3 flex-shrink-0">
                                            @if(!empty($venue->gallery))
                                                <img 
                                                    src="{{ asset('storage/' . $venue->gallery[0]) }}" 
                                                    alt="{{ $venue->name }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @else
                                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                    <i class="fas fa-building text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $venue->name }}</div>
                                            <div class="text-sm text-gray-500 truncate">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ Str::limit($venue->location_name, 30) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $venue->user->name ?? 'No Owner' }}</div>
                                    <div class="text-sm text-gray-500">{{ $venue->user->email ?? 'No Email' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $venue->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    TSh {{ number_format($venue->base_price) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="relative" x-data="{ open: false }">
                                        <button 
                                            @click="open = !open"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer
                                            {{ $venue->status === 'active' ? 'bg-green-100 text-green-800' : 
                                               ($venue->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                'bg-red-100 text-red-800') }}"
                                        >
                                            {{ ucfirst($venue->status) }}
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        
                                        <div x-show="open" @click.away="open = false" 
                                             x-transition
                                             class="absolute z-10 mt-1 w-32 bg-white rounded-md shadow-lg border border-gray-200">
                                            <div class="py-1">
                                                <button wire:click="updateStatus({{ $venue->id }}, 'active')" 
                                                        class="flex items-center w-full px-3 py-2 text-sm text-green-700 hover:bg-green-50">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    Active
                                                </button>
                                                <button wire:click="updateStatus({{ $venue->id }}, 'pending')" 
                                                        class="flex items-center w-full px-3 py-2 text-sm text-yellow-700 hover:bg-yellow-50">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    Pending
                                                </button>
                                                <button wire:click="updateStatus({{ $venue->id }}, 'inactive')" 
                                                        class="flex items-center w-full px-3 py-2 text-sm text-red-700 hover:bg-red-50">
                                                    <i class="fas fa-pause-circle mr-2"></i>
                                                    Inactive
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center space-x-4">
                                        <span title="Bookings" class="flex items-center">
                                            <i class="fas fa-calendar-check text-blue-500 mr-1"></i>
                                            {{ $venue->bookings_count }}
                                        </span>
                                        <span title="Reviews" class="flex items-center">
                                            <i class="fas fa-star text-yellow-500 mr-1"></i>
                                            {{ $venue->reviews_count }}
                                        </span>
                                        <span title="Likes" class="flex items-center">
                                            <i class="fas fa-heart text-red-500 mr-1"></i>
                                            {{ $venue->likes_count }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button 
                                            wire:click="openViewModal({{ $venue->id }})"
                                            class="text-gray-600 hover:text-gray-900 p-1 rounded transition-colors"
                                            title="View Details"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <button 
                                            wire:click="openEditModal({{ $venue->id }})"
                                            class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors"
                                            title="Edit Venue"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button 
                                            onclick="confirmDelete({{ $venue->id }}, '{{ $venue->name }}')"
                                            class="text-red-600 hover:text-red-900 p-1 rounded transition-colors"
                                            title="Delete Venue"
                                        >
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
                {{ $venues->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-building text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No venues found</h3>
                <p class="text-gray-500 mb-6">Get started by adding your first venue.</p>
                <button 
                    wire:click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Add Venue
                </button>
            </div>
        @endif
    </div>

    <!-- Add/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto z-50" wire:click="resetModal">
            <div class="relative top-4 mx-auto p-5 border max-w-4xl shadow-lg rounded-lg bg-white" wire:click.stop>
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-medium text-gray-900">
                        {{ $editingVenueId ? 'Edit Venue' : 'Add New Venue' }}
                    </h3>
                    <button wire:click="resetModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Form -->
                <!-- Validation Errors Summary -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Please fix the following errors:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name *</label>
                                <input 
                                    type="text" 
                                    wire:model="name"
                                    placeholder="Enter venue name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                >
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Price Range -->
                            <div class="space-y-4">
                                <label class="block text-sm font-medium text-gray-700">Price Range (TSh) *</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <input 
                                            type="number" 
                                            wire:model="price_min"
                                            placeholder="Min price"
                                            min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price_min') border-red-500 @enderror"
                                        >
                                        @error('price_min') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <input 
                                            type="number" 
                                            wire:model="price_max"
                                            placeholder="Max price"
                                            min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price_max') border-red-500 @enderror"
                                        >
                                        @error('price_max') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Price Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price Type *</label>
                                <select 
                                    wire:model="price_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price_type') border-red-500 @enderror"
                                >
                                    <option value="per_event">Per Event</option>
                                    <option value="per_hour">Per Hour</option>
                                    <option value="per_day">Per Day</option>
                                </select>
                                @error('price_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                <select 
                                    wire:model="category_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror"
                                >
                                    <option value="">Select Category</option>
                                    @forelse($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @empty
                                        <option value="" disabled>No venue categories available</option>
                                    @endforelse
                                </select>
                                @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Owner *</label>
                                <select 
                                    wire:model="user_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('user_id') border-red-500 @enderror"
                                >
                                    <option value="">Select Owner</option>
                                    @forelse($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @empty
                                        <option value="" disabled>No vendors available</option>
                                    @endforelse
                                </select>
                                @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select 
                                    wire:model="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-gray-900 border-b pb-2">Location & Images</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location Name *</label>
                                <input 
                                    type="text" 
                                    wire:model="location_name"
                                    placeholder="Enter location"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location_name') border-red-500 @enderror"
                                >
                                @error('location_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitude *</label>
                                    <input 
                                        type="number" 
                                        wire:model="latitude"
                                        placeholder="-6.7924"
                                        step="any"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('latitude') border-red-500 @enderror"
                                    >
                                    @error('latitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitude *</label>
                                    <input 
                                        type="number" 
                                        wire:model="longitude"
                                        placeholder="39.2083"
                                        step="any"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('longitude') border-red-500 @enderror"
                                    >
                                    @error('longitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Get Current Location Button -->
                            <div class="flex justify-start space-x-3">
                                @if(!$locationCaptured)
                                    <button type="button" 
                                            wire:click="getCurrentLocation"
                                            @disabled($gettingLocation)
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        @if($gettingLocation)
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Getting Location...
                                        @else
                                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                            Use Current Location
                                        @endif
                                    </button>
                                @else
                                    <div class="flex items-center space-x-3">
                                        <div class="inline-flex items-center px-4 py-2 border border-green-300 bg-green-50 rounded-lg text-sm text-green-700">
                                            <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                            Location Captured Successfully
                                        </div>
                                        <button type="button" 
                                                wire:click="resetLocationCapture"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-redo mr-2"></i>
                                            Re-capture
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @if(!empty($gallery))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach($gallery as $index => $image)
                                            <div class="relative group">
                                                <img src="{{ asset('storage/' . $image) }}" class="w-full h-20 object-cover rounded-lg">
                                                <button 
                                                    type="button"
                                                    wire:click="removeImage({{ $index }})"
                                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs flex items-center justify-center"
                                                >×</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ !empty($gallery) ? 'Add More Images' : 'Upload Images' }}
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="newImages"
                                    multiple
                                    accept="image/*"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                <p class="mt-1 text-xs text-gray-500">Max 2MB per image. Supports JPG, PNG, GIF</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea 
                            wire:model="description"
                            rows="4"
                            placeholder="Enter venue description..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500">Provide a detailed description of the venue</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                        <button 
                            type="button"
                            wire:click="resetModal"
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg disabled:opacity-50 transition-colors"
                        >
                            <span wire:loading.remove>
                                {{ $editingVenueId ? 'Update Venue' : 'Create Venue' }}
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                {{ $editingVenueId ? 'Updating...' : 'Creating...' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- View Modal -->
    @if($showViewModal && $viewingVenue)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto z-50" wire:click="resetModal">
            <div class="relative top-4 mx-auto p-5 border max-w-4xl shadow-lg rounded-lg bg-white" wire:click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-medium text-gray-900">{{ $viewingVenue->name }}</h3>
                    <button wire:click="resetModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Images & Info -->
                    <div>
                        @if(!empty($viewingVenue->gallery))
                            <img src="{{ asset('storage/' . $viewingVenue->gallery[0]) }}" class="w-full h-64 object-cover rounded-lg mb-4">
                            
                            @if(count($viewingVenue->gallery) > 1)
                                <div class="grid grid-cols-4 gap-2 mb-4">
                                    @foreach(array_slice($viewingVenue->gallery, 1, 4) as $image)
                                        <img src="{{ asset('storage/' . $image) }}" class="w-full h-16 object-cover rounded-lg">
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-building text-4xl text-gray-400"></i>
                            </div>
                        @endif

                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Venue Information</h4>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Price:</dt>
                                    <dd class="text-sm font-medium text-gray-900">TSh {{ number_format($viewingVenue->base_price) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Category:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $viewingVenue->category->name ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Owner:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $viewingVenue->user->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Email:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $viewingVenue->user->email }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Location:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $viewingVenue->location_name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Status:</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $viewingVenue->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           ($viewingVenue->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($viewingVenue->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Created:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $viewingVenue->created_at->format('M d, Y') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Stats & Details -->
                    <div>
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Statistics</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $viewingVenue->bookings->count() }}</div>
                                    <div class="text-sm text-blue-600">Total Bookings</div>
                                </div>
                                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $viewingVenue->reviews->count() }}</div>
                                    <div class="text-sm text-yellow-600">Reviews</div>
                                </div>
                                <div class="bg-red-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-red-600">{{ $viewingVenue->likes->count() }}</div>
                                    <div class="text-sm text-red-600">Likes</div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $viewingVenue->created_at->format('M Y') }}</div>
                                    <div class="text-sm text-green-600">Joined</div>
                                </div>
                            </div>
                        </div>

                        @if($viewingVenue->description)
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900 mb-3">Description</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $viewingVenue->description }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Location Coordinates</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm text-gray-600">Latitude:</span>
                                        <span class="text-sm font-medium ml-2">{{ $viewingVenue->latitude }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Longitude:</span>
                                        <span class="text-sm font-medium ml-2">{{ $viewingVenue->longitude }}</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="https://maps.google.com/?q={{ $viewingVenue->latitude }},{{ $viewingVenue->longitude }}" 
                                       target="_blank" 
                                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        View on Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if(!empty($viewingVenue->gallery) && count($viewingVenue->gallery) > 1)
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3">Gallery ({{ count($viewingVenue->gallery) }} images)</h4>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($viewingVenue->gallery as $image)
                                        <img src="{{ asset('storage/' . $image) }}" class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <button 
                        wire:click="openEditModal({{ $viewingVenue->id }})"
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors"
                    >
                        <i class="fas fa-edit mr-2"></i>
                        Edit Venue
                    </button>
                    <button 
                        wire:click="resetModal"
                        class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function confirmDelete(venueId, venueName) {
    if (confirm(`Are you sure you want to delete "${venueName}"? This action cannot be undone.`)) {
        @this.call('deleteVenue', venueId);
    }
}

// Geolocation functionality for venue creation
document.addEventListener('livewire:initialized', function() {
    let isGettingLocation = false;

    Livewire.on('getCurrentLocation', function() {
        // Prevent multiple simultaneous location requests
        if (isGettingLocation) {
            return;
        }

        if (navigator.geolocation) {
            isGettingLocation = true;
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Reverse geocoding to get location name (optional)
                    fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`)
                        .then(response => response.json())
                        .then(data => {
                            const locationName = data.city || data.locality || data.countryName || 'Unknown Location';
                            Livewire.dispatch('locationSelected', {
                                latitude: lat,
                                longitude: lng,
                                locationName: locationName
                            });
                        })
                        .catch(() => {
                            Livewire.dispatch('locationSelected', {
                                latitude: lat,
                                longitude: lng,
                                locationName: null
                            });
                        })
                        .finally(() => {
                            isGettingLocation = false;
                        });
                },
                function(error) {
                    isGettingLocation = false;
                    // Reset the UI state on error
                    Livewire.dispatch('locationError');
                    alert('Error getting location: ' + error.message + '. Please enter coordinates manually or try again.');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        } else {
            alert('Geolocation is not supported by this browser. Please enter coordinates manually.');
            Livewire.dispatch('locationError');
        }
    });
});
</script>


</div>